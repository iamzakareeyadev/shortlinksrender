@extends('layouts.app')

@section('title', 'JND ShortLinks - ย่อ URL ของคุณ')

@section('content')
<div class="container py-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">
                ย่อ URL ของคุณ<br>
                <span class="text-primary">ง่ายและรวดเร็ว</span>
            </h1>
            <p class="lead text-muted mb-4">
                สร้างลิงก์สั้นๆ ที่จดจำง่าย แชร์ได้สะดวก พร้อมติดตามสถิติการคลิก
            </p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-3">
                    <i class="bi bi-person-plus"></i> เริ่มต้นใช้งาน
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                    เข้าสู่ระบบ
                </a>
            @else
                <a href="{{ route('urls.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> สร้าง Short URL
                </a>
            @endguest
        </div>
        <div class="col-lg-6 text-center">
            <div class="p-5">
                <i class="bi bi-link-45deg text-primary" style="font-size: 15rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    
    <!-- Features -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-lightning-charge text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">รวดเร็ว</h5>
                    <p class="card-text text-muted">
                        สร้าง Short URL ได้ทันทีภายในเสี้ยววินาที ไม่ต้องรอนาน
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-bar-chart text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">ติดตามสถิติ</h5>
                    <p class="card-text text-muted">
                        ดูจำนวนการคลิก อุปกรณ์ เบราว์เซอร์ และข้อมูลอื่นๆ
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title">ปลอดภัย</h5>
                    <p class="card-text text-muted">
                        ลิงก์ของคุณถูกเก็บอย่างปลอดภัย พร้อมระบบจัดการที่มีประสิทธิภาพ
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
