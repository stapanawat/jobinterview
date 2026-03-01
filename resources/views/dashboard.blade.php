<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-title">แดชบอร์ด</h1>
            <p class="page-subtitle">ภาพรวมระบบสมัครงานและสัมภาษณ์</p>
        </div>
    </x-slot>

    {{-- Styled Alerts --}}
    <style>
        .pks-alert {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            animation: alertSlideIn 0.4s ease-out;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .pks-alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 14px 0 0 14px;
        }
        .pks-alert-success {
            background: linear-gradient(135deg, #E8F5E9, #F1F8E9);
            border: 1px solid #A5D6A7;
            color: #1B5E20;
        }
        .pks-alert-success::before {
            background: linear-gradient(180deg, #2E7D32, #1B5E20);
        }
        .pks-alert-error {
            background: linear-gradient(135deg, #FFEBEE, #FFF3E0);
            border: 1px solid #EF9A9A;
            color: #B71C1C;
        }
        .pks-alert-error::before {
            background: linear-gradient(180deg, #E53935, #B71C1C);
        }
        .pks-alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pks-alert-success .pks-alert-icon {
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            color: white;
        }
        .pks-alert-error .pks-alert-icon {
            background: linear-gradient(135deg, #E53935, #B71C1C);
            color: white;
        }
        .pks-alert-text {
            flex: 1;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
        }
        .pks-alert-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            color: inherit;
            opacity: 0.5;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .pks-alert-close:hover {
            opacity: 1;
            background: rgba(0,0,0,0.06);
        }
        @keyframes alertSlideIn {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes alertFadeOut {
            from { opacity: 1; transform: translateY(0); max-height: 100px; margin-bottom: 24px; }
            to { opacity: 0; transform: translateY(-8px); max-height: 0; margin-bottom: 0; padding: 0; border: 0; }
        }
    </style>

    @if(session('success'))
        <div class="pks-alert pks-alert-success" id="alertSuccess">
            <div class="pks-alert-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="pks-alert-text">{{ session('success') }}</span>
            <button class="pks-alert-close" onclick="this.parentElement.style.animation='alertFadeOut 0.3s ease-out forwards';">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="pks-alert pks-alert-error" id="alertError">
            <div class="pks-alert-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <span class="pks-alert-text">{{ session('error') }}</span>
            <button class="pks-alert-close" onclick="this.parentElement.style.animation='alertFadeOut 0.3s ease-out forwards';">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
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

        {{-- Time Confirmed --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">ยืนยันเวลานัด</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $stats['time_confirmed'] }}</p>
                </div>
                <div class="stat-card-icon" style="background: linear-gradient(135deg, #a855f7, #7e22ce); color: white;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Confirmed --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">ยืนยันเข้าร่วม</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $stats['attendance_confirmed'] }}</p>
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
                                    @case('time_confirmed')
                                        <span class="badge badge-purple" style="background: #f3e8ff; color: #7e22ce;">ยืนยันเวลานัด</span>
                                        @break
                                    @case('attendance_confirmed')
                                        <span class="badge badge-green">ยืนยันเข้าร่วม</span>
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
                                <div class="flex flex-wrap items-center justify-center gap-2 max-w-[200px] mx-auto">
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
                                    @if(in_array($applicant->status, ['scheduled', 'time_confirmed', 'attendance_confirmed', 'reschedule_requested']))
                                        <form action="{{ route('interviews.cancel', ['applicant' => $applicant->id]) }}" method="POST" class="inline w-full mt-1" onsubmit="return confirm('คุณต้องการยกเลิกการนัดสัมภาษณ์ผู้สมัครรายนี้ใช่หรือไม่?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm w-full flex justify-center items-center" style="background-color: #ef4444; color: white;">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                ยกเลิกนัดสัมภาษณ์
                                            </button>
                                        </form>
                                    @endif
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