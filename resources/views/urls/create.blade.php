@extends('layouts.app')

@section('title', 'สร้าง Short URL - JND ShortLinks')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">
                        <i class="bi bi-link-45deg text-primary"></i> สร้าง Short URL
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('urls.store') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="original_url" class="form-label fw-bold">URL ที่ต้องการย่อ *</label>
                            <input type="url" class="form-control form-control-lg @error('original_url') is-invalid @enderror" 
                                   id="original_url" name="original_url" value="{{ old('original_url') }}" 
                                   placeholder="https://example.com/your-long-url" required>
                            @error('original_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">ชื่อ (ไม่บังคับ)</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="ตั้งชื่อเพื่อให้จำง่าย">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="custom_code" class="form-label fw-bold">รหัสย่อกำหนดเอง (ไม่บังคับ)</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ url('/') }}/</span>
                                    <input type="text" class="form-control @error('custom_code') is-invalid @enderror" 
                                           id="custom_code" name="custom_code" value="{{ old('custom_code') }}" 
                                           placeholder="mylink" pattern="[a-zA-Z0-9_-]+" minlength="4" maxlength="10">
                                </div>
                                @error('custom_code')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text">4-10 ตัวอักษร ใช้ได้เฉพาะ a-z, A-Z, 0-9, -, _</div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="expires_at" class="form-label fw-bold">วันหมดอายุ (ไม่บังคับ)</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">ว่างไว้หมายถึงไม่มีวันหมดอายุ</div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-magic"></i> สร้าง Short URL
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
