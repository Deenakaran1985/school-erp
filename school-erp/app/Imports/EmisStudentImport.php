<?php
namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class EmisStudentImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use Importable;

    public int   $imported = 0;
    public int   $skipped  = 0;
    public int   $updated  = 0;
    public array $errors   = [];

    public function __construct(
        private readonly int    $yearId,
        private readonly int    $classId   = 0,
        private readonly int    $sectionId = 0,
        private readonly string $dupMode   = 'skip',
    ) {}

    public function model(array $row): ?Student
    {
        // ── Normalize column keys (EMIS uses inconsistent headers) ──
        $emisNo     = trim($row['emis_no']     ?? $row['emis_number'] ?? $row['emisno'] ?? '');
        $name       = trim($row['stu_name']   ?? $row['student_name'] ?? $row['name'] ?? '');
        $fatherName = trim($row['father_name'] ?? $row['fathername'] ?? '');
        $dob        = trim($row['dob']         ?? $row['date_of_birth'] ?? '');
        $gender     = strtoupper(substr(trim($row['gender'] ?? 'M'), 0, 1));
        $mobile     = trim($row['mobile_no']  ?? $row['mobile'] ?? $row['parent_mobile'] ?? '');
        $community  = strtoupper(trim($row['community'] ?? ''));
        $aadhar     = preg_replace('/\D/', '', $row['aadhar_no'] ?? $row['aadhar'] ?? '');
        $className  = trim($row['class'] ?? $row['std'] ?? $row['standard'] ?? '');
        $section    = strtoupper(trim($row['section'] ?? 'A'));
        $medium     = trim($row['medium'] ?? 'Tamil');

        if (empty($emisNo) || empty($name)) return null;

        // ── Check for existing student ──────────────────────────
        $existing = Student::where('emis_number', $emisNo)->first();

        if ($existing) {
            if ($this->dupMode === 'skip') {
                $this->skipped++;
                return null;
            }
            // update mode
            $existing->update([
                'name'        => $name,
                'father_name' => $fatherName,
                'community'   => $community ?: null,
                'parent_mobile' => $mobile,
            ]);
            $this->updated++;
            return null;
        }

        // ── Resolve class & section ─────────────────────────────
        $classId   = $this->classId;
        $sectionId = $this->sectionId;

        if (!$classId && !empty($className)) {
            $cls = SchoolClass::where('academic_year_id', $this->yearId)
                ->where(function($q) use ($className) {
                    $q->where('name', $className)
                      ->orWhere('display_name', 'like', "%{$className}%");
                })->first();
            $classId   = $cls?->id ?? 0;
            $sectionId = $cls
                ? Section::where('school_class_id', $classId)->where('name', $section)->value('id')
                : null;
        }

        if (!$classId) {
            $this->errors[] = ['emis' => $emisNo, 'name' => $name, 'error' => 'Class not found'];
            return null;
        }

        // ── Parse DOB ───────────────────────────────────────────
        try {
            $parsedDob = Carbon::parse($dob)->format('Y-m-d');
        } catch (\Exception) {
            $this->errors[] = ['emis' => $emisNo, 'name' => $name, 'error' => 'Invalid DOB: ' . $dob];
            return null;
        }

        // ── Create parent user ──────────────────────────────────
        $parentUser = User::firstOrCreate(
            ['email' => 'parent_' . $emisNo . '@school.local'],
            [
                'name'      => $fatherName . ' (Parent)',
                'password'  => Hash::make($mobile ?: $emisNo),
                'phone'     => $mobile,
                'user_type' => 'parent',
                'status'    => 'active',
            ]
        );
        if (!$parentUser->hasRole('parent')) $parentUser->assignRole('parent');

        // ── Create student user ─────────────────────────────────
        $studentUser = User::create([
            'name'      => $name,
            'email'     => 'stu_' . $emisNo . '@school.local',
            'password'  => Hash::make($mobile ?: $emisNo),
            'user_type' => 'student',
            'status'    => 'active',
        ]);
        $studentUser->assignRole('student');

        $this->imported++;

        return new Student([
            'user_id'          => $studentUser->id,
            'parent_user_id'   => $parentUser->id,
            'academic_year_id' => $this->yearId,
            'school_class_id'  => $classId,
            'section_id'       => $sectionId,
            'emis_number'      => $emisNo,
            'admission_no'     => 'ADM-' . $emisNo,
            'name'             => $name,
            'father_name'      => $fatherName,
            'mother_name'      => trim($row['mother_name'] ?? '') ?: null,
            'date_of_birth'    => $parsedDob,
            'gender'           => in_array($gender, ['M','F','O']) ? $gender : 'M',
            'community'        => in_array($community, ['OC','BC','MBC','SC','ST']) ? $community : null,
            'aadhar_number'    => strlen($aadhar) === 12 ? $aadhar : null,
            'parent_mobile'    => $mobile,
            'medium'           => $medium,
            'admission_date'   => now(),
            'status'           => 'active',
        ]);
    }

    public function rules(): array
    {
        return [];  // validation handled inline in model() for better error messages
    }

    public function onError(Throwable $e): void
    {
        $this->errors[] = ['emis' => '?', 'name' => '?', 'error' => $e->getMessage()];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $f) {
            $this->errors[] = [
                'emis'  => 'Row ' . $f->row(),
                'name'  => implode(', ', $f->attribute()),
                'error' => implode(' ', $f->errors()),
            ];
        }
    }

    public function batchSize(): int     { return 100; }
    public function chunkSize(): int     { return 200; }
}