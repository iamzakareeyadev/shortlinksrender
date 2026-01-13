@extends('layouts.app')

@section('title', 'Admin Dashboard - JND ShortLinks')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-shield-lock text-primary"></i> Admin Dashboard
    </h2>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">ผู้ใช้ทั้งหมด</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_users']) }}</h3>
                        </div>
                        <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">URLs ทั้งหมด</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_urls']) }}</h3>
                        </div>
                        <i class="bi bi-link-45deg" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">คลิกทั้งหมด</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_clicks']) }}</h3>
                        </div>
                        <i class="bi bi-cursor" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">URLs ใช้งานได้</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['active_urls']) }}</h3>
                        </div>
                        <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Recent URLs -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">URLs ล่าสุด</h5>
                    <a href="{{ route('admin.urls') }}" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Short Code</th>
                                    <th>ผู้ใช้</th>
                                    <th>คลิก</th>
                                    <th>เวลา</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUrls as $url)
                                    <tr>
                                        <td><code>{{ $url->short_code }}</code></td>
                                        <td>{{ $url->user->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $url->clicks }}</span></td>
                                        <td><small class="text-muted">{{ $url->created_at->diffForHumans() }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top URLs -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">URLs ยอดนิยม</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Short Code</th>
                                    <th>ผู้ใช้</th>
                                    <th>คลิก</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topUrls as $url)
                                    <tr>
                                        <td><code>{{ $url->short_code }}</code></td>
                                        <td>{{ $url->user->name }}</td>
                                        <td><span class="badge bg-primary">{{ number_format($url->clicks) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">ไม่มีข้อมูล</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- URLs Created Chart -->
    @if(!$urlsByDay->isEmpty())
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> URLs สร้างรายวัน (30 วันล่าสุด)</h5>
        </div>
        <div class="card-body">
            <canvas id="urlsChart" height="100" data-chart='@json($urlsByDay->reverse()->values())'></canvas>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('urlsChart');
        const urlsData = JSON.parse(ctx.getAttribute('data-chart'));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: urlsData.map(d => d.date),
                datasets: [{
                    label: 'URLs สร้าง',
                    data: urlsData.map(d => d.count),
                    backgroundColor: '#6366f1',
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
</div>
@endsection
