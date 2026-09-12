@extends('layouts.app')

@section('title', 'Staff')

@section('content')
<div class="d-flex flex-column gap-3 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Staff Management</h5>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Staff
        </a>
        @endif
    </div>

    <form method="GET" class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-muted" for="search">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or email...">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary flex-fill">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px"></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                    <tr>
                        <td>
                            <div class="avatar avatar-sm" style="width:38px;height:38px;font-size:.85rem;">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                        </td>
                        <td class="fw-semibold">{{ $member->name }}</td>
                        <td class="text-muted">{{ $member->email }}</td>
                        <td>{{ $member->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $member->role === 'admin' ? 'warning' : 'secondary' }}">
                                {{ ucfirst($member->role) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $actionsByModule = [];
                                foreach (config('permissions.groups') as $module => $perms) {
                                    $granted = [];
                                    foreach ($perms as $key => $label) {
                                        if (! in_array($key, $member->permissions ?? [], true)) {
                                            continue;
                                        }
                                        if (str_ends_with($key, '.add')) {
                                            $granted[] = 'add';
                                        } elseif (str_ends_with($key, '.edit')) {
                                            $granted[] = 'edit';
                                        } elseif (str_ends_with($key, '.delete')) {
                                            $granted[] = 'delete';
                                        } elseif (str_ends_with($key, '.status')) {
                                            $granted[] = 'status';
                                        }
                                    }
                                    if ($granted) {
                                        $actionsByModule[$module] = $granted;
                                    }
                                }
                            @endphp
                            @if(count($actionsByModule))
                                @foreach($actionsByModule as $module => $granted)
                                    <span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1">{{ $module }} <small>({{ implode(', ', $granted) }})</small></span>
                                @endforeach
                            @else
                                <span class="text-muted">View only</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $member->status ? 'success' : 'secondary' }}">
                                {{ $member->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('staff.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this staff member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-person-vcard d-block mb-2" style="font-size:2rem;"></i>
                            No staff members found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $staff->links() }}
        </div>
    </div>
</div>
@endsection
