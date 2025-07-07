<!-- Enhanced PHC Selection Modal with more flexible date selection -->
<div class="modal fade" id="phcSelectionModal" tabindex="-1" aria-labelledby="phcSelectionModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="phcSelectionModalLabel">
                    <i class="fas fa-hospital-user me-2"></i>
                    <span class="d-none d-sm-inline">PHC Assessment Selection</span>
                    <span class="d-sm-none">Assessment Setup</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="text-center mb-4">
                    <h4 class="h5 h-md-4">Welcome to the PHC Assessment Menu</h4>
                    <p class="text-muted small">Please select the location and assessment period details.</p>
                </div>

                <form id="phcSelectionForm" action="{{ route('assessments.select-phc') }}" method="POST">
                    @csrf

                    <!-- Location Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                Location Selection
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="district_id" class="form-label">District <span class="text-danger">*</span></label>
                                    <select class="form-select" id="district_id" name="district_id" required>
                                        <option value="">-- Select District --</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="lga_id" class="form-label">Local Government <span class="text-danger">*</span></label>
                                    <select class="form-select" id="lga_id" name="lga_id" required disabled>
                                        <option value="">-- Select LGA --</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="phc_id" class="form-label">Primary Health Center <span class="text-danger">*</span></label>
                                    <select class="form-select" id="phc_id" name="phc_id" required disabled>
                                        <option value="">-- Select PHC --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Period Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                Assessment Period & Date
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            {{-- <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Flexible Assessment Dates:</strong> You can conduct assessments for any quarter at any time throughout the year.
                                The assessment date is when you're actually conducting this assessment, regardless of the quarter period.
                            </div> --}}

                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <label for="assessment_quarter" class="form-label">Reporting Quarter <span class="text-danger">*</span></label>
                                    <select class="form-select" id="assessment_quarter" name="assessment_quarter" required>
                                        <option value="">-- Select --</option>
                                        <option value="Q1">Q1 (Jan-Mar)</option>
                                        <option value="Q2">Q2 (Apr-Jun)</option>
                                        <option value="Q3">Q3 (Jul-Sep)</option>
                                        <option value="Q4">Q4 (Oct-Dec)</option>
                                    </select>
                                    <div class="form-text small">Which quarter this assessment covers</div>
                                </div>

                                <div class="col-6 col-md-3">
                                    <label for="assessment_year" class="form-label">Reporting Year <span class="text-danger">*</span></label>
                                    <select class="form-select" id="assessment_year" name="assessment_year" required>
                                        <option value="">-- Select --</option>
                                        <!-- Years will be populated by JavaScript -->
                                    </select>
                                    <div class="form-text small">Which year this assessment covers</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="assessment_date" class="form-label">
                                        Assessment Conduct Date <span class="text-danger">*</span>
                                        <i class="fas fa-question-circle ms-1" data-bs-toggle="tooltip"
                                           title="When you're actually conducting this assessment - can be any date"></i>
                                    </label>
                                    <input type="date" class="form-control" id="assessment_date" name="assessment_date" required>
                                    <div class="form-text small">
                                        <strong>When you're conducting this assessment</strong><br>
                                        {{-- This can be any date throughout the year. For example:
                                        <ul class="mt-1 mb-0 ps-3">
                                            <li>Conduct Q1 assessment in May, July, or any month</li>
                                            <li>Conduct Q2 assessment in August or later</li>
                                            <li>No restriction based on quarter months</li> --}}
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Quarter Information -->
                            {{-- <div class="mt-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-calendar me-2"></i>Quarter Coverage Periods
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary">Q1</strong>
                                                    <small class="text-muted">January - March</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary">Q2</strong>
                                                    <small class="text-muted">April - June</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary">Q3</strong>
                                                    <small class="text-muted">July - September</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="d-flex flex-column">
                                                    <strong class="text-primary">Q4</strong>
                                                    <small class="text-muted">October - December</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 p-2 bg-warning bg-opacity-10 border border-warning rounded">
                                            <small class="text-warning-emphasis">
                                                <i class="fas fa-lightbulb me-1"></i>
                                                <strong>Example:</strong> You can conduct a Q2 assessment (covering April-June activities)
                                                in July, August, or any month when it's convenient. The system allows full flexibility.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Date Examples -->
                            {{-- <div class="mt-3">
                                <div class="card border-success bg-success bg-opacity-10">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-success mb-2">
                                            <i class="fas fa-check-circle me-2"></i>Valid Examples
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-12 col-md-6">
                                                <small class="text-success">
                                                    <strong>Scenario 1:</strong><br>
                                                    Quarter: Q1 (Jan-Mar)<br>
                                                    Assessment Date: July 15, 2024<br>
                                                    <em>✓ Perfectly valid - late assessment</em>
                                                </small>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <small class="text-success">
                                                    <strong>Scenario 2:</strong><br>
                                                    Quarter: Q3 (Jul-Sep)<br>
                                                    Assessment Date: December 10, 2024<br>
                                                    <em>✓ Perfectly valid - end of year catch-up</em>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="continueBtn" disabled>
                            <i class="fas fa-check-circle me-2"></i>
                            <span class="d-none d-sm-inline">Continue to Assessment</span>
                            <span class="d-sm-none">Continue</span>
                        </button>
                    </div>
                </form>

                <!-- Loading indicator -->
                <div id="loadingIndicator" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading assessment...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal positioning and sizing fixes */
.modal-dialog {
    margin: 1rem auto;
    max-width: 900px;
}

.modal-content {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.5rem 0.5rem 0 0;
}

.modal-body {
    max-height: 80vh;
    overflow-y: auto;
}

/* Form improvements */
.form-select, .form-control {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    line-height: 1.5;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:focus, .form-control:focus {
    border-color: #0199dc;
    box-shadow: 0 0 0 0.2rem rgba(1, 153, 220, 0.25);
    outline: 0;
}

.form-select:disabled {
    background-color: #e9ecef;
    opacity: 1;
}

/* Card styling */
.card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.5rem 0.5rem 0 0;
    padding: 0.75rem 1rem;
}

.card-body {
    padding: 1rem;
}

/* Button styling */
.btn-primary {
    background-color: #0199dc;
    border-color: #0199dc;
    color: white;
    font-weight: 500;
    padding: 0.625rem 1.25rem;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
}

.btn-primary:hover:not(:disabled) {
    background-color: #0181b8;
    border-color: #0181b8;
    transform: translateY(-1px);
}

.btn-primary:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
    opacity: 0.65;
    cursor: not-allowed;
}

/* Loading state */
.spinner-border {
    width: 2rem;
    height: 2rem;
}

/* Validation states */
.form-select.is-valid, .form-control.is-valid {
    border-color: #198754;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73 1.31 1.26a.75.75 0 0 0 1.06 0l3.68-3.68a.75.75 0 0 0-1.06-1.06L4 6.59 2.69 5.28a.75.75 0 0 0-1.06 1.05z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem 1rem;
    padding-right: 2.5rem;
}

.form-select.is-invalid, .form-control.is-invalid {
    border-color: #dc3545;
}

/* Enhanced example cards */
.bg-success.bg-opacity-10 {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.border-success {
    border-color: #198754 !important;
}

.text-success {
    color: #198754 !important;
}

.text-warning-emphasis {
    color: #997404 !important;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }

    .modal-body {
        padding: 1rem !important;
        max-height: 85vh;
    }

    .card-body {
        padding: 0.75rem !important;
    }

    .form-select, .form-control {
        font-size: 16px; /* Prevents zoom on iOS */
    }

    .btn {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.25rem;
        max-width: calc(100% - 0.5rem);
    }

    .card-header h6 {
        font-size: 0.9rem;
    }

    .form-label {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .form-text {
        font-size: 0.75rem;
    }
}

/* Fix for modal backdrop */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

/* Ensure proper z-index */
.modal {
    z-index: 1055;
}

.modal-backdrop {
    z-index: 1050;
}

/* Tooltip styling */
[data-bs-toggle="tooltip"] {
    cursor: help;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Enhanced PHC Selection Modal Script Loaded');

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Get form elements
    const form = document.getElementById('phcSelectionForm');
    const districtSelect = document.getElementById('district_id');
    const lgaSelect = document.getElementById('lga_id');
    const phcSelect = document.getElementById('phc_id');
    const quarterSelect = document.getElementById('assessment_quarter');
    const yearSelect = document.getElementById('assessment_year');
    const dateInput = document.getElementById('assessment_date');
    const continueBtn = document.getElementById('continueBtn');
    const loadingIndicator = document.getElementById('loadingIndicator');

    // Check if elements exist
    if (!districtSelect || !lgaSelect || !phcSelect) {
        console.error('Required form elements not found');
        return;
    }

    console.log('All form elements found successfully');

    // Populate years (current year and 5 years back for more flexibility)
    const currentYear = new Date().getFullYear();
    yearSelect.innerHTML = '<option value="">-- Select --</option>';
    for (let year = currentYear; year >= currentYear - 5; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (year === currentYear) {
            option.selected = true;
        }
        yearSelect.appendChild(option);
    }

    // Set current quarter as default
    const currentMonth = new Date().getMonth() + 1;
    let currentQuarter;
    if (currentMonth <= 3) currentQuarter = 'Q1';
    else if (currentMonth <= 6) currentQuarter = 'Q2';
    else if (currentMonth <= 9) currentQuarter = 'Q3';
    else currentQuarter = 'Q4';
    quarterSelect.value = currentQuarter;

    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;

    // Set max date to today (can't assess for future dates, but remove minimum date restriction)
    dateInput.setAttribute('max', today);

    // Remove any minimum date restrictions to allow flexible assessment dates
    // Allow dates from up to 3 years ago for maximum flexibility
    const threeYearsAgo = new Date();
    threeYearsAgo.setFullYear(currentYear - 3);
    dateInput.setAttribute('min', threeYearsAgo.toISOString().split('T')[0]);

    // Form validation function
    function validateForm() {
        const isValid = districtSelect.value &&
                       lgaSelect.value &&
                       phcSelect.value &&
                       quarterSelect.value &&
                       yearSelect.value &&
                       dateInput.value;

        continueBtn.disabled = !isValid;

        // Update visual states
        updateFieldValidation(districtSelect);
        updateFieldValidation(lgaSelect);
        updateFieldValidation(phcSelect);
        updateFieldValidation(quarterSelect);
        updateFieldValidation(yearSelect);
        updateFieldValidation(dateInput);

        // Validate assessment date (more flexible now)
        if (dateInput.value) {
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            const threeYearsAgo = new Date();
            threeYearsAgo.setFullYear(today.getFullYear() - 3);

            if (selectedDate > today) {
                continueBtn.disabled = true;
                showValidationMessage('Assessment date cannot be in the future.');
                return false;
            }

            if (selectedDate < threeYearsAgo) {
                showValidationMessage('Assessment date cannot be more than 3 years old.', 'warning');
            }
        }

        clearValidationMessage();
        return isValid;
    }

    function updateFieldValidation(field) {
        if (field.value) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            if (field.hasAttribute('data-touched')) {
                field.classList.add('is-invalid');
            }
        }
    }

    function showValidationMessage(message, type = 'danger') {
        let alertDiv = document.getElementById('validation-alert');
        if (!alertDiv) {
            alertDiv = document.createElement('div');
            alertDiv.id = 'validation-alert';
            alertDiv.className = `alert alert-${type} mt-3`;
            form.appendChild(alertDiv);
        } else {
            alertDiv.className = `alert alert-${type} mt-3`;
        }
        alertDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${message}`;
        alertDiv.style.display = 'block';
    }

    function clearValidationMessage() {
        const alertDiv = document.getElementById('validation-alert');
        if (alertDiv) {
            alertDiv.style.display = 'none';
        }
    }

    // District change event
    districtSelect.addEventListener('change', function() {
        const districtId = this.value;
        console.log('District changed to:', districtId);

        // Mark as touched
        this.setAttribute('data-touched', 'true');

        // Reset dependent selects
        lgaSelect.innerHTML = '<option value="">-- Select LGA --</option>';
        lgaSelect.disabled = !districtId;
        lgaSelect.classList.remove('is-valid', 'is-invalid');

        phcSelect.innerHTML = '<option value="">-- Select PHC --</option>';
        phcSelect.disabled = true;
        phcSelect.classList.remove('is-valid', 'is-invalid');

        validateForm();

        if (!districtId) return;

        // Show loading state for LGA
        lgaSelect.innerHTML = '<option value="">Loading...</option>';
        lgaSelect.disabled = true;

        // Fetch LGAs
        fetch(`/api/districts/${districtId}/lgas`)
            .then(response => {
                console.log('LGA fetch response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received LGA data:', data);

                // Clear loading state
                lgaSelect.innerHTML = '<option value="">-- Select LGA --</option>';
                lgaSelect.disabled = false;

                if (data && data.length) {
                    data.forEach(lga => {
                        const option = document.createElement('option');
                        option.value = lga.id;
                        option.textContent = lga.name;
                        lgaSelect.appendChild(option);
                    });
                    console.log(`Added ${data.length} LGA options`);
                } else {
                    console.log('No LGAs found for this district');
                    lgaSelect.innerHTML = '<option value="">No LGAs found</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching LGAs:', error);
                lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
                showValidationMessage('Error loading Local Governments. Please check your connection and try again.');
            });
    });

    // LGA change event
    lgaSelect.addEventListener('change', function() {
        const lgaId = this.value;
        console.log('LGA changed to:', lgaId);

        // Mark as touched
        this.setAttribute('data-touched', 'true');

        // Reset PHC select
        phcSelect.innerHTML = '<option value="">-- Select PHC --</option>';
        phcSelect.disabled = !lgaId;
        phcSelect.classList.remove('is-valid', 'is-invalid');

        validateForm();

        if (!lgaId) return;

        // Show loading state for PHC
        phcSelect.innerHTML = '<option value="">Loading...</option>';
        phcSelect.disabled = true;

        // Fetch PHCs
        fetch(`/api/lgas/${lgaId}/phcs`)
            .then(response => {
                console.log('PHC fetch response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received PHC data:', data);

                // Clear loading state
                phcSelect.innerHTML = '<option value="">-- Select PHC --</option>';
                phcSelect.disabled = false;

                if (data && data.length) {
                    data.forEach(phc => {
                        const option = document.createElement('option');
                        option.value = phc.id;
                        option.textContent = phc.name;
                        phcSelect.appendChild(option);
                    });
                    console.log(`Added ${data.length} PHC options`);
                } else {
                    console.log('No PHCs found for this LGA');
                    phcSelect.innerHTML = '<option value="">No PHCs found</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching PHCs:', error);
                phcSelect.innerHTML = '<option value="">Error loading PHCs</option>';
                showValidationMessage('Error loading Primary Health Centers. Please check your connection and try again.');
            });
    });

    // Add validation listeners for all fields
    [phcSelect, quarterSelect, yearSelect, dateInput].forEach(element => {
        element.addEventListener('change', function() {
            this.setAttribute('data-touched', 'true');
            validateForm();
        });
    });

    // Enhanced date input validation (more flexible)
    dateInput.addEventListener('change', function() {
        this.setAttribute('data-touched', 'true');

        const selectedDate = new Date(this.value);
        const today = new Date();
        const threeYearsAgo = new Date();
        threeYearsAgo.setFullYear(today.getFullYear() - 3);

        if (selectedDate > today) {
            showValidationMessage('Assessment date cannot be in the future.');
            this.value = today.toISOString().split('T')[0];
        } else if (selectedDate < threeYearsAgo) {
            showValidationMessage('Assessment date cannot be more than 3 years old.');
            this.value = threeYearsAgo.toISOString().split('T')[0];
        }

        validateForm();
    });

    // Quarter change event to provide helpful information (no date restrictions)
    quarterSelect.addEventListener('change', function() {
        this.setAttribute('data-touched', 'true');

        const selectedQuarter = this.value;

        // Provide helpful context about quarters without restricting dates
        if (selectedQuarter) {
            let quarterMonths = '';
            switch(selectedQuarter) {
                case 'Q1': quarterMonths = 'January-March'; break;
                case 'Q2': quarterMonths = 'April-June'; break;
                case 'Q3': quarterMonths = 'July-September'; break;
                case 'Q4': quarterMonths = 'October-December'; break;
            }

            console.log(`Selected quarter ${selectedQuarter} covers ${quarterMonths} - but assessment can be conducted at any time`);
        }

        validateForm();
    });

    // Form submission with enhanced validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            showValidationMessage('Please fill in all required fields correctly.');
            return;
        }

        // Show loading state
        continueBtn.disabled = true;
        continueBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';

        // Hide any validation messages
        clearValidationMessage();

        // Log submission data for debugging
        console.log('Submitting form with data:', {
            district_id: districtSelect.value,
            lga_id: lgaSelect.value,
            phc_id: phcSelect.value,
            quarter: quarterSelect.value,
            year: yearSelect.value,
            assessment_date: dateInput.value
        });

        // Submit form
        this.submit();
    });

    // Initial validation
    validateForm();

    console.log('Enhanced PHC Selection Modal initialized successfully with flexible date handling');
});
</script>
