<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-title">นัดหมายสัมภาษณ์</h1>
            <p class="page-subtitle">กำหนดเวลาสัมภาษณ์สำหรับ {{ $applicant->name }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        {{-- Applicant Info Card --}}
        <div class="card mb-6">
            <div class="card-body flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    {{ strtoupper(substr($applicant->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">{{ $applicant->name }}</h3>
                    <p class="text-sm text-gray-500">📞 {{ $applicant->phone }} · 💼 {{ $applicant->position }}</p>
                </div>
                <div class="ml-auto">
                    @switch($applicant->status)
                        @case('pending_review')
                            <span class="badge badge-yellow">รอตรวจสอบ</span>
                            @break
                        @case('scheduled')
                            <span class="badge badge-blue">นัดสัมภาษณ์แล้ว</span>
                            @break
                        @case('confirmed')
                            <span class="badge badge-green">ยืนยันแล้ว</span>
                            @break
                        @default
                            <span class="badge badge-gray">{{ $applicant->status }}</span>
                    @endswitch
                </div>
            </div>
        </div>

        {{-- Interview Form --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">รายละเอียดการสัมภาษณ์</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('interviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        {{-- Date --}}
                        <div>
                            <label for="interview_date" class="form-label">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    วันที่สัมภาษณ์
                                </span>
                            </label>
                            <input type="date" name="interview_date" id="interview_date" class="form-input" required>
                        </div>

                        {{-- Time --}}
                        <div>
                            <label for="interview_time" class="form-label">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    เวลา
                                </span>
                            </label>
                            <input type="time" name="interview_time" id="interview_time" class="form-input" required>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="mb-6">
                        <label for="location" class="form-label">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                สถานที่ / ลิงก์ออนไลน์
                            </span>
                        </label>
                        <textarea name="location" id="location" rows="3" class="form-input"
                            placeholder="เช่น ห้องประชุม ชั้น 3 หรือ https://meet.google.com/..." required></textarea>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            ยืนยันการนัดหมาย
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>