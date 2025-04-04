<div id="processing" style="display:none;">
    <x-processing-component message="Bill updating please wait..." />
</div>

<div id="form_div">
    <form method="POST" action="{{ route($thisModule . '.billing.update.bill', ['bill_id' => $bill->id]) }}" id="updateBilingForm">
        @csrf
        <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">

        <div class="row my-3">
            <div class="col" id="residents_list">
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
                                    value="{{ $resident->user_id }}" {{ $bill->user_id == $resident->user_id ? 'selected' : '' }}>
                                    {{ $resident->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="row my-3">
            <div class="col-md-6">
                <label class="form-check-label" for="amount">Amount <span class="text-danger">*</span></label>
                <input type="number" min="0" class="form-control" id="amount" name="amount" placeholder="Amount.." required value="{{ $bill->amount }}">
            </div>

            <div class="col-md-6">
                <label class="form-check-label" for="due_date">Due Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="due_date" name="due_date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required value="{{ $bill->due_date }}">
            </div>
        </div>

        <div class="save-close-btn">
            <button type="button" class="border_theme_btn close-btn cancel_btn" data-bs-dismiss="modal">Close</button>
            <button type="button" data-formtype="add" class="bg_theme_btn" onclick="submitForm(this, 'updateBilingForm', 'updateBillModal', 'You want to update this bill?', false, true)" >Submit</button>
        </div>
    </form>
</div>

