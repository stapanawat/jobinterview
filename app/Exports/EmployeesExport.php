<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Storage;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all employees (working or terminated)
        return Applicant::whereIn('status', ['working', 'terminated'])->orderBy('updated_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'ชื่อ-นามสกุล',
            'เบอร์โทรศัพท์',
            'ตำแหน่งที่สมัคร',
            'ตำแหน่ง/งานที่ทำล่าสุด (Job Description)',
            'สถานะพนักงาน',
            'ที่พักปัจจุบัน',
            'ปัจจุบันทำอะไร',
            'อายุ',
            'ระดับการศึกษา',
            'จำนวนบุตร',
            'ขับขี่รถจักรยานยนต์',
            'สามารถทำงาน/วันหยุด',
            'บุคคลติดต่อฉุกเฉิน',
            'ประวัติประสบการณ์',
            'ข้อดี-ข้อเสีย',
            'ความฝันในชีวิต',
            'URL รูปถ่าย',
            'URL บัตรประชาชน',
            'URL รูปโปรไฟล์ LINE',
            'วันที่สมัคร',
            'อัปเดตล่าสุด'
        ];
    }

    public function map($applicant): array
    {
        // Generate full URLs for the documents
        $photoUrl = $applicant->photo ? asset('storage/' . $applicant->photo) : '';
        $idCardUrl = $applicant->id_card_image ? asset('storage/' . $applicant->id_card_image) : '';
        $linePictureUrl = $applicant->line_picture_url ?? '';

        $statusLabel = 'ทำงานอยู่';
        if ($applicant->status === 'terminated') {
            $statusLabel = 'เลิกจ้างแล้ว';
        }

        return [
            $applicant->id,
            $applicant->name,
            $applicant->phone,
            $applicant->position,
            $applicant->job_description,
            $statusLabel,
            $applicant->current_residence,
            $applicant->current_occupation,
            $applicant->age,
            $applicant->education_level,
            $applicant->number_of_children,
            $applicant->can_drive_motorcycle,
            $applicant->preferred_working_hours,
            $applicant->emergency_contact,
            $applicant->experience,
            $applicant->pros_and_cons,
            $applicant->life_dream,
            $photoUrl,
            $idCardUrl,
            $linePictureUrl,
            $applicant->created_at ? $applicant->created_at->format('Y-m-d H:i') : '',
            $applicant->updated_at ? $applicant->updated_at->format('Y-m-d H:i') : '',
        ];
    }
}
