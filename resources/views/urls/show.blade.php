@extends('layouts.app')

@section('title', 'รายละเอียด URL - JND ShortLinks')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> กลับไป Dashboard
        </a>
    </div>
    
    <!-- URL Info Card -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h4 class="fw-bold mb-3">{{ $url->title ?: 'Short URL' }}</h4>
                    
                    <div class="short-url-display d-flex align-items-center justify-content-between mb-3">
                        <span>{{ $url->short_url }}</span>
                        <button class="btn btn-light btn-sm copy-btn" 
                                onclick="copyToClipboard('{{ $url->short_url }}', this)">
                            <i class="bi bi-clipboard"></i> คัดลอก
                        </button>
                    </div>
                    
                    <p class="text-muted mb-2">
                        <i class="bi bi-link"></i> <strong>URL ต้นฉบับ:</strong><br>
                        <a href="{{ $url->original_url }}" target="_blank" class="text-break">
                            {{ $url->original_url }}
                        </a>
                    </p>
                    
                    <div class="d-flex gap-3 flex-wrap text-muted small">
                        <span><i class="bi bi-calendar"></i> สร้างเมื่อ {{ $url->created_at->format('d/m/Y H:i') }}</span>
                        @if($url->expires_at)
                            <span><i class="bi bi-clock"></i> หมดอายุ {{ $url->expires_at->format('d/m/Y H:i') }}</span>
                        @endif
                        <span>
                            @if($url->isAccessible())
                                <span class="badge bg-success">ใช้งานได้</span>
                            @elseif($url->isExpired())
                                <span class="badge bg-warning">หมดอายุ</span>
                            @else
                                <span class="badge bg-danger">ปิดใช้งาน</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="display-4 fw-bold text-primary">{{ number_format($url->clicks) }}</div>
                    <div class="text-muted">คลิกทั้งหมด</div>
                    <div class="mt-3">
                        <a href="{{ route('urls.edit', $url) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> แก้ไข
                        </a>
                        <a href="{{ $url->short_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i> เปิดลิงก์
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="row g-4">
        <!-- Clicks Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> คลิกรายวัน (30 วันล่าสุด)</h5>
                </div>
                <div class="card-body">
                    @if($clicksByDay->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bar-chart" style="font-size: 3rem;"></i>
                            <p class="mt-2">ยังไม่มีข้อมูลการคลิก</p>
                        </div>
                    @else
                        <canvas id="clicksChart" height="200" data-chart='@json($clicksByDay->reverse()->values())'></canvas>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Device & Browser Stats -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-phone"></i> อุปกรณ์</h6>
                </div>
                <div class="card-body">
                    @forelse($clicksByDevice as $device)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-capitalize">
                                @if($device->device === 'mobile')
                                    <i class="bi bi-phone"></i>
                                @elseif($device->device === 'tablet')
                                    <i class="bi bi-tablet"></i>
                                @else
                                    <i class="bi bi-display"></i>
                                @endif
                                {{ $device->device }}
                            </span>
                            <span class="badge bg-secondary">{{ $device->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">ไม่มีข้อมูล</p>
                    @endforelse
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-globe"></i> เบราว์เซอร์</h6>
                </div>
                <div class="card-body">
                    @forelse($clicksByBrowser as $browser)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $browser->browser }}</span>
                            <span class="badge bg-secondary">{{ $browser->count }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">ไม่มีข้อมูล</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$clicksByDay->isEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('clicksChart');
    const clicksData = JSON.parse(ctx.getAttribute('data-chart'));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: clicksData.map(d => d.date),
            datasets: [{
                label: 'คลิก',
                data: clicksData.map(d => d.count),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endif
@endsection
