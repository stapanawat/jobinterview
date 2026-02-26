<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>สมัครงาน | LINE Job</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 0;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            min-height: 100vh;
            padding: 24px 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .header p {
            font-size: 14px;
            color: #666;
            margin-top: 4px;
        }

        /* Reviews Section */
        .reviews-section {
            background: linear-gradient(135deg, #f8f9ff, #f0f0ff);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            border: 1px solid #e8e8ff;
        }

        .reviews-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .reviews-header h3 {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .avg-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
            font-weight: 600;
            color: #f59e0b;
        }

        .review-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 8px;
            border: 1px solid #f0f0f0;
        }

        .review-card:last-child {
            margin-bottom: 0;
        }

        .review-stars {
            color: #f59e0b;
            font-size: 13px;
        }

        .review-comment {
            font-size: 13px;
            color: #555;
            margin-top: 4px;
            line-height: 1.5;
        }

        .review-meta {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
        }

        .no-reviews {
            text-align: center;
            color: #999;
            font-size: 13px;
            padding: 12px;
        }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        .form-group label .required {
            color: #ef4444;
            margin-left: 2px;
        }

        .form-group input[type="text"],
        .form-group input[type="tel"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Noto Sans Thai', sans-serif;
            transition: border-color 0.2s;
            background: #fafafa;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        /* File Upload */
        .file-upload {
            position: relative;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #fafafa;
        }

        .file-upload:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .file-upload input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload .icon {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .file-upload .text {
            font-size: 13px;
            color: #666;
        }

        .file-upload .preview {
            max-width: 100%;
            max-height: 120px;
            border-radius: 8px;
            margin-top: 8px;
            display: none;
        }

        /* PDPA */
        .pdpa-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .pdpa-section h4 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .pdpa-section p {
            font-size: 12px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .pdpa-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .pdpa-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #667eea;
            flex-shrink: 0;
        }

        .pdpa-checkbox label {
            font-size: 13px;
            color: #333;
            cursor: pointer;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Noto Sans Thai', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .submit-btn .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Success Screen */
        .success-screen {
            display: none;
            text-align: center;
            padding: 60px 20px;
        }

        .success-screen .icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .success-screen h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .success-screen p {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        .close-btn {
            margin-top: 24px;
            padding: 12px 40px;
            background: #06c755;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Noto Sans Thai', sans-serif;
            cursor: pointer;
        }

        /* Error */
        .error-text {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .form-group.error input,
        .form-group.error textarea {
            border-color: #ef4444;
        }

        .form-group.error .error-text {
            display: block;
        }

        /* Loading */
        .loading-screen {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            flex-direction: column;
            gap: 12px;
        }

        .loading-screen .spinner-lg {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 0.8s linear infinite;
        }

        .loading-screen p {
            color: #666;
            font-size: 14px;
        }

        /* Section divider */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Loading Screen -->
        <div id="loading-screen" class="loading-screen">
            <div class="spinner-lg"></div>
            <p>กำลังเตรียมฟอร์ม...</p>
        </div>

        <!-- Application Form -->
        <div id="form-section" style="display: none;">
            <div class="header">
                <div class="logo">💼</div>
                <h1>สมัครงาน</h1>
                <p>กรอกข้อมูลเพื่อสมัครงานกับเรา</p>
            </div>

            <!-- Shop Reviews -->
            <div class="reviews-section">
                <div class="reviews-header">
                    <h3>⭐ รีวิวร้านค้า</h3>
                    @if($totalReviews > 0)
                        <span class="avg-rating">{{ number_format($avgRating, 1) }}/5 ({{ $totalReviews }})</span>
                    @endif
                </div>
                @if($reviews->isEmpty())
                    <div class="no-reviews">ยังไม่มีรีวิวในขณะนี้</div>
                @else
                    @foreach($reviews as $review)
                        <div class="review-card">
                            <div class="review-stars">@for($i = 0; $i < $review->rating; $i++)⭐@endfor</div>
                            <div class="review-comment">"{{ $review->comment ?: 'ไม่มีความคิดเห็น' }}"</div>
                            <div class="review-meta">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                    @endforeach
                @endif
            </div>

            <form id="application-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="line_user_id" id="line_user_id">
                <input type="hidden" name="line_display_name" id="line_display_name">
                <input type="hidden" name="line_picture_url" id="line_picture_url">

                <!-- Personal Info -->
                <div class="section-title">📋 ข้อมูลส่วนตัว</div>

                <div class="form-group">
                    <label>ชื่อ-นามสกุล <span class="required">*</span></label>
                    <input type="text" name="name" id="name" placeholder="กรอกชื่อ-นามสกุล">
                    <div class="error-text">กรุณากรอกชื่อ-นามสกุล</div>
                </div>

                <div class="form-group">
                    <label>เบอร์โทรศัพท์ <span class="required">*</span></label>
                    <input type="tel" name="phone" id="phone" placeholder="08X-XXX-XXXX">
                    <div class="error-text">กรุณากรอกเบอร์โทรศัพท์</div>
                </div>

                <div class="form-group">
                    <label>ที่อยู่</label>
                    <textarea name="address" id="address" placeholder="กรอกที่อยู่ปัจจุบัน (ไม่บังคับ)"></textarea>
                </div>

                <!-- Job Info -->
                <div class="section-title" style="margin-top: 8px;">💼 ข้อมูลงาน</div>

                <div class="form-group">
                    <label>ตำแหน่งที่ต้องการสมัคร <span class="required">*</span></label>
                    <input type="text" name="position" id="position" placeholder="เช่น พนักงานบริการ, แคชเชียร์">
                    <div class="error-text">กรุณากรอกตำแหน่ง</div>
                </div>

                <div class="form-group">
                    <label>ประสบการณ์ทำงาน</label>
                    <textarea name="experience" id="experience"
                        placeholder="เช่น เคยทำงานร้านกาแฟ 1 ปี (ไม่บังคับ)"></textarea>
                </div>

                <!-- File Uploads -->
                <div class="section-title" style="margin-top: 8px;">📎 แนบเอกสาร</div>

                <div class="form-group">
                    <label>รูปถ่าย</label>
                    <div class="file-upload" id="photo-upload">
                        <div class="icon">📸</div>
                        <div class="text">แตะเพื่ออัพโหลดรูปถ่าย</div>
                        <img class="preview" id="photo-preview">
                        <input type="file" name="photo" id="photo" accept="image/*"
                            onchange="previewFile(this, 'photo-preview')">
                    </div>
                </div>

                <div class="form-group">
                    <label>สำเนาบัตรประชาชน</label>
                    <div class="file-upload" id="idcard-upload">
                        <div class="icon">🪪</div>
                        <div class="text">แตะเพื่ออัพโหลดสำเนาบัตรประชาชน</div>
                        <img class="preview" id="idcard-preview">
                        <input type="file" name="id_card_image" id="id_card_image" accept="image/*"
                            onchange="previewFile(this, 'idcard-preview')">
                    </div>
                </div>

                <!-- PDPA -->
                <div class="pdpa-section">
                    <h4>🔒 ข้อตกลงการคุ้มครองข้อมูลส่วนบุคคล (PDPA)</h4>
                    <p>ข้าพเจ้ายินยอมให้จัดเก็บ ใช้ และเปิดเผยข้อมูลส่วนบุคคลของข้าพเจ้า
                        เพื่อวัตถุประสงค์ในการสมัครงานและการนัดหมายสัมภาษณ์
                        ข้อมูลจะถูกเก็บรักษาอย่างปลอดภัยและไม่ถูกเผยแพร่ต่อบุคคลที่สามโดยไม่ได้รับอนุญาต</p>
                    <div class="pdpa-checkbox">
                        <input type="checkbox" name="pdpa_accepted" id="pdpa_accepted" value="1">
                        <label for="pdpa_accepted">ข้าพเจ้ายอมรับข้อตกลงการคุ้มครองข้อมูลส่วนบุคคล</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">
                    <span class="btn-text">ส่งใบสมัคร</span>
                    <div class="spinner"></div>
                </button>
            </form>
        </div>

        <!-- Success Screen -->
        <div id="success-screen" class="success-screen">
            <div class="icon">🎉</div>
            <h2>สมัครงานสำเร็จ!</h2>
            <p>ข้อมูลของคุณถูกส่งเรียบร้อยแล้ว<br>ทาง HR จะติดต่อกลับทาง LINE นะครับ</p>
            <button class="close-btn" onclick="closeLiff()">กลับ LINE</button>
        </div>
    </div>

    <!-- LIFF SDK -->
    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script>
        const LIFF_ID = "{{ $liffId }}";

        // Initialize LIFF
        async function initLiff() {
            try {
                await liff.init({ liffId: LIFF_ID });

                if (!liff.isLoggedIn()) {
                    liff.login();
                    return;
                }

                // Get LINE Profile
                const profile = await liff.getProfile();
                document.getElementById('line_user_id').value = profile.userId;
                document.getElementById('line_display_name').value = profile.displayName;
                document.getElementById('line_picture_url').value = profile.pictureUrl || '';

                // Pre-fill name from LINE display name
                document.getElementById('name').value = profile.displayName;

                // Show form, hide loading
                document.getElementById('loading-screen').style.display = 'none';
                document.getElementById('form-section').style.display = 'block';

            } catch (err) {
                console.error('LIFF init failed:', err);
                // Fallback: show form anyway (for testing in browser)
                document.getElementById('line_user_id').value = 'dummy_user_id_' + Math.floor(Math.random() * 1000000);
                document.getElementById('line_display_name').value = 'Guest User';
                document.getElementById('loading-screen').style.display = 'none';
                document.getElementById('form-section').style.display = 'block';
            }
        }

        // File preview
        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    input.parentElement.querySelector('.icon').style.display = 'none';
                    input.parentElement.querySelector('.text').textContent = 'แตะเพื่อเปลี่ยนรูป';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form submission
        document.getElementById('application-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear errors
            document.querySelectorAll('.form-group').forEach(g => g.classList.remove('error'));

            // Validate
            let hasError = false;
            ['name', 'phone', 'position'].forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    input.closest('.form-group').classList.add('error');
                    hasError = true;
                }
            });

            if (!document.getElementById('pdpa_accepted').checked) {
                alert('กรุณายอมรับข้อตกลง PDPA');
                hasError = true;
            }

            if (hasError) return;

            // Show loading
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.spinner').style.display = 'block';

            // Submit
            const formData = new FormData(this);

            try {
                const response = await fetch('/apply', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    if (typeof liff !== 'undefined' && liff.isLoggedIn() && liff.isInClient()) {
                        liff.sendMessages([{
                            type: 'text',
                            text: 'ยืนยันการส่งใบสมัคร'
                        }]).then(() => {
                            liff.closeWindow();
                        }).catch((err) => {
                            console.error('liff.sendMessages error', err);
                            document.getElementById('form-section').style.display = 'none';
                            document.getElementById('success-screen').style.display = 'block';
                        });
                    } else {
                        document.getElementById('form-section').style.display = 'none';
                        document.getElementById('success-screen').style.display = 'block';
                    }
                } else {
                    console.error('Validation errors:', result.errors || result.message);
                    alert('เกิดข้อผิดพลาด: ' + (result.message || 'กรุณาลองใหม่อีกครั้ง'));
                    btn.disabled = false;
                    btn.querySelector('.btn-text').style.display = 'inline';
                    btn.querySelector('.spinner').style.display = 'none';
                }
            } catch (err) {
                console.error('Submit error:', err);
                alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                btn.disabled = false;
                btn.querySelector('.btn-text').style.display = 'inline';
                btn.querySelector('.spinner').style.display = 'none';
            }
        });

        // Close LIFF and go back to LINE
        function closeLiff() {
            if (liff.isInClient()) {
                liff.closeWindow();
            } else {
                window.location.href = 'https://line.me/R/';
            }
        }

        // Start
        initLiff();
    </script>
</body>

</html>