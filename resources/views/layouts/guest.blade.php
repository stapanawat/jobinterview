<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PKS Recruit — เข้าสู่ระบบ | Petkaset.co</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.login-body {
            font-family: 'Noto Sans Thai', 'Inter', sans-serif;
            background: linear-gradient(135deg, #0D1B0F 0%, #0A120B 40%, #132815 70%, #0D1B0F 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        body.login-body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(46, 125, 50, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(201, 168, 76, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 920px;
            background: rgba(22, 34, 24, 0.95);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), 0 0 60px rgba(46, 125, 50, 0.08);
            border: 1px solid rgba(46, 125, 50, 0.2);
            animation: cardIn 0.5s ease-out;
            backdrop-filter: blur(20px);
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-brand {
            flex: 1;
            background: linear-gradient(160deg, #132815 0%, #0D1B0F 50%, #0A120B 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .login-brand::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #C9A84C, transparent);
        }

        .brand-icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid #C9A84C;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            animation: iconGlow 3s ease-in-out infinite;
        }

        @keyframes iconGlow {

            0%,
            100% {
                box-shadow: 0 0 4px rgba(201, 168, 76, 0.3);
            }

            50% {
                box-shadow: 0 0 16px rgba(201, 168, 76, 0.5);
            }
        }

        .brand-icon-circle svg {
            color: #C9A84C;
        }

        .brand-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #E8F5E9;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 0.8rem;
            color: #C9A84C;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .brand-divider {
            width: 48px;
            height: 3px;
            background: linear-gradient(90deg, #C9A84C, #B8942F);
            border-radius: 2px;
            margin: 20px 0;
        }

        .brand-desc {
            font-size: 0.88rem;
            color: #A5D6A7;
            line-height: 1.7;
            opacity: 0.85;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.82rem;
            color: #A5D6A7;
            padding: 8px 14px;
            border-radius: 8px;
            background: rgba(46, 125, 50, 0.08);
            border: 1px solid rgba(46, 125, 50, 0.12);
            transition: all 0.25s ease;
        }

        .brand-feature:hover {
            background: rgba(46, 125, 50, 0.15);
            transform: translateX(4px);
        }

        .brand-footer {
            font-size: 0.72rem;
            color: #6B8F6E;
            opacity: 0.6;
        }

        .login-form-side {
            flex: 1;
            padding: 48px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(17, 29, 19, 0.9);
        }

        .login-form-inner {
            width: 100%;
            max-width: 340px;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #E8F5E9;
            margin-bottom: 4px;
        }

        .form-subtitle {
            font-size: 0.82rem;
            color: #6B8F6E;
        }

        .pks-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #A5D6A7;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pks-input {
            width: 100%;
            padding: 11px 14px;
            background: #0F1A11;
            border: 1.5px solid rgba(46, 125, 50, 0.2);
            border-radius: 10px;
            color: #E8F5E9;
            font-size: 0.92rem;
            transition: all 0.25s ease;
            outline: none;
            font-family: inherit;
        }

        .pks-input:focus {
            border-color: #C9A84C;
            box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.12);
        }

        .pks-input::placeholder {
            color: #6B8F6E;
            opacity: 0.5;
        }

        .pks-checkbox {
            accent-color: #2E7D32;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .pks-remember {
            font-size: 0.82rem;
            color: #A5D6A7;
        }

        .pks-forgot {
            font-size: 0.82rem;
            color: #C9A84C;
            transition: opacity 0.2s;
        }

        .pks-forgot:hover {
            opacity: 0.7;
            text-decoration: underline;
        }

        .pks-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%);
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(27, 94, 32, 0.3);
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .pks-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(27, 94, 32, 0.4);
        }

        .pks-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
            transition: left 0.5s;
        }

        .pks-btn:hover::before {
            left: 100%;
        }

        .pks-error {
            color: #EF5350;
            font-size: 0.78rem;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 440px;
            }

            .login-brand {
                padding: 28px 24px;
            }

            .login-brand .brand-features-list {
                display: none;
            }

            .login-form-side {
                padding: 28px 24px;
            }
        }
    </style>
</head>

<body class="login-body">
    <div class="min-h-screen flex items-center justify-center p-5 relative z-10">
        <div class="login-container">
            <!-- Left: Branding -->
            <div class="login-brand">
                <div>
                    <div class="brand-icon-circle">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h1 class="brand-title">PKS Recruit</h1>
                    <p class="brand-sub">Petkaset.co</p>
                    <div class="brand-divider"></div>
                    <p class="brand-desc">
                        ระบบสมัครงานและนัดสัมภาษณ์<br>
                        สำหรับบริหารจัดการผู้สมัครงาน<br>
                        และนัดหมายสัมภาษณ์อย่างครบวงจร
                    </p>
                    <div class="brand-features-list"
                        style="display:flex;flex-direction:column;gap:10px;margin-top:24px;">
                        <div class="brand-feature">
                            <svg width="16" height="16" fill="none" stroke="#A5D6A7" viewBox="0 0 24 24"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg> <span>จัดการใบสมัครงาน</span>
                        </div>
                        <div class="brand-feature">
                            <svg width="16" height="16" fill="none" stroke="#A5D6A7" viewBox="0 0 24 24"
                                stroke-width="1.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg> <span>นัดหมายสัมภาษณ์</span>
                        </div>
                        <div class="brand-feature">
                            <svg width="16" height="16" fill="none" stroke="#A5D6A7" viewBox="0 0 24 24"
                                stroke-width="1.5">
                                <line x1="18" y1="20" x2="18" y2="10" />
                                <line x1="12" y1="20" x2="12" y2="4" />
                                <line x1="6" y1="20" x2="6" y2="14" />
                            </svg> <span>รายงานและสถิติ</span>
                        </div>
                    </div>
                </div>
                <p class="brand-footer">© 2026 Petkaset.co — All rights reserved</p>
            </div>

            <!-- Right: Login Form -->
            <div class="login-form-side">
                <div class="login-form-inner">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>