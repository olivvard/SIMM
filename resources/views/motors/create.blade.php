@extends('layouts.app')
@section('title', 'Add Motor')
@section('page-title', 'Add New Motor')

@section('content')
<div class="row justify-content-center mt-3">
    <div class="col-lg-8">
        <div class="card card-panel">
            <div class="card-header">
                <h6 class="card-panel__title mb-0">
                    <i class="bi bi-plus-circle-fill me-2 text-primary"></i>Motor Information
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('motors.store') }}" novalidate>
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="motor_code" class="form-label fw-semibold">Motor Code <span class="text-danger">*</span></label>
                            <input type="text" id="motor_code" name="motor_code"
                                class="form-control @error('motor_code') is-invalid @enderror"
                                value="{{ old('motor_code') }}" placeholder="e.g. MTR-001" required>
                            @error('motor_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                            <select id="location" name="location" class="form-select @error('location') is-invalid @enderror" required>
                                <option value="">-- Select Location --</option>
                                <option value="MA-1" {{ old('location') == 'MA-1' ? 'selected' : '' }}>MA-1</option>
                                <option value="MA-2" {{ old('location') == 'MA-2' ? 'selected' : '' }}>MA-2</option>
                                <option value="MA-3" {{ old('location') == 'MA-3' ? 'selected' : '' }}>MA-3</option>
                                <option value="MA-4" {{ old('location') == 'MA-4' ? 'selected' : '' }}>MA-4</option>
                            </select>
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="area" class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                            <select id="area" name="area" class="form-select @error('area') is-invalid @enderror" required disabled>
                                <option value="">-- Select Area --</option>
                            </select>
                            @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Category (HP) <span class="text-danger">*</span></label>
                            <select id="category" name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                <option value="<100" {{ old('category') == '<100' ? 'selected' : '' }}><100 HP</option>
                                <option value="100-500" {{ old('category') == '100-500' ? 'selected' : '' }}>100-500 HP</option>
                                <option value=">500" {{ old('category') == '>500' ? 'selected' : '' }}>>500 HP</option>
                            </select>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="installation_date" class="form-label fw-semibold">Installation Date <span class="text-danger">*</span></label>
                            <input type="date" id="installation_date" name="installation_date"
                                class="form-control @error('installation_date') is-invalid @enderror"
                                value="{{ old('installation_date') }}" required>
                            @error('installation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Motor
                        </button>
                        <a href="{{ route('motors.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const locationSelect = document.getElementById('location');
        const areaSelect = document.getElementById('area');
        const areas = {
            'MA-1': ['DHDT', 'COOKER', 'HVU', 'DCU'],
            'MA-2': ['RX', 'PL-2', 'PL-1', 'H2P', 'HCC'],
            'MA-3': ['PLTU', 'WTP', 'HDC', 'EX-BOILER', 'JETTY-1', 'JETTY-3'],
            'MA-4': ['LPG', 'JETTY-2', 'PUMP-HOUSE', 'SEPARATOR']
        };

        const oldArea = "{{ old('area') }}";

        function updateAreas() {
            const loc = locationSelect.value;
            areaSelect.innerHTML = '<option value="">-- Select Area --</option>';
            if (areas[loc]) {
                areaSelect.disabled = false;
                areas[loc].forEach(function(area) {
                    const isSelected = oldArea === area ? 'selected' : '';
                    areaSelect.innerHTML += `<option value="${area}" ${isSelected}>${area}</option>`;
                });
            } else {
                areaSelect.disabled = true;
            }
        }

        locationSelect.addEventListener('change', updateAreas);
        updateAreas();
    });
</script>
@endpush
