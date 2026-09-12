@extends('layouts.app')

@section('title', 'Services')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Service Management</h5>
    @if(auth()->user()->canDo('services.add'))
    <a href="{{ route('services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Service
    </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="80">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td>
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" style="height:50px;" class="rounded">
                            @else
                                <div style="height:50px;background:#e9ecef;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $service->name }}</td>
                        <td>{{ $service->category->name ?? '-' }}</td>
                        <td>${{ number_format($service->price, 2) }}</td>
                        <td>{{ $service->duration }} min</td>
                        <td>
                            <span class="badge bg-{{ $service->status ? 'success' : 'secondary' }}">
                                {{ $service->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            @if(auth()->user()->canDo('services.edit'))
                            <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDo('services.delete'))
                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this service?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No services found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection
