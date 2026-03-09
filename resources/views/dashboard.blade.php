<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-title">แดชบอร์ด</h1>
            <p class="page-subtitle">ภาพรวมระบบสมัครงานและสัมภาษณ์</p>
        </div>
    </x-slot>

    {{-- Styled Alerts --}}
    <style>
        .pks-alert {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            animation: alertSlideIn 0.4s ease-out;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .pks-alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 14px 0 0 14px;
        }
        .pks-alert-success {
            background: linear-gradient(135deg, #E8F5E9, #F1F8E9);
            border: 1px solid #A5D6A7;
            color: #1B5E20;
        }
        .pks-alert-success::before {
            background: linear-gradient(180deg, #2E7D32, #1B5E20);
        }
        .pks-alert-error {
            background: linear-gradient(135deg, #FFEBEE, #FFF3E0);
            border: 1px solid #EF9A9A;
            color: #B71C1C;
        }
        .pks-alert-error::before {
            background: linear-gradient(180deg, #E53935, #B71C1C);
        }
        .pks-alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pks-alert-success .pks-alert-icon {
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            color: white;
        }
        .pks-alert-error .pks-alert-icon {
            background: linear-gradient(135deg, #E53935, #B71C1C);
            color: white;
        }
        .pks-alert-text {
            flex: 1;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
        }
        .pks-alert-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            color: inherit;
            opacity: 0.5;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .pks-alert-close:hover {
            opacity: 1;
            background: rgba(0,0,0,0.06);
        }
        @keyframes alertSlideIn {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes alertFadeOut {
            from { opacity: 1; transform: translateY(0); max-height: 100px; margin-bottom: 24px; }
            to { opacity: 0; transform: translateY(-8px); max-height: 0; margin-bottom: 0; padding: 0; border: 0; }
        }
    </style>

    <div id="flash-messages">
        @if(session('success'))
            <div class="pks-alert pks-alert-success" id="alertSuccess">
                <div class="pks-alert-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="pks-alert-text">{{ session('success') }}</span>
                <button class="pks-alert-close" onclick="this.parentElement.style.animation='alertFadeOut 0.3s ease-out forwards';">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="pks-alert pks-alert-error" id="alertError">
                <div class="pks-alert-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <span class="pks-alert-text">{{ session('error') ?? $errors->first() }}</span>
                <button class="pks-alert-close" onclick="this.parentElement.style.animation='alertFadeOut 0.3s ease-out forwards';">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
    </div>

    {{-- Filter Bar --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-4" id="dashboardFilterForm">
                <div class="flex-1 min-w-[200px] max-w-sm">
                    <label class="form-label">ช่วงเวลา</label>
                    <select name="date_filter" class="form-select w-full" onchange="document.getElementById('dashboardFilterForm').submit()">
                        <option value="">ทั้งหมด (ล่าสุด)</option>
                        <option value="today" {{ (isset($dateFilter) && $dateFilter === 'today') ? 'selected' : '' }}>วันนี้</option>
                        <option value="yesterday" {{ (isset($dateFilter) && $dateFilter === 'yesterday') ? 'selected' : '' }}>เมื่อวาน</option>
                        <option value="this_week" {{ (isset($dateFilter) && $dateFilter === 'this_week') ? 'selected' : '' }}>สัปดาห์นี้</option>
                        <option value="this_month" {{ (isset($dateFilter) && $dateFilter === 'this_month') ? 'selected' : '' }}>เดือนนี้</option>
                    </select>
                </div>
                <div class="self-center hidden sm:block text-sm text-gray-500 mb-1">
                    *ข้อมูลในแดชบอร์ดจะแสดงตามช่วงเวลาที่ถูกเลือก
                </div>
            </form>
        </div>
    </div>

    {{-- Pipeline Flow — Split into Interview vs Employee --}}
    <style>
        .pipeline-step { position: relative; text-align: center; padding: 16px 8px; border-radius: 16px; transition: all 0.3s ease; flex: 1; min-width: 0; }
        .pipeline-step:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .pipeline-connector { display: flex; align-items: center; justify-content: center; color: #d1d5db; flex-shrink: 0; width: 24px; }
        .pipeline-connector svg { width: 16px; height: 16px; }
        .pipeline-group { background: white; border-radius: 20px; border: 1px solid #e5e7eb; padding: 20px; box-shadow: 0 1px 8px rgba(0,0,0,0.04); }
        .pipeline-group-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 12px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; }
        .pipeline-transition { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0 8px; flex-shrink: 0; }
        .pipeline-transition-arrow { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; animation: pulseArrow 2s ease-in-out infinite; }
        @keyframes pulseArrow { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.08); } }
        .pipeline-count { font-size: 28px; font-weight: 800; line-height: 1; }
        .pipeline-label { font-size: 11px; font-weight: 600; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pipeline-icon { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; }
        .quick-stat { display: flex; align-items: center; gap: 12px; padding: 14px 20px; background: white; border-radius: 14px; border: 1px solid #e5e7eb; }
        .quick-stat-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    </style>

    <div class="flex flex-col lg:flex-row gap-4 mb-6 items-stretch">
        {{-- Interview Group --}}
        <div class="pipeline-group flex-1">
            <div class="pipeline-group-label bg-amber-50 text-amber-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                ขั้นตอนสัมภาษณ์
            </div>
            <div class="flex items-center gap-1">
                {{-- รอตรวจสอบ --}}
                <div class="pipeline-step bg-amber-50">
                    <div class="pipeline-icon bg-amber-100 text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="pipeline-count text-amber-700">{{ $stats['pending'] }}</div>
                    <div class="pipeline-label text-amber-600">รอตรวจสอบ</div>
                </div>

                <div class="pipeline-connector"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>

                {{-- นัดสัมภาษณ์แล้ว --}}
                <div class="pipeline-step bg-blue-50">
                    <div class="pipeline-icon bg-blue-100 text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="pipeline-count text-blue-700">{{ $stats['scheduled'] }}</div>
                    <div class="pipeline-label text-blue-600">นัดแล้ว</div>
                </div>

                <div class="pipeline-connector"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>

                {{-- ยืนยันเวลานัด --}}
                <div class="pipeline-step bg-purple-50">
                    <div class="pipeline-icon bg-purple-100 text-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="pipeline-count text-purple-700">{{ $stats['time_confirmed'] }}</div>
                    <div class="pipeline-label text-purple-600">ยืนยันเวลา</div>
                </div>

                <div class="pipeline-connector"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>

                {{-- ยืนยันเข้าร่วม --}}
                <div class="pipeline-step bg-emerald-50">
                    <div class="pipeline-icon bg-emerald-100 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="pipeline-count text-emerald-700">{{ $stats['attendance_confirmed'] }}</div>
                    <div class="pipeline-label text-emerald-600">ยืนยันเข้าร่วม</div>
                </div>
            </div>
        </div>

        {{-- Transition Arrow --}}
        <div class="pipeline-transition hidden lg:flex">
            <div class="pipeline-transition-arrow" style="background: linear-gradient(135deg, #10b981, #059669);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>
            <div style="font-size: 9px; font-weight: 700; color: #059669; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.05em;">ผ่านสัมภาษณ์</div>
        </div>

        {{-- Mobile Transition Arrow --}}
        <div class="flex lg:hidden items-center justify-center py-1">
            <div class="pipeline-transition-arrow" style="background: linear-gradient(135deg, #10b981, #059669); transform: rotate(90deg);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>
        </div>

        {{-- Employee Group --}}
        <div class="pipeline-group" style="flex: 0.6;">
            <div class="pipeline-group-label bg-blue-50 text-blue-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                สถานะพนักงาน
            </div>
            <div class="flex items-center gap-1">
                {{-- กำลังทำงานอยู่ --}}
                <div class="pipeline-step bg-blue-50">
                    <div class="pipeline-icon bg-blue-100 text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="pipeline-count text-blue-700">{{ $stats['working'] }}</div>
                    <div class="pipeline-label text-blue-600">ทำงานอยู่</div>
                </div>

                <div class="pipeline-connector"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div>

                {{-- เลิกจ้างแล้ว --}}
                <div class="pipeline-step bg-red-50">
                    <div class="pipeline-icon bg-red-100 text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div class="pipeline-count text-red-700">{{ $stats['terminated'] }}</div>
                    <div class="pipeline-label text-red-600">เลิกจ้าง</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="quick-stat">
            <div class="quick-stat-icon" style="background: linear-gradient(135deg, #2E7D32, #1B5E20); color: white;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500">ผู้สมัครทั้งหมด</div>
                <div class="text-xl font-bold text-gray-900">{{ $stats['total'] }} <span class="text-xs font-normal text-gray-400">คน</span></div>
            </div>
        </div>
        <div class="quick-stat">
            <div class="quick-stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500">ตำแหน่งที่เปิดรับ</div>
                <div class="text-xl font-bold text-gray-900">{{ $stats['active_positions'] }} <span class="text-xs font-normal text-gray-400">ตำแหน่ง</span></div>
            </div>
        </div>
        <div class="quick-stat">
            <div class="quick-stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <div>
                <div class="text-xs font-medium text-gray-500">รีวิวทั้งหมด</div>
                <div class="text-xl font-bold text-gray-900">{{ $stats['total_reviews'] }} <span class="text-xs font-normal text-gray-400">รีวิว</span></div>
            </div>
        </div>
    </div>

    {{-- Positions Management --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 transition-shadow hover:shadow-md">
        <div class="px-6 py-5 flex items-center justify-between cursor-pointer bg-gradient-to-r from-gray-50 to-white" onclick="document.getElementById('positions-content').classList.toggle('hidden'); document.getElementById('positions-chevron').classList.toggle('rotate-180')">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">จัดการตำแหน่งงาน</h2>
                    <p class="text-sm text-gray-500 mt-0.5">เพิ่ม ลบ หรือระงับตำแหน่งงานในหน้าฟอร์มรับสมัคร</p>
                </div>
            </div>
            <button class="text-gray-400 hover:text-gray-600 transition-transform duration-300" id="positions-chevron">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
        
        <div id="positions-content" class="hidden">
            <div class="p-6 bg-white border-b border-gray-100 flex justify-between items-center sm:flex-row flex-col gap-4">
                <p class="text-sm text-gray-500">คุณสามารถจัดการและเพิ่มรายละเอียดเชิงลึกของแต่ละตำแหน่งงานได้</p>
                <button type="button" onclick="openPositionModal('add')" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> 
                    เพิ่มตำแหน่งใหม่
                </button>
            </div>
            
            <div class="p-6 bg-gray-50/50">
                @if($positions->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($positions as $position)
                    <div class="group relative rounded-2xl overflow-hidden transition-all duration-300 {{ $position->is_active ? 'bg-gradient-to-br from-white to-green-50/20 border-green-100 hover:border-brand-300' : 'bg-gray-50/80 border-gray-200' }} border hover:-translate-y-1 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] shadow-sm flex flex-col justify-between min-h-[120px]">
                        {{-- Top highlight bar --}}
                        <div class="absolute top-0 inset-x-0 h-1 {{ $position->is_active ? 'bg-gradient-to-r from-brand-400 to-green-400 opacity-80' : 'bg-gray-200' }}"></div>

                        <div class="p-5 flex flex-col h-full gap-4 pt-6">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $position->is_active ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></div>
                                    <span class="text-[11px] font-bold tracking-wider uppercase {{ $position->is_active ? 'text-green-700' : 'text-gray-500' }}">
                                        {{ $position->is_active ? 'เปิดรับสมัคร' : 'ระงับชั่วคราว' }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-gray-900 text-lg leading-tight group-hover:text-brand-700 transition-colors {{ !$position->is_active ? 'text-gray-500 line-through' : '' }}" title="{{ $position->name }}">
                                    {{ $position->name }}
                                </h3>
                            </div>
                            
                            <div class="mt-auto pt-4 flex items-center justify-between border-t border-gray-100/60">
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">จัดการ</span>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="openPositionModal('edit', {{ json_encode($position) }})" class="p-2 inline-flex items-center justify-center bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 hover:shadow-sm rounded-xl transition-all duration-200" title="แก้ไขรายละเอียดงาน">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <form action="{{ route('positions.update', $position->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $position->name }}">
                                        @if(!$position->is_active)
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit" class="p-2 inline-flex items-center justify-center bg-white border border-gray-200 text-gray-500 hover:text-green-600 hover:border-green-300 hover:bg-green-50 hover:shadow-sm rounded-xl transition-all duration-200" title="เปิดรับสมัครอีกครั้ง">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        @else
                                            <button type="submit" class="p-2 inline-flex items-center justify-center bg-white border border-gray-200 text-gray-500 hover:text-orange-600 hover:border-orange-300 hover:bg-orange-50 hover:shadow-sm rounded-xl transition-all duration-200" title="ปิดรับสมัครชั่วคราว">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                            </button>
                                        @endif
                                    </form>
                                    <form action="{{ route('positions.destroy', $position->id) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบตำแหน่งนีทิ้งถาวร?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 inline-flex items-center justify-center bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-300 hover:bg-red-50 hover:shadow-sm rounded-xl transition-all duration-200" title="ลบตำแหน่งนี้">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-16 px-6 text-center bg-white rounded-2xl border border-gray-100 flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">ยังไม่มีตำแหน่งงาน</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">เริ่มต้นรับสมัครพนักงานของคุณโดยการเพิ่มตำแหน่งงานใหม่ด้านบนได้เลย</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Applicants / Interview Table --}}
    <div class="card mb-8">
        <div class="card-header flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">ผู้สมัคร / สัมภาษณ์</h2>
                    <p class="text-sm text-gray-500 mt-0.5">จัดการผู้สมัครที่อยู่ในขั้นตอนสัมภาษณ์</p>
                </div>
            </div>
            <span class="text-sm font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-700">{{ $applicants->count() }} คน</span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ชื่อ</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ตำแหน่ง</th>
                        <th>สถานะ</th>
                        <th>วันนัดสัมภาษณ์</th>
                        <th class="text-center">ยืนยันล่วงหน้า</th>
                        <th>อัปเดตล่าสุด</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applicants as $applicant)
                        <tr>
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
                                    <option value="scheduled" {{ $applicant->status == 'scheduled' ? 'selected' : '' }}>นัดสัมภาษณ์แล้ว</option>
                                    <option value="time_confirmed" {{ $applicant->status == 'time_confirmed' ? 'selected' : '' }}>ยืนยันเวลานัด</option>
                                    <option value="attendance_confirmed" {{ $applicant->status == 'attendance_confirmed' ? 'selected' : '' }}>ยืนยันเข้าร่วม</option>
                                    <option value="working" {{ $applicant->status == 'working' ? 'selected' : '' }}>✓ ผ่าน → เริ่มทำงาน</option>
                                    <option value="cancelled" {{ $applicant->status == 'cancelled' ? 'selected' : '' }}>✗ ยกเลิก</option>
                                </select>
                            </td>
                            <td>
                                @php
                                    $latestInterview = $applicant->interviews->sortByDesc('created_at')->first();
                                @endphp
                                @if($latestInterview && $latestInterview->interview_date)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($latestInterview->interview_date)->format('d/m/Y') }}</div>
                                            @if($latestInterview->interview_time)
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($latestInterview->interview_time)->format('H:i') }} น.</div>
                                            @endif
                                            @if($latestInterview->location)
                                                <div class="text-xs text-gray-400">{{ $latestInterview->location }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300">ยังไม่มีนัด</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $interview = $applicant->interviews->first();
                                @endphp
                                @if($interview && $interview->day_before_confirmed)
                                    <span title="ยืนยันมาแน่นอน" class="inline-flex items-center justify-center text-green-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                @elseif($interview && $interview->day_before_reminder_sent)
                                    <span title="ส่งแจ้งเตือนแล้ว รอยืนยัน" class="inline-flex items-center justify-center text-amber-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                @else
                                    <span title="ยังไม่ถึงเวลาแจ้งเตือน" class="inline-flex items-center justify-center text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </span>
                                @endif
                            </td>
                            <td class="text-gray-500 text-xs">{{ $applicant->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="flex flex-wrap items-center justify-center gap-2 max-w-[200px] mx-auto">
                                    <button onclick="openApplicantModal({{ json_encode($applicant) }})" class="btn btn-sm" style="background-color: #3b82f6; color: white;">
                                        <svg class="w-3.5 h-3.5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        ดูข้อมูล
                                    </button>
                                    <a href="{{ route('interviews.create', ['applicant' => $applicant->id]) }}"
                                       class="btn btn-primary btn-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        นัดสัมภาษณ์
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
                            <td colspan="8" class="text-center py-12">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-gray-400 font-medium">ไม่มีผู้สมัครในขั้นตอนสัมภาษณ์</p>
                                <p class="text-gray-400 text-xs mt-1">ผู้สมัครจะปรากฏเมื่อมีการสมัครผ่าน LINE</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Employees Table --}}
    <div class="card">
        <div class="card-header flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">พนักงาน</h2>
                    <p class="text-sm text-gray-500 mt-0.5">รายชื่อพนักงานที่กำลังทำงานและเลิกจ้าง</p>
                </div>
            </div>
            <span class="text-sm font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-700">{{ $employees->count() }} คน</span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ชื่อ</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ตำแหน่ง</th>
                        <th>สถานะ</th>
                        <th>รายละเอียดงานที่ทำ</th>
                        <th>อัปเดตล่าสุด</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0 {{ $employee->status === 'working' ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600' }}">
                                        {{ strtoupper(substr($employee->name ?: '?', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $employee->name ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="text-gray-600">{{ $employee->phone ?: '-' }}</td>
                            <td class="text-gray-600">{{ $employee->position ?: '-' }}</td>
                            <td>
                                <select onchange="handleStatusChange(this, {{ $employee->id }}, '{{ $employee->job_description }}')"
                                    class="status-dropdown text-xs font-semibold rounded-lg px-2.5 py-1.5 pr-6 border-0 cursor-pointer focus:ring-2 focus:ring-brand-300 transition-all
                                    {{ $employee->status === 'working' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}"
                                    style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22%236b7280%22%3E%3Cpath fill-rule=%22evenodd%22 d=%22M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z%22 clip-rule=%22evenodd%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.4rem center; background-size: 1em;">
                                    <option value="working" {{ $employee->status == 'working' ? 'selected' : '' }}>ทำงานอยู่</option>
                                    <option value="terminated" {{ $employee->status == 'terminated' ? 'selected' : '' }}>เลิกจ้างแล้ว</option>
                                </select>
                            </td>
                            <td class="text-gray-600 text-sm">
                                @if($employee->job_description)
                                    <span class="inline-flex items-center gap-1.5" title="{{ $employee->job_description }}">
                                        <svg class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span class="truncate max-w-[180px]">{{ $employee->job_description }}</span>
                                    </span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="text-gray-500 text-xs">{{ $employee->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="flex flex-wrap items-center justify-center gap-2 max-w-[200px] mx-auto">
                                    <button onclick="openApplicantModal({{ json_encode($employee) }})" class="btn btn-sm" style="background-color: #3b82f6; color: white;">
                                        <svg class="w-3.5 h-3.5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        ดูข้อมูล
                                    </button>
                                    <a href="{{ route('reviews.create', ['applicant' => $employee->id]) }}"
                                       class="btn btn-success btn-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        เขียนรีวิว
                                    </a>
                                    <a href="{{ route('reviews.index', ['applicant_id' => $employee->id]) }}"
                                       class="btn btn-secondary btn-sm">
                                        ดูรีวิว
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if($employees->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <p class="text-gray-400 font-medium">ยังไม่มีพนักงาน</p>
                                <p class="text-gray-400 text-xs mt-1">เปลี่ยนสถานะผู้สมัครเป็น "กำลังทำงานอยู่" เพื่อย้ายมาที่นี่</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Applicant Details Modal --}}
    <div id="applicantModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-xl font-semibold text-gray-900">ข้อมูลผู้สมัคร</h3>
                <button onclick="closeApplicantModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium text-gray-500">ชื่อ-นามสกุล:</span> <span id="m_name" class="text-gray-900 block mt-1"></span></div>
                    <div><span class="font-medium text-gray-500">เบอร์โทรศัพท์:</span> <span id="m_phone" class="text-gray-900 block mt-1"></span></div>
                    <div><span class="font-medium text-gray-500">อายุ:</span> <span id="m_age" class="text-gray-900 block mt-1"></span></div>
                    <div><span class="font-medium text-gray-500">ตำแหน่งที่สมัคร:</span> <span id="m_position" class="text-gray-900 block mt-1"></span></div>
                    
                    <div class="col-span-2"><span class="font-medium text-gray-500">ที่พักปัจจุบัน:</span> <span id="m_residence" class="text-gray-900 block mt-1"></span></div>
                    <div class="col-span-2"><span class="font-medium text-gray-500">ปัจจุบันทำอะไร:</span> <span id="m_occupation" class="text-gray-900 block mt-1"></span></div>
                    
                    <div><span class="font-medium text-gray-500">จบการศึกษา (ระดับสูงสุด):</span> <span id="m_education" class="text-gray-900 block mt-1"></span></div>
                    <div><span class="font-medium text-gray-500">มีบุตรกี่คน:</span> <span id="m_children" class="text-gray-900 block mt-1"></span></div>
                    <div><span class="font-medium text-gray-500">ขับขี่รถจักรยานยนต์:</span> <span id="m_motorcycle" class="text-gray-900 block mt-1"></span></div>
                    <div><span class="font-medium text-gray-500">บุคคลที่ติดต่อแทนได้:</span> <span id="m_emergency" class="text-gray-900 block mt-1"></span></div>
                    
                    <div class="col-span-2"><span class="font-medium text-gray-500">เวลาทำงานที่สะดวก/วันหยุดที่ต้องการ:</span> <span id="m_hours" class="text-gray-900 block mt-1"></span></div>
                    <div class="col-span-2"><span class="font-medium text-gray-500">ประสบการณ์ทำงาน:</span> <span id="m_experience" class="text-gray-900 block mt-1 whitespace-pre-wrap"></span></div>
                    <div class="col-span-2"><span class="font-medium text-gray-500">อธิบายข้อดี-ข้อเสียของตัวท่านเอง:</span> <span id="m_pros_cons" class="text-gray-900 block mt-1 whitespace-pre-wrap"></span></div>
                    <div class="col-span-2"><span class="font-medium text-gray-500">ความฝันในชีวิต:</span> <span id="m_dream" class="text-gray-900 block mt-1 whitespace-pre-wrap"></span></div>
                </div>
            </div>
            <div class="p-6 border-t bg-gray-50 flex justify-end">
                <button onclick="closeApplicantModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>

    {{-- Position Details Modal --}}
    <div id="positionModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-6 border-b shrink-0">
                <h3 class="text-xl font-semibold text-gray-900" id="positionModalTitle">เพิ่มตำแหน่งงานใหม่</h3>
                <button type="button" onclick="closePositionModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto min-h-0 bg-gray-50 flex-1">
                <form id="positionForm" action="" method="POST" class="space-y-5">
                    @csrf
                    <div id="positionMethodContainer"></div>
                    <input type="hidden" name="is_full_update" value="1">
                    
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
                        {{-- Small indicator to show this is important --}}
                        <div class="absolute top-0 right-0 p-3 opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-1.5 h-4 bg-brand-500 rounded-full"></div>
                            ข้อมูลหลักที่ผู้สมัครจะเห็น
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อตำแหน่งงาน <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="p_name" required placeholder="เช่น พนักงานเสิร์ฟ, พนักงานชงน้ำ" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อร้าน / สาขา</label>
                                    <input type="text" name="shop_name" id="p_shop_name" placeholder="ชื่อร้านที่รับสมัคร" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ที่ตั้ง</label>
                                    <input type="text" name="location" id="p_location" placeholder="เช่น ราชเทวี, พญาไท" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
                        <div class="absolute top-0 right-0 p-3 opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-1.5 h-4 bg-green-500 rounded-full"></div>
                            รายได้และเวลาทำงาน
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">รายได้ / เงินเดือน</label>
                                <input type="text" name="salary" id="p_salary" placeholder="เช่น 12,000 - 15,000 บาท/เดือน" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เงินพิเศษ / คอมมิชชั่น</label>
                                <input type="text" name="extra_pay" id="p_extra_pay" placeholder="เช่น ทิปรายวัน, OT" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เวลาทำงาน</label>
                                <input type="text" name="working_hours" id="p_working_hours" placeholder="เช่น 9:00 - 18:00 น." class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">วันหยุด</label>
                                <input type="text" name="days_off" id="p_days_off" placeholder="เช่น หยุด 1 วัน/สัปดาห์" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
                        <div class="absolute top-0 right-0 p-3 opacity-20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                            รายละเอียดและเงื่อนไข
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">หน้าที่รับผิดชอบ</label>
                                <textarea name="duties" id="p_duties" rows="2" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียดงานเพิ่มเติม</label>
                                <textarea name="description" id="p_description" rows="2" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">สวัสดิการอื่นๆ</label>
                                <textarea name="benefits" id="p_benefits" rows="2" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">คุณสมบัติผู้สมัคร</label>
                                <textarea name="qualifications" id="p_qualifications" rows="2" class="block w-full border border-gray-300 rounded-lg p-2.5 focus:ring-brand-500 focus:border-brand-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-6 border-t border-gray-200 bg-white flex justify-end gap-3 shrink-0 rounded-b-xl">
                <button type="button" onclick="closePositionModal()" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">ยกเลิก</button>
                <button type="submit" form="positionForm" class="px-5 py-2.5 bg-brand-600 text-white rounded-xl hover:bg-brand-700 font-medium flex items-center justify-center min-w-[120px] transition-colors shadow-sm" id="p_submit_btn">
                    บันทึกข้อมูล
                </button>
            </div>
        </div>
    </div>

    {{-- Job Description Modal --}}
    <div id="jobDescModal" class="fixed inset-0 z-[60] hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">รายละเอียดงานที่ทำ</h3>
                        <p class="text-sm text-gray-500">กรุณาระบุรายละเอียดงานที่ผู้สมัครทำ</p>
                    </div>
                </div>
                <button onclick="closeJobDescModal(false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <textarea id="jobDescInput" rows="4" class="block w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm resize-none" placeholder="เช่น พนักงานเสิร์ฟ ร้านกาแฟสาขาราชเทวี"></textarea>
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-end gap-3">
                <button onclick="closeJobDescModal(false)" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 font-medium transition-colors">ยกเลิก</button>
                <button onclick="closeJobDescModal(true)" class="px-5 py-2.5 text-white rounded-xl font-medium transition-colors shadow-sm" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">บันทึก</button>
            </div>
        </div>
    </div>

    <script>
    // Status change handling
    let pendingStatusChange = null;

    function handleStatusChange(selectEl, applicantId, currentJobDesc) {
        const newStatus = selectEl.value;

        if (newStatus === 'working') {
            // Show job description modal
            pendingStatusChange = { selectEl, applicantId, previousValue: selectEl.dataset.prevValue || selectEl.querySelector('[selected]')?.value || 'pending_review' };
            document.getElementById('jobDescInput').value = currentJobDesc || '';
            document.getElementById('jobDescModal').classList.remove('hidden');
        } else if (newStatus === 'terminated') {
            if (confirm('คุณแน่ใจหรือไม่ที่จะเปลี่ยนสถานะเป็น "เลิกจ้างแล้ว"?')) {
                submitStatusChange(applicantId, newStatus, null, selectEl);
            } else {
                // Revert
                selectEl.value = selectEl.dataset.prevValue || 'pending_review';
            }
        } else {
            submitStatusChange(applicantId, newStatus, null, selectEl);
        }
    }

    function closeJobDescModal(confirmed) {
        if (confirmed && pendingStatusChange) {
            const jobDesc = document.getElementById('jobDescInput').value.trim();
            submitStatusChange(pendingStatusChange.applicantId, 'working', jobDesc, pendingStatusChange.selectEl);
        } else if (pendingStatusChange) {
            // Revert dropdown
            pendingStatusChange.selectEl.value = pendingStatusChange.previousValue;
        }
        pendingStatusChange = null;
        document.getElementById('jobDescModal').classList.add('hidden');
    }

    async function submitStatusChange(applicantId, status, jobDescription, selectEl) {
        // Store previous value for revert
        const prevValue = selectEl.dataset.prevValue || selectEl.querySelector('[selected]')?.value || 'pending_review';

        try {
            selectEl.disabled = true;
            selectEl.style.opacity = '0.5';

            const response = await fetch(`/applicants/${applicantId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status, job_description: jobDescription }),
            });

            const result = await response.json();

            if (result.success) {
                // Update the previous value to the new one
                selectEl.dataset.prevValue = status;

                // Update dropdown color classes
                updateDropdownStyle(selectEl, status);

                // Reload page to update stats & job description column
                window.location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (result.message || 'ไม่สามารถอัปเดตสถานะได้'));
                selectEl.value = prevValue;
            }
        } catch (err) {
            console.error('Status update error:', err);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            selectEl.value = prevValue;
        } finally {
            selectEl.disabled = false;
            selectEl.style.opacity = '1';
        }
    }

    function updateDropdownStyle(selectEl, status) {
        // Remove all status color classes
        selectEl.classList.remove(
            'bg-amber-100', 'text-amber-800',
            'bg-blue-100', 'text-blue-800',
            'bg-purple-100', 'text-purple-800',
            'bg-emerald-100', 'text-emerald-800',
            'bg-red-100', 'text-red-800', 'text-red-700',
            'bg-gray-100', 'text-gray-700'
        );
        const colorMap = {
            'pending_review': ['bg-amber-100', 'text-amber-800'],
            'scheduled': ['bg-blue-100', 'text-blue-800'],
            'time_confirmed': ['bg-purple-100', 'text-purple-800'],
            'attendance_confirmed': ['bg-emerald-100', 'text-emerald-800'],
            'working': ['bg-blue-100', 'text-blue-800'],
            'terminated': ['bg-red-100', 'text-red-800'],
            'cancelled': ['bg-red-100', 'text-red-700'],
        };
        const classes = colorMap[status] || ['bg-gray-100', 'text-gray-700'];
        selectEl.classList.add(...classes);
    }

    // Set initial prevValue on all dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.status-dropdown').forEach(el => {
            el.dataset.prevValue = el.value;
        });
    });

    function openApplicantModal(data) {
        document.getElementById('m_name').textContent = data.name || '-';
        document.getElementById('m_phone').textContent = data.phone || '-';
        document.getElementById('m_age').textContent = data.age ? data.age + ' ปี' : '-';
        document.getElementById('m_position').textContent = data.position || '-';
        document.getElementById('m_residence').textContent = data.current_residence || '-';
        document.getElementById('m_occupation').textContent = data.current_occupation || '-';
        document.getElementById('m_education').textContent = data.education_level || '-';
        document.getElementById('m_children').textContent = data.number_of_children !== null ? data.number_of_children + ' คน' : '-';
        document.getElementById('m_motorcycle').textContent = data.can_drive_motorcycle || '-';
        document.getElementById('m_emergency').textContent = data.emergency_contact || '-';
        document.getElementById('m_hours').textContent = data.preferred_working_hours || '-';
        document.getElementById('m_experience').textContent = data.experience || '-';
        document.getElementById('m_pros_cons').textContent = data.pros_and_cons || '-';
        document.getElementById('m_dream').textContent = data.life_dream || '-';
        
        document.getElementById('applicantModal').classList.remove('hidden');
    }

    function closeApplicantModal() {
        document.getElementById('applicantModal').classList.add('hidden');
    }

    // Modal Handle for Positions
    function openPositionModal(mode, data = null) {
        const form = document.getElementById('positionForm');
        const title = document.getElementById('positionModalTitle');
        const methodContainer = document.getElementById('positionMethodContainer');
        const btn = document.getElementById('p_submit_btn');
        
        // Reset form completely
        form.reset();
        
        if (mode === 'add') {
            title.textContent = 'เพิ่มตำแหน่งงานใหม่';
            form.action = "{{ route('positions.store') }}";
            methodContainer.innerHTML = '';
            btn.innerHTML = 'เพิ่มตำแหน่ง';
        } else if (data) {
            title.textContent = 'แก้ไขรายละเอียด: ' + data.name;
            form.action = `/positions/${data.id}`;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            btn.innerHTML = 'บันทึกการแก้ไข';
            
            // Fill existing data safely
            document.getElementById('p_name').value = data.name || '';
            document.getElementById('p_shop_name').value = data.shop_name || '';
            document.getElementById('p_location').value = data.location || '';
            document.getElementById('p_salary').value = data.salary || '';
            document.getElementById('p_extra_pay').value = data.extra_pay || '';
            document.getElementById('p_working_hours').value = data.working_hours || '';
            document.getElementById('p_days_off').value = data.days_off || '';
            document.getElementById('p_duties').value = data.duties || '';
            document.getElementById('p_description').value = data.description || '';
            document.getElementById('p_benefits').value = data.benefits || '';
            document.getElementById('p_qualifications').value = data.qualifications || '';
        }
        
        document.getElementById('positionModal').classList.remove('hidden');
    }

    function closePositionModal() {
        document.getElementById('positionModal').classList.add('hidden');
    }

    // AJAX Handling for Positions
    document.addEventListener('DOMContentLoaded', function() {
        const attachAjaxForms = () => {
            const positionsContainer = document.getElementById('positions-content');
            if(!positionsContainer) return;
            
            const forms = positionsContainer.querySelectorAll('form');
            forms.forEach(form => {
                if(form.dataset.ajaxAttached) return;
                form.dataset.ajaxAttached = "true";

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const btn = form.querySelector('button[type="submit"]');
                    let originalHtml = '';
                    if(btn) {
                        originalHtml = btn.innerHTML;
                        btn.innerHTML = '⏳...';
                        btn.disabled = true;
                    }

                    try {
                        const formData = new FormData(form);
                        
                        const response = await fetch(form.action, {
                            method: form.method || 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });

                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Replace positions content
                        const newContent = doc.querySelector('#positions-content');
                        if(newContent) {
                            positionsContainer.innerHTML = newContent.innerHTML;
                            attachAjaxForms(); 
                        }

                        // Replace flash messages
                        const newFlash = doc.querySelector('#flash-messages');
                        const currentFlash = document.querySelector('#flash-messages');
                        if(newFlash && currentFlash) {
                            currentFlash.innerHTML = newFlash.innerHTML;
                        }

                    } catch (err) {
                        console.error('AJAX Error:', err);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                    } finally {
                        if (btn && document.body.contains(btn)) {
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }
                    }
                });
            });
        };

        attachAjaxForms();
    });
    </script>
</x-app-layout>