{{-- Stats Part — This will be sent as JSON or just updated via a different method if needed, but for now let's focus on the table --}}
@foreach ($applicants as $applicant)
    <tr class="fade-in">
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
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="text-gray-400 font-medium">ไม่มีข้อมูลผู้สมัคร</p>
            <p class="text-gray-400 text-xs mt-1">ผู้สมัครจะปรากฏเมื่อมีการสมัครผ่าน LINE</p>
        </td>
    </tr>
@endif
