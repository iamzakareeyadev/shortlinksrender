@extends('layouts.app')

@section('title', 'แก้ไข URL - JND ShortLinks')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('urls.show', $url) }}" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> กลับไป
        </a>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil text-primary"></i> แก้ไข URL
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>Short URL:</strong> {{ $url->short_url }}
                    </div>
                    
                    <form method="POST" action="{{ route('urls.update', $url) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">URL ต้นฉบับ</label>
                            <input type="text" class="form-control" value="{{ $url->original_url }}" disabled>
                            <div class="form-text">ไม่สามารถแก้ไข URL ต้นฉบับได้</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">ชื่อ</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $url->title) }}" 
                                   placeholder="ตั้งชื่อเพื่อให้จำง่าย">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="expires_at" class="form-label fw-bold">วันหมดอายุ</label>
                            <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" name="expires_at" 
                                   value="{{ old('expires_at', $url->expires_at?->format('Y-m-d\TH:i')) }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $url->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">เปิดใช้งาน</label>
                            </div>
                            <div class="form-text">ปิดการใช้งานจะทำให้ลิงก์ไม่สามารถเข้าถึงได้</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> บันทึกการเปลี่ยนแปลง
                            </button>
                            <a href="{{ route('urls.show', $url) }}" class="btn btn-outline-secondary">ยกเลิก</a>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-end">
                        <form action="{{ route('urls.destroy', $url) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('ต้องการลบ URL นี้หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash"></i> ลบ URL นี้
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
