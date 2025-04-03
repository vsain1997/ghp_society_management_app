<div id="processing" style="display:none;">
    <x-processing-component message="Bill creating please wait..." />
</div>

<div id="form_div">
    <form method="POST" action="{{ route($thisModule . '.billing.add') }}" id="addBilingForm">
        @csrf
        <div class="row">
            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
            <input type="hidden" name="society_id" value="{{ $selectedSociety }}">

            <div class="col-md-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="billing_user_type" id="all" value="all" onchange="displayUsersList(this)" required>
                    <label class="form-check-label" for="all">For All Residents</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="billing_user_type" id="single" value="single" onclick="displayUsersList(this)" required>
                    <label class="form-check-label" for="single">Only Single Resident</label>
                  </div>
            </div>
        </div>

        <div class="row my-3">
            <div class="col" id="residents_list" style="display: none">
                <div class="form-group d-flex flex-column">
                    <select name="user_id" id="resident" class="form-select form-control select2 resident_required">
                        <option value="">-- Select Resident *--</option>
                        @if (!empty($residents))
                            @foreach ($residents as $resident)
                                <option data-floor="{{ $resident->floor_number }}"
                                    data-block="{{ $resident->block_name }}"
                                    data-unitType="{{ $resident->unit_type }}"
                                    data-aprtNo="{{ $resident->aprt_no }}"
                                    data-phone="{{ $resident->phone }}"
                                    data-name="{{ $resident->name }}"
                                    value="{{ $resident->user_id }}">
                                    {{ $resident->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="row my-3">
            <div class="col-md-6" style="display: none">
                <label class="form-check-label" for="amount">Service <span class="text-danger">*</span></label>
                <select name="service_id" id="service_id" class="residentList form-select form-control">
                    <option value="">--select--</option>
                    @if (!empty($billServices))
                        @foreach ($billServices as $services)
                            <option value="{{ $services->id }}" {{ $defaultService->id == $services->id ? 'selected' : '' }}>
                                {{ $services->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-check-label" for="amount">Amount <span class="text-danger">*</span></label>
                <input type="number" min="0" class="form-control" id="amount" name="amount" placeholder="Amount.." required>
            </div>

            <div class="col-md-6">
                <label class="form-check-label" for="due_date">Due Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="due_date" name="due_date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
            </div>
        </div>

        <div class="save-close-btn">
            <button type="button" class="border_theme_btn close-btn cancel_btn" data-bs-dismiss="modal">Close</button>
            <button type="button" data-formtype="add" class="bg_theme_btn" onclick="submitForm(this, 'addBilingForm', 'addBillModal', 'You want to create bill?')" >Submit</button>
        </div>
    </form>
</div>

