@extends('layouts.app')

@section('title', 'Dashboard - JND ShortLinks')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Dashboard</h2>
        <a href="{{ route('urls.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> สร้าง URL ใหม่
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">URLs ทั้งหมด</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalUrls) }}</h2>
                        </div>
                        <i class="bi bi-link-45deg" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">คลิกทั้งหมด</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalClicks) }}</h2>
                        </div>
                        <i class="bi bi-cursor" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">อัตราคลิกเฉลี่ย</h6>
                            <h2 class="mb-0 fw-bold">
                                {{ $totalUrls > 0 ? number_format($totalClicks / $totalUrls, 1) : 0 }}
                            </h2>
                        </div>
                        <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- URLs Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">URLs ของคุณ</h5>
        </div>
        <div class="card-body p-0">
            @if($urls->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">ยังไม่มี URL ที่สร้าง</p>
                    <a href="{{ route('urls.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> สร้าง URL แรกของคุณ
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 url-table">
                        <thead class="table-light">
                            <tr>
                                <th>Short URL</th>
                                <th>URL ต้นฉบับ</th>
                                <th>คลิก</th>
                                <th>สถานะ</th>
                                <th>สร้างเมื่อ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($urls as $url)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <code class="text-primary">{{ $url->short_code }}</code>
                                            <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" 
                                                    onclick="copyToClipboard('{{ $url->short_url }}', this)"
                                                    title="คัดลอก">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="original-url" title="{{ $url->original_url }}">
                                        {{ $url->title ?: $url->original_url }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ number_format($url->clicks) }}</span>
                                    </td>
                                    <td>
                                        @if($url->isAccessible())
                                            <span class="badge bg-success">ใช้งานได้</span>
                                        @elseif($url->isExpired())
                                            <span class="badge bg-warning">หมดอายุ</span>
                                        @else
                                            <span class="badge bg-danger">ปิดใช้งาน</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $url->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ $url->short_url }}" target="_blank">
                                                        <i class="bi bi-box-arrow-up-right"></i> เปิดลิงก์
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('urls.show', $url) }}">
                                                        <i class="bi bi-bar-chart"></i> ดูสถิติ
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('urls.edit', $url) }}">
                                                        <i class="bi bi-pencil"></i> แก้ไข
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('urls.destroy', $url) }}" method="POST" 
                                                          onsubmit="return confirm('ต้องการลบ URL นี้หรือไม่?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash"></i> ลบ
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($urls->hasPages())
                    <div class="card-footer bg-white">
                        {{ $urls->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
