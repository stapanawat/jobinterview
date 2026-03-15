<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title text-2xl font-bold text-gray-900">ประวัติรีวิวและผลประเมิน</h1>
                <p class="page-subtitle text-sm text-gray-500">จัดการและตรวจสอบรีวิวทั้งหมดในระบบ</p>
            </div>
            @if(isset($applicant))
                <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-xl text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    รีวิวของ: {{ $applicant->name ?: '-' }}
                    <a href="{{ route('reviews.index') }}" class="ml-2 text-blue-400 hover:text-blue-600" title="ดูทั้งหมด">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm animate-fade-in">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-8 shadow-sm border-0 bg-white overflow-hidden rounded-2xl">
        <div class="p-6">
            <form method="GET" action="{{ route('reviews.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                @if(request('applicant_id'))
                    <input type="hidden" name="applicant_id" value="{{ request('applicant_id') }}">
                @endif
                
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">ตำแหน่งงาน</label>
                    <select name="position" class="form-select w-full rounded-xl border-gray-200 focus:ring-brand-500 focus:border-brand-500 text-sm">
                        <option value="">ทุกตำแหน่ง</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->name }}" {{ request('position') === $pos->name ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">ประเภทรีวิว</label>
                    <select name="reviewer_type" class="form-select w-full rounded-xl border-gray-200 focus:ring-brand-500 focus:border-brand-500 text-sm">
                        <option value="">ทั้งหมด</option>
                        <option value="shop" {{ request('reviewer_type') === 'shop' ? 'selected' : '' }}>ผลประเมินพนักงาน</option>
                        <option value="employee" {{ request('reviewer_type') === 'employee' ? 'selected' : '' }}>รีวิวงาน/ร้านค้า</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">ช่วงเวลา</label>
                    <select name="date_filter" class="form-select w-full rounded-xl border-gray-200 focus:ring-brand-500 focus:border-brand-500 text-sm">
                        <option value="">ล่าสุดทั้งหมด</option>
                        <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>วันนี้</option>
                        <option value="yesterday" {{ request('date_filter') === 'yesterday' ? 'selected' : '' }}>เมื่อวาน</option>
                        <option value="this_week" {{ request('date_filter') === 'this_week' ? 'selected' : '' }}>สัปดาห์นี้</option>
                        <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>เดือนนี้</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider ml-1">ค้นหาชื่อ</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ชื่อพนักงาน..." class="form-input pl-10 w-full rounded-xl border-gray-200 focus:ring-brand-500 focus:border-brand-500 text-sm">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        ค้นหา
                    </button>
                    <a href="{{ route('reviews.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-xl transition-all shadow-sm" title="ล้างค่า">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Reviews Table Card --}}
    <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">พนักงาน / ตำแหน่ง</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ประเภท</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">คะแนน</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">ความคิดเห็น</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">วันที่</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center text-sm font-bold border border-brand-100">
                                        {{ strtoupper(substr($review->applicant->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $review->applicant->name ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">{{ $review->applicant->position ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($review->reviewer_type === 'shop')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">ผลประเมินพนักงาน</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-100">รีวิวงาน/ร้าน</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-sm font-bold text-gray-700 ml-1">{{ number_format($review->rating, 1) }}</span>
                                    </div>
                                    @if($review->reviewer_type === 'shop' && $review->rating_punctuality !== null)
                                        <button type="button" onclick="toggleDetails('details-{{ $review->id }}')" class="text-[10px] text-brand-600 hover:text-brand-800 font-bold flex items-center gap-0.5 uppercase tracking-wide transition-colors">
                                            รายละเอียดคะแนน <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-xs line-clamp-2">{{ $review->comment ?: '-' }}</div>
                                @if($review->reviewer_type === 'shop' && $review->rating_punctuality !== null)
                                    <div id="details-{{ $review->id }}" class="hidden mt-3 p-3 bg-gray-50 rounded-xl border border-gray-100 animate-slide-down">
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                                            @php
                                                $criteria = [
                                                    'ตรงเวลา' => $review->rating_punctuality,
                                                    'เข้าร่วม' => $review->rating_showed_up,
                                                    'ซื่อสัตย์' => $review->rating_honesty,
                                                    'ขยัน' => $review->rating_diligence,
                                                    'ทำตามคำสั่ง' => $review->rating_following_instructions,
                                                    'อื่นๆ' => $review->rating_others,
                                                ];
                                            @endphp
                                            @foreach($criteria as $label => $score)
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-gray-400">{{ $label }}</span>
                                                    <span class="font-bold text-gray-600">{{ $score }}★</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-medium text-gray-900">{{ $review->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-gray-400 font-medium">{{ $review->created_at->format('H:i') }} น.</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </div>
                                    <div class="text-gray-400 font-semibold">ไม่พบข้อมูลรีวิว</div>
                                    <p class="text-xs text-gray-300">ลองล้างตัวกรองหรือใช้คำค้นหาอื่นแทนครับ</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="px-6 py-5 bg-gray-50/50 border-t border-gray-100">
                {{ $reviews->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .animate-slide-down { animation: slideDown 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>

    <script>
        function toggleDetails(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>
</x-app-layout>