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
            <select onchange="handleStatusChange(this, {{ $applicant->id }}, '{{ $applicant->job_description }}')"
                class="status-dropdown text-xs font-semibold rounded-lg px-2.5 py-1.5 pr-6 border-0 cursor-pointer focus:ring-2 focus:ring-brand-300 transition-all
                @switch($applicant->status)
                    @case('pending_review') bg-amber-100 text-amber-800 @break
                    @case('scheduled') bg-blue-100 text-blue-800 @break
                    @case('time_confirmed') bg-purple-100 text-purple-800 @break
                    @case('attendance_confirmed') bg-emerald-100 text-emerald-800 @break
                    @case('working') bg-blue-100 text-blue-800 @break
                    @case('terminated') bg-red-100 text-red-800 @break
                    @case('cancelled') bg-red-100 text-red-700 @break
                    @default bg-gray-100 text-gray-700 @break
                @endswitch"
                style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22%236b7280%22%3E%3Cpath fill-rule=%22evenodd%22 d=%22M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z%22 clip-rule=%22evenodd%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.4rem center; background-size: 1em;">
                <option value="pending_review" {{ $applicant->status == 'pending_review' ? 'selected' : '' }}>รอตรวจสอบ</option>
                <option value="scheduled" {{ $applicant->status == 'scheduled' ? 'selected' : '' }}>นัดหมายแล้ว</option>
                <option value="time_confirmed" {{ $applicant->status == 'time_confirmed' ? 'selected' : '' }}>ยืนยันเวลานัด</option>
                <option value="attendance_confirmed" {{ $applicant->status == 'attendance_confirmed' ? 'selected' : '' }}>ยืนยันเข้าร่วม</option>
                <option value="working" {{ $applicant->status == 'working' ? 'selected' : '' }}>✓ ผ่าน → เริ่มทำงาน</option>
                <option value="cancelled" {{ $applicant->status == 'cancelled' ? 'selected' : '' }}>✗ ยกเลิก</option>
            </select>
        </td>
        <td class="text-gray-600 text-xs">
            @if($applicant->status === 'working' && $applicant->job_description)
                <span class="inline-flex items-center gap-1" title="{{ $applicant->job_description }}">
                    <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="truncate max-w-[120px]">{{ $applicant->job_description }}</span>
                </span>
            @else
                <span class="text-gray-300">-</span>
            @endif
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
                    นัดหมาย
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
                    <form action="{{ route('interviews.cancel', ['applicant' => $applicant->id]) }}" method="POST" class="inline w-full mt-1" onsubmit="return confirm('คุณต้องการยกเลิกการนัดหมายผู้สมัครรายนี้ใช่หรือไม่?');">
                        @csrf
                        <button type="submit" class="btn btn-sm w-full flex justify-center items-center" style="background-color: #ef4444; color: white;">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            ยกเลิกนัดหมาย
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@endforeach

@if($applicants->isEmpty())
    <tr>
        <td colspan="8" class="text-center py-12">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="text-gray-400 font-medium">ไม่มีข้อมูลผู้สมัคร</p>
            <p class="text-gray-400 text-xs mt-1">ผู้สมัครจะปรากฏเมื่อมีการสมัครผ่าน LINE</p>
        </td>
    </tr>
@endif
