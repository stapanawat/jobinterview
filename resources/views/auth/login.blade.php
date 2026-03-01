<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="form-title">เข้าสู่ระบบ</h2>
    <p class="form-subtitle" style="margin-bottom: 28px;">กรุณากรอกข้อมูลเพื่อเข้าใช้งานระบบ</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div style="margin-bottom: 18px;">
            <label for="email" class="pks-label">อีเมล</label>
            <input id="email" class="pks-input" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="your@email.com">
            @error('email')
                <p class="pks-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div style="margin-bottom: 18px;">
            <label for="password" class="pks-label">รหัสผ่าน</label>
            <input id="password" class="pks-input" type="password" name="password" required
                autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <p class="pks-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div style="margin-bottom:24px;">
            <label for="remember_me" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input id="remember_me" type="checkbox" class="pks-checkbox" name="remember">
                <span class="pks-remember">จดจำการเข้าสู่ระบบ</span>
            </label>
        </div>

        <button type="submit" class="pks-btn">
            เข้าสู่ระบบ
        </button>
    </form>

    <!-- Register Link -->
    <p style="text-align:center;margin-top:20px;font-size:0.82rem;color:#6B8F6E;">
        ยังไม่มีบัญชี?
        <a href="{{ route('register') }}" style="color:#C9A84C;font-weight:500;text-decoration:none;">สมัครสมาชิก</a>
    </p>
</x-guest-layout>