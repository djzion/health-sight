<!-- resources/views/components/director-phc-modal.blade.php -->
<div class="modal fade" id="phcSelectionModal" tabindex="-1" aria-labelledby="phcSelectionModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="phcSelectionModalLabel">
                    <i class="fas fa-hospital-user me-2"></i> PHC Assessment Selection
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h4>Welcome to the PHC Assessment Menu</h4>
                    <p class="text-muted">Please select the District, LGA, and PHC you want to assess.</p>
                </div>

                <form id="phcSelectionForm" action="{{ route('assessments.select-phc') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="district_id" class="form-label">Select District</label>
                        <select class="form-select" id="district_id" name="district_id" required>
                            <option value="">-- Select District --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="lga_id" class="form-label">Select Local Government Area</label>
                        <select class="form-select" id="lga_id" name="lga_id" required disabled>
                            <option value="">-- Select LGA --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="phc_id" class="form-label">Select Primary Health Center</label>
                        <select class="form-select" id="phc_id" name="phc_id" required disabled>
                            <option value="">-- Select PHC --</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle me-2"></i> Continue to Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get select elements
    const districtSelect = document.getElementById('district_id');
    const lgaSelect = document.getElementById('lga_id');
    const phcSelect = document.getElementById('phc_id');

    // District change event - loads associated LGAs
    districtSelect.addEventListener('change', function() {
        const districtId = this.value;
        console.log('District selected:', districtId);

        // Reset PHC select
        phcSelect.innerHTML = '<option value="">-- Select PHC --</option>';
        phcSelect.disabled = true;

        if (!districtId) {
            // Reset LGA select
            lgaSelect.innerHTML = '<option value="">-- Select LGA --</option>';
            lgaSelect.disabled = true;
            return;
        }

        // Enable LGA select
        lgaSelect.disabled = false;

        // Fetch LGAs for the selected district
        fetch(`/api/districts/${districtId}/lgas`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Received LGA data:', data);
                // Clear previous options
                lgaSelect.innerHTML = '<option value="">-- Select LGA --</option>';

                // Add new options
                if (data && data.length) {
                    data.forEach(lga => {
                        const option = document.createElement('option');
                        option.value = lga.id;
                        option.textContent = lga.name;
                        lgaSelect.appendChild(option);
                    });
                } else {
                    console.log('No LGAs found for this district');
                }
            })
            .catch(error => {
                console.error('Error fetching LGAs:', error);
            });
    });

    // LGA change event - loads associated PHCs
    lgaSelect.addEventListener('change', function() {
        const lgaId = this.value;
        console.log('LGA selected:', lgaId);

        if (!lgaId) {
            // Reset PHC select
            phcSelect.innerHTML = '<option value="">-- Select PHC --</option>';
            phcSelect.disabled = true;
            return;
        }

        // Enable PHC select
        phcSelect.disabled = false;

        // Fetch PHCs for the selected LGA
        fetch(`/api/lgas/${lgaId}/phcs`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Received PHC data:', data);
                // Clear previous options
                phcSelect.innerHTML = '<option value="">-- Select PHC --</option>';

                // Add new options
                if (data && data.length) {
                    data.forEach(phc => {
                        const option = document.createElement('option');
                        option.value = phc.id;
                        option.textContent = phc.name;
                        phcSelect.appendChild(option);
                    });
                } else {
                    console.log('No PHCs found for this LGA');
                }
            })
            .catch(error => {
                console.error('Error fetching PHCs:', error);
            });
    });
});
</script>
