@extends('layouts.app')

@section('title', 'เข้าสู่ระบบ - JND ShortLinks')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">เข้าสู่ระบบ</h2>
                        <p class="text-muted">ยินดีต้อนรับกลับมา!</p>
                    </div>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">อีเมล</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">รหัสผ่าน</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">จดจำฉันไว้</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <p class="text-center mb-0">
                        ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
