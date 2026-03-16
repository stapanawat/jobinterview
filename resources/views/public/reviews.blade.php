<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีวิวทั้งหมด | PKB</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background: linear-gradient(135deg, #0D1B0F 0%, #132815 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4b5563;
            text-decoration: none;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .stats-card {
            background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: center;
            border: 1px solid #A5D6A7;
        }

        .avg-rating {
            font-size: 32px;
            font-weight: 700;
            color: #1B5E20;
            margin-bottom: 4px;
        }

        .stars {
            color: #f59e0b;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .total-count {
            font-size: 14px;
            color: #4b5563;
        }

        /* Filter */
        .filter-section {
            margin-bottom: 20px;
        }

        .filter-section select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Noto Sans Thai', sans-serif;
            background: #fafafa;
            color: #333;
            outline: none;
            transition: border-color 0.2s;
        }

        .filter-section select:focus {
            border-color: #2E7D32;
        }

        .review-card {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .review-card:last-child {
            border-bottom: none;
        }

        .review-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .reviewer-name {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .review-date {
            font-size: 12px;
            color: #9ca3af;
        }

        .review-position {
            font-size: 12px;
            color: #2E7D32;
            font-weight: 500;
            background: #E8F5E9;
            padding: 2px 8px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .review-comment {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
        }

        /* Pagination Fix - Surgical Reset */
        .pagination-container {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #f3f4f6;
            width: 100%;
            overflow-x: hidden; /* Prevent horizontal scroll on container */
        }

        .pagination-container nav {
            width: 100% !important;
        }

        /* Hide the simple "Previous/Next" mobile-only block (the top buttons) */
        .pagination-container nav > div:first-child {
            display: none !important;
        }

        /* Force the desktop/full block to be visible and centered */
        .pagination-container nav > div:last-child {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            width: 100% !important;
        }

        /* Hide the text info on mobile to save space */
        .pagination-container nav div:last-child p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
            text-align: center;
        }

        /* Target the actual buttons wrapper */
        .pagination-container nav div:last-child > div:last-child {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 4px !important;
            background: transparent !important; /* Force transparent */
            box-shadow: none !important; /* Remove unwanted shadow */
            border: none !important; /* Remove unwanted border */
            padding: 0 !important;
            width: 100% !important;
        }

        /* Style individual buttons */
        .pagination-container a, 
        .pagination-container span {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 38px !important;
            height: 38px !important;
            padding: 0 10px !important;
            border-radius: 10px !important;
            background: #fff !important; /* White background */
            color: #4b5563 !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            border: 1px solid #e5e7eb !important;
            margin: 3px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        }

        .pagination-container a:hover {
            border-color: #2E7D32 !important;
            color: #2E7D32 !important;
            background: #F1F8F1 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }

        /* Styling for dots (...) */
        .pagination-container span[aria-disabled="true"] > span {
            background: transparent !important;
            border: none !important;
            color: #9ca3af !important;
            box-shadow: none !important;
            min-width: auto !important;
        }

        /* Active page */
        .pagination-container span[aria-current="page"] {
            background: linear-gradient(135deg, #2E7D32, #1B5E20) !important;
            border-color: #1B5E20 !important;
            color: #fff !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3) !important;
            z-index: 10;
        }

        .pagination-container span[aria-current="page"] > span {
            background: transparent !important;
            color: #fff !important;
            border: none !important;
            padding: 0 !important;
        }
        
        /* Arrows specific styling */
        .pagination-container svg {
            width: 18px !important;
            height: 18px !important;
            stroke-width: 2.5;
        }

        /* Disabled arrows */
        .pagination-container span[aria-disabled="true"] {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9fafb !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('apply.form') }}" class="back-btn">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1>รีวิวทั้งหมดจากพนักงาน</h1>
        </div>

        <div class="stats-card">
            <div class="avg-rating">{{ number_format($avgRating, 1) }}</div>
            <div class="stars">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($avgRating))
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <div class="total-count">ทั้งหมด {{ $totalReviews }} รีวิว</div>
        </div>

        <!-- Position Filter -->
        <div class="filter-section">
            <select onchange="window.location.href = '{{ route('public.reviews') }}?position=' + this.value">
                <option value="">ทุกตำแหน่ง</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->name }}" {{ request('position') == $pos->name ? 'selected' : '' }}>
                        {{ $pos->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="reviews-list">
            @forelse($reviews as $review)
                <div class="review-card">
                    <div class="review-meta">
                        <span class="reviewer-name">{{ $review->applicant?->name ?? 'ผู้สมัครงาน' }}</span>
                        <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="stars" style="font-size: 14px; margin-bottom: 4px;">
                        @for($i = 0; $i < $review->rating; $i++) ★ @endfor
                    </div>
                    @if($review->applicant?->position)
                        <div class="review-position">{{ $review->applicant->position }}</div>
                    @endif
                    {{-- @if($review->comment)
                        <p class="review-comment">{{ $review->comment }}</p>
                    @endif --}}
                </div>
            @empty
                <div style="text-align: center; padding: 40px; color: #9ca3af;">ยังไม่มีรีวิว</div>
            @endforelse
        </div>

        <div class="pagination-container">
            {{ $reviews->appends(request()->query())->links() }}
        </div>
    </div>
</body>

</html>
