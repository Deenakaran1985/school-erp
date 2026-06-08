<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('student.create');
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:100'],
            'father_name'       => ['required', 'string', 'max:100'],
            'mother_name'       => ['nullable', 'string', 'max:100'],
            'date_of_birth'     => ['required', 'date', 'before:today'],
            'gender'            => ['required', 'in:M,F,O'],
            'school_class_id'   => ['required', 'exists:school_classes,id'],
            'section_id'        => ['nullable', 'exists:sections,id'],
            'academic_year_id'  => ['required', 'exists:academic_years,id'],
            'parent_mobile'     => ['required', 'digits:10'],
            'alt_mobile'        => ['nullable', 'digits:10'],
            'emis_number'       => ['nullable', 'string', 'max:20', 'unique:students,emis_number'],
            'aadhar_number'     => ['nullable', 'digits:12'],
            'community'         => ['nullable', 'in:OC,BC,MBC,SC,ST'],
            'religion'          => ['nullable', 'string', 'max:30'],
            'blood_group'       => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'address'           => ['nullable', 'string'],
            'pincode'           => ['nullable', 'digits:6'],
            'roll_number'       => ['nullable', 'integer', 'min:1'],
            'uses_transport'    => ['boolean'],
            'photo'             => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_mobile.digits'   => 'Parent mobile must be exactly 10 digits.',
            'aadhar_number.digits'   => 'Aadhar number must be exactly 12 digits.',
            'emis_number.unique'     => 'This EMIS number is already registered.',
            'date_of_birth.before'   => 'Date of birth must be in the past.',
        ];
    }
}