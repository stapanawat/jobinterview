<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-brand-600 hover:border-brand-200 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">ประวัติการสมัคร</h1>
                <p class="page-subtitle">แสดงใบสมัครทั้งหมดของ: <span class="font-bold text-gray-900">{{ $applicant->name }}</span> ({{ $applicant->phone }})</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @foreach($history as $record)
            <div class="card overflow-hidden transition-all hover:shadow-md border-l-4 {{ 
                $record->status === 'working' ? 'border-l-blue-500' : (
                $record->status === 'terminated' || $record->status === 'cancelled' ? 'border-l-red-500' : 'border-l-amber-500'
            )}}">
                <div class="card-header bg-gray-50/50 flex flex-wrap justify-between items-center gap-4 py-4">
                    <div class="flex items-center gap-4">
                        <div class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-600 shadow-sm">
                            สมัครเมื่อ: {{ $record->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-500">ตำแหน่ง:</span>
                            <span class="px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-sm font-bold border border-brand-100 italic">
                                {{ $record->position }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg
                            @switch($record->status)
                                @case('pending_review') bg-amber-100 text-amber-800 @break
                                @case('scheduled') bg-blue-100 text-blue-800 @break
                                @case('time_confirmed') bg-purple-100 text-purple-800 @break
                                @case('attendance_confirmed') bg-emerald-100 text-emerald-800 @break
                                @case('working') bg-blue-100 text-blue-800 @break
                                @case('terminated') bg-red-100 text-red-800 @break
                                @case('cancelled') bg-red-100 text-red-700 @break
                                @default bg-gray-100 text-gray-700 @break
                            @endswitch">
                            {{ 
                                $record->status === 'pending_review' ? 'รอตรวจสอบ' : (
                                $record->status === 'scheduled' ? 'นัดหมายแล้ว' : (
                                $record->status === 'time_confirmed' ? 'ยืนยันเวลานัด' : (
                                $record->status === 'attendance_confirmed' ? 'ยืนยันเข้าร่วม' : (
                                $record->status === 'working' ? 'ทำงานอยู่' : (
                                $record->status === 'terminated' ? 'เลิกจ้างแล้ว' : (
                                $record->status === 'cancelled' ? 'ยกเลิก' : $record->status
                            )))))) }}
                        </span>
                        <button onclick="toggleDetails('record-{{ $record->id }}')" class="p-2 text-gray-400 hover:text-gray-600 transition-all">
                            <svg id="icon-record-{{ $record->id }}" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                <div id="record-{{ $record->id }}" class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fadeIn">
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            ข้อมูลส่วนตัว
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">ชื่อ-นามสกุล</p><p class="text-sm font-semibold text-gray-900">{{ $record->name }}</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">อายุ</p><p class="text-sm font-semibold text-gray-900">{{ $record->age ?? '-' }} ปี</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">การศึกษา</p><p class="text-sm font-semibold text-gray-900">{{ $record->education_level ?? '-' }}</p></div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            รายละเอียดงาน
                        </h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">ตำแหน่ง</p><p class="text-sm font-semibold text-gray-900">{{ $record->position }}</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">ประสบการณ์</p><p class="text-sm text-gray-900">{{ $record->experience ?? '-' }}</p></div>
                            <div><p class="text-[10px] text-gray-400 font-bold uppercase">ปัจจุบันทำอะไร</p><p class="text-sm text-gray-900">{{ $record->current_occupation ?? '-' }}</p></div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            หมายเหตุการสัมภาษณ์
                        </h4>
                        <p class="text-sm text-gray-600 bg-gray-50 border border-gray-100 rounded-xl p-3 h-full whitespace-pre-wrap italic">
                            {{ $record->notes ?: 'ไม่มีบันทึกข้อมูล' }}
                        </p>
                    </div>

                    <div class="col-span-1 md:col-span-2 lg:col-span-3 pt-4 border-t border-gray-100 flex flex-wrap gap-4">
                        <a href="{{ route('interviews.create', ['applicant' => $record->id]) }}" class="btn btn-primary btn-sm rounded-lg flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            นัดหมายจากใบสมัครนี้
                        </a>
                        <a href="{{ route('reviews.index', ['applicant_id' => $record->id]) }}" class="btn btn-secondary btn-sm rounded-lg flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            ดูรีวิว
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function toggleDetails(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            el.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-app-layout>
