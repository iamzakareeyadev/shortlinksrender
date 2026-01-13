@extends('layouts.app')

@section('title', 'จัดการ URLs - Admin')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-link-45deg text-primary"></i> จัดการ URLs
    </h2>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="ค้นหา URL, ผู้ใช้...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">ทุกสถานะ</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>ใช้งานได้</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>ปิดใช้งาน</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>ล่าสุด</option>
                        <option value="clicks" {{ request('sort') === 'clicks' ? 'selected' : '' }}>คลิกมากสุด</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- URLs Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Short Code</th>
                            <th>URL ต้นฉบับ</th>
                            <th>ผู้ใช้</th>
                            <th>คลิก</th>
                            <th>สถานะ</th>
                            <th>สร้างเมื่อ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($urls as $url)
                            <tr>
                                <td>{{ $url->id }}</td>
                                <td>
                                    <a href="{{ $url->short_url }}" target="_blank">
                                        <code>{{ $url->short_code }}</code>
                                    </a>
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $url->original_url }}">
                                        {{ $url->original_url }}
                                    </div>
                                </td>
                                <td>
                                    <span title="{{ $url->user->email }}">{{ $url->user->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ number_format($url->clicks) }}</span>
                                </td>
                                <td>
                                    @if($url->is_active)
                                        <span class="badge bg-success">ใช้งานได้</span>
                                    @else
                                        <span class="badge bg-danger">ปิดใช้งาน</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $url->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('urls.show', $url) }}">
                                                    <i class="bi bi-eye"></i> ดูรายละเอียด
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.urls.toggle', $url) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-toggle-on"></i> 
                                                        {{ $url->is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.urls.delete', $url) }}" method="POST"
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
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">ไม่พบข้อมูล</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($urls->hasPages())
            <div class="card-footer bg-white">
                {{ $urls->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
