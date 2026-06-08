<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('staff.view');

        $departments = Department::all();

        $staff = Staff::with(['user', 'department'])
            ->when($request->search, fn($q, $v) =>
                $q->where(function ($q) use ($v) {
                    $q->where('name', 'like', "%{$v}%")
                      ->orWhere('employee_id', 'like', "%{$v}%")
                      ->orWhereHas('user', fn($q) => $q->where('phone', 'like', "%{$v}%"));
                })
            )
            ->when($request->department_id, fn($q, $v) => $q->where('department_id', $v))
            ->when($request->staff_type,    fn($q, $v) => $q->where('staff_type', $v))
            ->when($request->status,        fn($q, $v) => $q->where('status', $v))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('staff.index', compact('staff', 'departments'));
    }

    public function create()
    {
        $this->authorize('staff.create');
        $departments = Department::all();
        return view('staff.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('staff.create');

        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'gender'          => 'required|in:M,F,O',
            'date_of_birth'   => 'required|date|before:today',
            'phone'           => 'required|digits:10|unique:users,phone',
            'department_id'   => 'required|exists:departments,id',
            'designation'     => 'required|string|max:100',
            'staff_type'      => 'required|in:teaching,non_teaching,admin',
            'joining_date'    => 'required|date',
            'qualification'   => 'nullable|string|max:150',
            'aadhar_number'   => 'nullable|string|max:16',
            'pan_number'      => 'nullable|string|max:10',
            'basic_salary'    => 'required|numeric|min:0',
            'da_percent'      => 'nullable|numeric|min:0|max:100',
            'hra_percent'     => 'nullable|numeric|min:0|max:100',
            'other_allowance' => 'nullable|numeric|min:0',
            'pf_percent'      => 'nullable|numeric|min:0|max:100',
            'bank_account'    => 'nullable|string|max:20',
            'bank_name'       => 'nullable|string|max:100',
            'bank_ifsc'       => 'nullable|string|max:15',
            'photo'           => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $phone = $validated['phone'];

            $user = User::create([
                'name'      => $validated['name'],
                'email'     => 'staff_' . $phone . '@school.local',
                'phone'     => $phone,
                'password'  => Hash::make($phone),
                'user_type' => 'staff',
                'status'    => 'active',
            ]);
            $user->assignRole('teacher');

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('staff-photos', 'public');
            }

            $lastId     = Staff::max('id') + 1;
            $employeeId = 'EMP-' . now()->year . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);

            Staff::create(array_merge(
                collect($validated)->except('phone')->toArray(),
                [
                    'user_id'     => $user->id,
                    'employee_id' => $employeeId,
                    'photo'       => $photoPath,
                    'status'      => 'active',
                    'da_percent'      => $validated['da_percent']      ?? 0,
                    'hra_percent'     => $validated['hra_percent']     ?? 0,
                    'other_allowance' => $validated['other_allowance'] ?? 0,
                    'pf_percent'      => $validated['pf_percent']      ?? 0,
                ]
            ));
        });

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    public function show(Staff $staff)
    {
        $this->authorize('staff.view');
        $staff->load(['user', 'department', 'payrolls' => fn($q) => $q->latest('month_year')->limit(6)]);
        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        $this->authorize('staff.edit');
        $departments = Department::all();
        return view('staff.edit', compact('staff', 'departments'));
    }

    public function update(Request $request, Staff $staff)
    {
        $this->authorize('staff.edit');

        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'gender'          => 'required|in:M,F,O',
            'date_of_birth'   => 'required|date|before:today',
            'department_id'   => 'required|exists:departments,id',
            'designation'     => 'required|string|max:100',
            'staff_type'      => 'required|in:teaching,non_teaching,admin',
            'qualification'   => 'nullable|string|max:150',
            'aadhar_number'   => 'nullable|string|max:16',
            'pan_number'      => 'nullable|string|max:10',
            'basic_salary'    => 'required|numeric|min:0',
            'da_percent'      => 'nullable|numeric|min:0|max:100',
            'hra_percent'     => 'nullable|numeric|min:0|max:100',
            'other_allowance' => 'nullable|numeric|min:0',
            'pf_percent'      => 'nullable|numeric|min:0|max:100',
            'bank_account'    => 'nullable|string|max:20',
            'bank_name'       => 'nullable|string|max:100',
            'bank_ifsc'       => 'nullable|string|max:15',
            'status'          => 'required|in:active,inactive,resigned,terminated',
            'photo'           => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($staff->photo) Storage::disk('public')->delete($staff->photo);
            $validated['photo'] = $request->file('photo')->store('staff-photos', 'public');
        }

        $staff->update($validated);
        $staff->user->update(['name' => $validated['name']]);

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $this->authorize('staff.delete');
        $staff->update(['status' => 'inactive']);
        $staff->delete();
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff removed.');
    }
}
