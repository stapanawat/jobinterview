<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-title">แดชบอร์ด</h1>
            <p class="page-subtitle">ภาพรวมระบบสมัครงานและสัมภาษณ์</p>
        </div>
    </x-slot>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">ผู้สมัครทั้งหมด</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="stat-card-icon gradient-brand">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">รอตรวจสอบ</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="stat-card-icon gradient-warning">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Scheduled --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">นัดสัมภาษณ์แล้ว</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['scheduled'] }}</p>
                </div>
                <div class="stat-card-icon gradient-info">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        {{-- Confirmed --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">ยืนยันแล้ว</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['confirmed'] }}</p>
                </div>
                <div class="stat-card-icon gradient-success">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Applicants Table --}}
    <div class="card">
        <div class="card-header flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">รายชื่อผู้สมัคร</h2>
                <p class="text-sm text-gray-500 mt-0.5">จัดการข้อมูลผู้สมัครงานทั้งหมด</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ชื่อ</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ตำแหน่ง</th>
                        <th>สถานะ</th>
                        <th class="text-center">ยืนยันล่วงหน้า</th>
                        <th>วันที่สมัคร</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applicants as $applicant)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($applicant->name ?: '?', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $applicant->name ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="text-gray-600">{{ $applicant->phone ?: '-' }}</td>
                            <td class="text-gray-600">{{ $applicant->position ?: '-' }}</td>
                            <td>
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
                                    @case('cancelled')
                                        <span class="badge badge-gray" style="background: #fee2e2; color: #dc2626;">ยกเลิก</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ $applicant->status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                @php
                                    $interview = $applicant->interviews->first();
                                @endphp
                                @if($interview && $interview->day_before_confirmed)
                                    <span title="ยืนยันมาแน่นอน" style="font-size: 18px;">✅</span>
                                @elseif($interview && $interview->day_before_reminder_sent)
                                    <span title="ส่งแจ้งเตือนแล้ว รอยืนยัน" style="font-size: 18px;">⏳</span>
                                @else
                                    <span title="ยังไม่ถึงเวลาแจ้งเตือน" style="color: #ccc;">➖</span>
                                @endif
                            </td>
                            <td class="text-gray-500 text-xs">{{ $applicant->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('interviews.create', ['applicant' => $applicant->id]) }}"
                                       class="btn btn-primary btn-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        นัดสัมภาษณ์
                                    </a>
                                    <a href="{{ route('reviews.create', ['applicant' => $applicant->id]) }}"
                                       class="btn btn-success btn-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        เขียนรีวิว
                                    </a>
                                    <a href="{{ route('reviews.index', ['applicant_id' => $applicant->id]) }}"
                                       class="btn btn-secondary btn-sm">
                                        ดูรีวิว
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($applicants->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-gray-400 font-medium">ไม่มีข้อมูลผู้สมัคร</p>
                                <p class="text-gray-400 text-xs mt-1">ผู้สมัครจะปรากฏเมื่อมีการสมัครผ่าน LINE</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>