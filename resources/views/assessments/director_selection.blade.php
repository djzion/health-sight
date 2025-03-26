@extends('layouts.app')

@section('content')
    <div class="main-content p-4">
        <div class="header">
            <div>
                <h1>Welcome, {{ Auth::user()->full_name }}</h1>
                <p class="text-muted">PHC Assessment Portal</p>
            </div>
            <div class="profile">
                <img src="https://via.placeholder.com/50" alt="User Profile">
                <span>{{ Auth::user()->full_name }}</span>
            </div>
        </div>

        <div class="card assessment-card p-4 mt-5">
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <h3 class="mb-4 text-center">Welcome to the PHC Assessment Menu</h3>
                    <p class="text-center mb-4">Please select the district, LGA, and PHC you want to assess.</p>

                    <form action="{{ route('assessments.index') }}" method="GET" id="phcSelectionForm">
                        <div class="mb-3">
                            <label for="district_id" class="form-label">Select District</label>
                            <select class="form-select" id="district_id" name="district_id" required>
                                <option value="">-- Select District --</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="lga_id" class="form-label">Select LGA</label>
                            <select class="form-select" id="lga_id" name="lga_id" required disabled>
                                <option value="">-- Select LGA --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="phc_id" class="form-label">Select PHC</label>
                            <select class="form-select" id="phc_id" name="phc_id" required disabled>
                                <option value="">-- Select PHC --</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Continue to Assessment <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                    var myModal = new bootstrap.Modal(document.getElementById('phcSelectionModal'));
                    myModal.show();

                    const districtSelect = document.getElementById('district_id');
                    const lgaSelect = document.getElementById('lga_id');
                    const phcSelect = document.getElementById('phc_id');

                    // When district is selected, fetch LGAs
                    districtSelect.addEventListener('change', function() {
                                const districtId = this.value;

                                // Reset and disable PHC select
                                phcSelect.innerHTML = '<option value="">-- Select PHC --</option>';
                                phcSelect.disabled = true;

                                if (districtId) {
                                    // Enable LGA select
                                    lgaSelect.disabled = false;

                                    // Fetch LGAs for the selected district
                                    fetch(`{{ route('assessments.lgas') }}?district_id=${districtId}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            lgaSelect.innerHTML = '<option value="">-- Select LGA --</option>';
                                            data.forEach(lga => {
                                                lgaSelect.innerHTML +=
                                                    `<option value="${lga.id}">${lga.name}</option>`;
                                            });

                                            lgaSelect.addEventListener('change', function() {
                                                const lgaId = this.value;

                                                if (!lgaId) {
                                                    // Reset PHC select
                                                    phcSelect.innerHTML =
                                                        '<option value="">-- Select PHC --</option>';
                                                    phcSelect.disabled = true;
                                                    return;
                                                }

                                                // Enable PHC select after fetching LGAs
                                                phcSelect.disabled = false;

                                                // Fetch PHCs for the selected LGA
                                                fetch(`/api/lgas/${lgaId}/phcs`)
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        // Clear previous options
                                                        phcSelect.innerHTML =
                                                            '<option value="">-- Select PHC --</option>';

                                                        // Add new options
                                                        data.forEach(phc => {
                                                            const option = document.createElement(
                                                                'option');
                                                            option.value = phc.id;
                                                            option.textContent = phc.name;
                                                            phcSelect.appendChild(option);
                                                        });
                                                    })
                                                    .catch(error => {
                                                        console.error('Error fetching PHCs:', error);
                                                    });
                                            });

                                            // Prevent modal from closing when clicking outside
                                            const phcModal = document.getElementById('phcSelectionModal');
                                            phcModal.setAttribute('data-bs-backdrop', 'static');
                                            phcModal.setAttribute('data-bs-keyboard', 'false');
                                        });
    </script>
