@extends('layouts.app')

@section('title', 'จัดการผู้ใช้ - Admin')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-people text-primary"></i> จัดการผู้ใช้
    </h2>
    
    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="ค้นหาชื่อ, อีเมล...">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">ล้าง</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>URLs</th>
                            <th>สถานะ</th>
                            <th>สมัครเมื่อ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <i class="bi bi-person-circle text-muted"></i>
                                    {{ $user->name }}
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $user->urls_count }}</span>
                                </td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-primary">User</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    @if($user->id !== auth()->id())
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-shield"></i> 
                                                            {{ $user->is_admin ? 'ลดสิทธิ์ Admin' : 'ให้สิทธิ์ Admin' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.users.delete', $user) }}" method="POST"
                                                          onsubmit="return confirm('ต้องการลบผู้ใช้นี้หรือไม่? URLs ทั้งหมดจะถูกลบด้วย')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash"></i> ลบผู้ใช้
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">ไม่พบข้อมูล</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($users->hasPages())
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
