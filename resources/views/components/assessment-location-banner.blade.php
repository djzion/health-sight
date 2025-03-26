@if(session('assessment_location_selected') && session('assessment_district_id') && session('assessment_lga_id') && session('assessment_phc_id'))
    @php
        $district = \App\Models\District::find(session('assessment_district_id'))->name ?? 'Unknown';
        $lga = \App\Models\Lga::find(session('assessment_lga_id'))->name ?? 'Unknown';
        $phc = \App\Models\Phc::find(session('assessment_phc_id'))->name ?? 'Unknown';
    @endphp

    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-3 fs-4"></i>
            <div>
                <strong>Currently Assessing:</strong>
                <p class="mb-0">District: <span class="fw-bold">{{ $district }}</span> |
                   LGA: <span class="fw-bold">{{ $lga }}</span> |
                   PHC: <span class="fw-bold">{{ $phc }}</span>
                </p>
            </div>
        </div>
        <form action="{{ route('assessments.reset-location') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary ms-3">
                Change Location
            </button>
        </form>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
