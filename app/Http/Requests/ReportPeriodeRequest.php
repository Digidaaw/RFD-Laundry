<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportPeriodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'export' => 'nullable|in:pdf,excel',
        ];
    }

    public function messages()
    {
        return [
            'start_date.date' => 'Tanggal mulai harus valid.',
            'end_date.date' => 'Tanggal akhir harus valid.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
            'export.in' => 'Tipe export tidak valid.',
        ];
    }
}
