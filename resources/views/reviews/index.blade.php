<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-title">ประวัติรีวิวทั้งหมด</h1>
            <p class="page-subtitle">รีวิวจากร้านค้าและพนักงาน</p>
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
                <div class="flex-1 min-w-[180px]">
                    <label class="form-label">พนักงาน</label>
                    <select name="applicant_id" class="form-select w-full">
                        <option value="">ทั้งหมด</option>
                        @foreach($applicants as $applicant)
                            <option value="{{ $applicant->id }}" {{ request('applicant_id') == $applicant->id ? 'selected' : '' }}>{{ $applicant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        กรอง
                    </button>
                    <a href="{{ route('reviews.index') }}" class="btn btn-secondary">ล้าง</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="card">
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
                    @foreach ($reviews as $review)
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
                                <div class="flex items-center gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                    <span class="text-xs text-gray-500 ml-1">({{ $review->rating }})</span>
                                </div>
                            </td>
                            <td class="text-gray-600 max-w-xs truncate">{{ $review->comment ?: '-' }}</td>
                            <td class="text-gray-500 text-xs">{{ $review->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach

                    @if($reviews->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                <p class="text-gray-400 font-medium">ไม่มีข้อมูลรีวิว</p>
                                <p class="text-gray-400 text-xs mt-1">รีวิวจะปรากฏเมื่อมีการเขียนรีวิวจาก Dashboard หรือ
                                    LINE</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>