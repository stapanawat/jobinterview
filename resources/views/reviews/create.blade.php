<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-title">เขียนรีวิว</h1>
            <p class="page-subtitle">รีวิวสำหรับ {{ $applicant->name }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        {{-- Applicant Info Card --}}
        <div class="card mb-6">
            <div class="card-body flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    {{ strtoupper(substr($applicant->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">{{ $applicant->name }}</h3>
                    <p class="text-sm text-gray-500">📞 {{ $applicant->phone }} · 💼 {{ $applicant->position }}</p>
                </div>
            </div>
        </div>

        {{-- Review Form --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">ฟอร์มรีวิว</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">

                    {{-- Reviewer Type --}}
                    <div class="mb-5">
                        <label class="form-label">ประเภทผู้รีวิว</label>
                        <select name="reviewer_type" class="form-select w-full" required>
                            <option value="shop">ร้านค้ารีวิวพนักงาน</option>
                            <option value="employee">พนักงานรีวิวร้านค้า</option>
                        </select>
                        @error('reviewer_type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Employee Review: Single Star Rating --}}
                    <div id="employee-review-section" class="mb-5 hidden">
                        <label class="form-label">คะแนนภาพรวม</label>
                        <div class="flex gap-1 mt-1 star-rating-group" data-input-name="rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer group">
                                    <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" {{ $i === 5 ? 'checked' : '' }}>
                                    <svg data-star="{{ $i }}"
                                        class="w-10 h-10 text-gray-200 peer-checked:text-amber-400 group-hover:text-amber-300 transition-colors duration-150"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Shop Review: Multiple Criteria Ratings --}}
                    <div id="shop-review-section" class="mb-5 space-y-4">
                        <h3 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-3">หัวข้อการประเมิน</h3>

                        @php
                            $criteriaList = [
                                'rating_punctuality' => 'ความตรงต่อเวลา',
                                'rating_showed_up' => 'นัดแล้วมา',
                                'rating_honesty' => 'ความซื่อสัตย์',
                                'rating_diligence' => 'ความขยันอดทน',
                                'rating_following_instructions' => 'การปฏิบัติตามคำสั่ง',
                                'rating_others' => 'อื่นๆ',
                            ];
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            @foreach($criteriaList as $field => $label)
                                <div>
                                    <label class="form-label text-sm mb-1 block">{{ $label }}</label>
                                    <div class="flex gap-1 star-rating-group" data-input-name="{{ $field }}">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="{{ $field }}" value="{{ $i }}" class="hidden peer" {{ $i === 5 ? 'checked' : '' }}>
                                                <svg data-star="{{ $i }}"
                                                    class="w-8 h-8 text-gray-200 peer-checked:text-amber-400 group-hover:text-amber-300 transition-colors duration-150"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                    @error($field)
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Comment --}}
                    <div class="mb-6">
                        <label for="comment" class="form-label">ความคิดเห็น</label>
                        <textarea name="comment" id="comment" rows="4" class="form-input"
                            placeholder="เขียนความคิดเห็นเพิ่มเติมของคุณ...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            บันทึกรีวิว
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Star Rating & UI Toggle JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // UI Toggle Logic
            const reviewerTypeSelect = document.querySelector('select[name="reviewer_type"]');
            const shopSection = document.getElementById('shop-review-section');
            const employeeSection = document.getElementById('employee-review-section');

            function updateUI() {
                const type = reviewerTypeSelect.value;
                if (type === 'shop') {
                    shopSection.style.display = 'block';
                    employeeSection.style.display = 'none';
                    // Enable shop inputs, disable employee inputs
                    shopSection.querySelectorAll('input').forEach(el => el.disabled = false);
                    employeeSection.querySelectorAll('input').forEach(el => el.disabled = true);
                } else {
                    shopSection.style.display = 'none';
                    employeeSection.style.display = 'block';
                    // Enable employee inputs, disable shop inputs
                    shopSection.querySelectorAll('input').forEach(el => el.disabled = true);
                    employeeSection.querySelectorAll('input').forEach(el => el.disabled = false);
                }
            }

            reviewerTypeSelect.addEventListener('change', updateUI);
            updateUI(); // Run on load

            // Multiple Star Ratings Logic
            const ratingGroups = document.querySelectorAll('.star-rating-group');

            ratingGroups.forEach(group => {
                const stars = group.querySelectorAll('svg[data-star]');
                const radios = group.querySelectorAll('input[type="radio"]');

                function updateStars(selectedValue) {
                    stars.forEach(star => {
                        const val = parseInt(star.dataset.star);
                        if (val <= selectedValue) {
                            star.classList.add('text-amber-400');
                            star.classList.remove('text-gray-200');
                        } else {
                            star.classList.remove('text-amber-400');
                            star.classList.add('text-gray-200');
                        }
                    });
                }

                stars.forEach(star => {
                    star.addEventListener('click', function () {
                        const val = parseInt(this.dataset.star);
                        radios.forEach(r => { r.checked = (parseInt(r.value) === val); });
                        updateStars(val);
                    });
                });

                // Initialize state
                const checked = group.querySelector('input:checked');
                if (checked) updateStars(parseInt(checked.value));
            });
        });
    </script>
</x-app-layout>