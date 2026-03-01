<x-guest-layout>
    <h2 class="form-title">สมัครสมาชิก</h2>
    <p class="form-subtitle" style="margin-bottom: 28px;">สร้างบัญชีเพื่อเข้าใช้งานระบบ</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div style="margin-bottom: 18px;">
            <label for="name" class="pks-label">ชื่อ</label>
            <input id="name" class="pks-input" type="text" name="name" value="{{ old('name') }}" required autofocus
                autocomplete="name" placeholder="ชื่อ-นามสกุล">
            @error('name')
                <p class="pks-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div style="margin-bottom: 18px;">
            <label for="email" class="pks-label">อีเมล</label>
            <input id="email" class="pks-input" type="email" name="email" value="{{ old('email') }}" required
                autocomplete="username" placeholder="your@email.com">
            @error('email')
                <p class="pks-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div style="margin-bottom: 18px;">
            <label for="password" class="pks-label">รหัสผ่าน</label>
            <input id="password" class="pks-input" type="password" name="password" required autocomplete="new-password"
                placeholder="••••••••">
            @error('password')
                <p class="pks-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div style="margin-bottom: 24px;">
            <label for="password_confirmation" class="pks-label">ยืนยันรหัสผ่าน</label>
            <input id="password_confirmation" class="pks-input" type="password" name="password_confirmation" required
                autocomplete="new-password" placeholder="••••••••">
            @error('password_confirmation')
                <p class="pks-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="pks-btn">
            สมัครสมาชิก
        </button>
    </form>

    <!-- Login Link -->
    <p style="text-align:center;margin-top:20px;font-size:0.82rem;color:#6B8F6E;">
        มีบัญชีอยู่แล้ว?
        <a href="{{ route('login') }}" style="color:#C9A84C;font-weight:500;text-decoration:none;">เข้าสู่ระบบ</a>
    </p>
</x-guest-layout>