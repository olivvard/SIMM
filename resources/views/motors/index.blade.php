@extends('layouts.app')
@section('title', 'Motors')
@section('page-title', 'Motor Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mt-3 mb-4">
    <div>
        <p class="text-muted mb-0">Manage all registered motors including soft-deleted records.</p>
    </div>
    <a href="{{ route('motors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-1"></i> Add Motor
    </a>
</div>

{{-- Search & Filter --}}
<div class="card card-panel mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('motors.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold small">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Code, name, or location…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="show_deleted" id="show_deleted" value="1" {{ request('show_deleted') ? 'checked' : '' }}>
                    <label class="form-check-label" for="show_deleted">Show soft-deleted</label>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                <a href="{{ route('motors.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card card-panel">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Install Date</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($motors as $i => $motor)
                    <tr class="{{ $motor->trashed() ? 'table-secondary text-muted' : '' }}">
                        <td>{{ $motors->firstItem() + $i }}</td>
                        <td><span class="fw-semibold text-primary">{{ $motor->motor_code }}</span></td>
                        <td><span class="badge badge-location">{{ $motor->location }} ({{ $motor->area }})</span></td>
                        <td>{{ $motor->category }} HP</td>
                        <td>{{ $motor->installation_date->format('d M Y') }}</td>
                        <td>
                            @if($motor->trashed())
                                <span class="badge bg-secondary">Deleted</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                @if(!$motor->trashed())
                                    <a href="{{ route('motors.show', $motor) }}" class="btn btn-outline-info mx-1" title="View">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('motors.edit', $motor) }}" class="btn btn-outline-warning mx-1" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form method="POST" action="{{ route('motors.destroy', $motor) }}" onsubmit="return confirm('Soft-delete this motor?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger mx-1" title="Soft Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('motors.restore', $motor->id) }}" onsubmit="return confirm('Restore this motor?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Restore">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('motors.forceDelete', $motor->id) }}" onsubmit="return confirm('PERMANENTLY delete? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Force Delete">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-lightning-charge fs-1 d-block opacity-25 mb-2"></i>
                            No motors found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($motors->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $motors->firstItem() }} – {{ $motors->lastItem() }} of {{ $motors->total() }}</small>
        {{ $motors->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
