<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-title">ประวัติรีวิวทั้งหมด</h1>
            <p class="page-subtitle">รีวิวจากร้านค้าและพนักงาน — แบ่งตามตำแหน่ง</p>
            @if(isset($applicant))
                <div class="mt-3 inline-flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-xl text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    กำลังดูรีวิวของพนักงาน: {{ $applicant->name ?: '-' }}
                    <a href="{{ route('reviews.index') }}" class="ml-2 text-blue-400 hover:text-blue-600 bg-white p-1 rounded-lg shadow-sm border border-blue-100" title="ดูรีวิวทั้งหมด">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    {{-- Success Alert --}}
    @if(session('success'))
        <div
            class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('reviews.index') }}" class="flex flex-wrap items-end gap-4">
                @if(request('applicant_id'))
                    <input type="hidden" name="applicant_id" value="{{ request('applicant_id') }}">
                @endif
                <div class="flex-1 min-w-[180px]">
                    <label class="form-label">ตำแหน่ง</label>
                    <select name="position" class="form-select w-full">
                        <option value="">ทุกตำแหน่ง</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->name }}" {{ request('position') === $pos->name ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[180px]">
                    <label class="form-label">ประเภทผู้รีวิว</label>
                    <select name="reviewer_type" class="form-select w-full">
                        <option value="">ทั้งหมด</option>
                        <option value="shop" {{ request('reviewer_type') === 'shop' ? 'selected' : '' }}>ร้านค้า → พนักงาน
                        </option>
                        <option value="employee" {{ request('reviewer_type') === 'employee' ? 'selected' : '' }}>พนักงาน →
                            ร้านค้า</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="form-label">ช่วงเวลา</label>
                    <select name="date_filter" class="form-select w-full">
                        <option value="">ทั้งหมด (ล่าสุด)</option>
                        <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>วันนี้</option>
                        <option value="yesterday" {{ request('date_filter') === 'yesterday' ? 'selected' : '' }}>เมื่อวาน</option>
                        <option value="this_week" {{ request('date_filter') === 'this_week' ? 'selected' : '' }}>สัปดาห์นี้</option>
                        <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>เดือนนี้</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[180px]">
                    <label class="form-label">ค้นหาพนักงาน</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="ระบุชื่อพนักงาน..." class="form-input pl-10 w-full">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        กรอง
                    </button>
                    <a href="{{ route('reviews.index', request()->has('applicant_id') ? ['applicant_id' => request('applicant_id')] : []) }}" class="btn btn-secondary">ล้าง</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Stats --}}
    @if($reviews->isNotEmpty())
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-2xl font-bold text-brand-600">{{ $reviews->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">รีวิวทั้งหมด</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-2xl font-bold text-amber-500">{{ number_format($reviews->avg('rating'), 1) }}</div>
                    <div class="text-xs text-gray-500 mt-1">คะแนนเฉลี่ย</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ $groupedReviews->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">ตำแหน่ง</div>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $reviews->where('rating', 5)->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">รีวิว 5 ดาว</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reviews Grouped by Position --}}
    @if($groupedReviews->isEmpty())
        <div class="card">
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <p class="text-gray-400 font-medium">ไม่มีข้อมูลรีวิว</p>
                <p class="text-gray-400 text-xs mt-1">รีวิวจะปรากฏเมื่อมีการเขียนรีวิวจาก Dashboard หรือ LINE</p>
            </div>
        </div>
    @else
        @foreach($groupedReviews as $positionName => $positionReviews)
            <div class="card mb-6">
                {{-- Position Header --}}
                <div class="card-body border-b border-gray-100">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $positionName }}</h3>
                                <p class="text-xs text-gray-500">{{ $positionReviews->count() }} รีวิว</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            {{-- Average Rating Badge --}}
                            <div class="flex items-center gap-1.5 bg-amber-50 px-3 py-1.5 rounded-xl">
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span
                                    class="text-sm font-semibold text-amber-600">{{ number_format($positionReviews->avg('rating'), 1) }}</span>
                            </div>
                            {{-- Rating Distribution Mini --}}
                            <div class="hidden sm:flex items-center gap-1">
                                @for($star = 5; $star >= 1; $star--)
                                    @php $count = $positionReviews->where('rating', $star)->count(); @endphp
                                    @if($count > 0)
                                        <span
                                            class="text-xs px-2 py-0.5 rounded-full {{ $star >= 4 ? 'bg-emerald-100 text-emerald-700' : ($star >= 3 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                            {{ $star }}★ {{ $count }}
                                        </span>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reviews Table --}}
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>พนักงาน</th>
                                <th>ประเภท</th>
                                <th>คะแนน</th>
                                <th>ความคิดเห็น</th>
                                <th>วันที่</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($positionReviews as $review)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-sm font-semibold flex-shrink-0">
                                                {{ strtoupper(substr($review->applicant->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $review->applicant->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($review->reviewer_type === 'shop')
                                            <span class="badge badge-purple">ร้านค้า → พนักงาน</span>
                                        @else
                                            <span class="badge badge-teal">พนักงาน → ร้านค้า</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                                <span class="text-xs font-semibold text-gray-700 ml-1">{{ number_format($review->rating, 1) }}</span>
                                            </div>
                                            
                                            @if($review->reviewer_type === 'shop' && $review->rating_punctuality !== null)
                                                <button type="button" onclick="toggleDetails('details-{{ $review->id }}')" class="text-xs text-brand-600 hover:text-brand-800 flex items-center gap-1 mt-1 font-medium transition-colors w-fit">
                                                    ดูคะแนนย่อย
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-gray-600">
                                        <div class="max-w-xs truncate">{{ $review->comment ?: '-' }}</div>
                                        
                                        @if($review->reviewer_type === 'shop' && $review->rating_punctuality !== null)
                                            <div id="details-{{ $review->id }}" class="hidden mt-3 p-3 bg-gray-50 rounded-lg border border-gray-100 text-xs shadow-inner">
                                                <div class="grid grid-cols-2 gap-y-2 gap-x-4">
                                                    @php
                                                        $criteria = [
                                                            'ตรงต่อเวลา' => $review->rating_punctuality,
                                                            'นัดแล้วมา' => $review->rating_showed_up,
                                                            'ความซื่อสัตย์' => $review->rating_honesty,
                                                            'ขยันอดทน' => $review->rating_diligence,
                                                            'ทำตามคำสั่ง' => $review->rating_following_instructions,
                                                            'อื่นๆ' => $review->rating_others,
                                                        ];
                                                    @endphp
                                                    @foreach($criteria as $label => $score)
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-gray-500">{{ $label }}:</span>
                                                            <div class="flex items-center gap-0.5">
                                                                <span class="font-medium text-gray-700 mr-1">{{ $score }}</span>
                                                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-gray-500 text-xs">{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif

    <script>
        function toggleDetails(id) {
            const el = document.getElementById(id);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>