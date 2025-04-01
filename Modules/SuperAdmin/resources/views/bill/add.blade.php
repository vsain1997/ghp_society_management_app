<form method="POST" action="{{ route($thisModule . '.billing.store') }}" id="addBilingForm">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="billing_type" id="all" value="all">
                <label class="form-check-label" for="all">For All Residents</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="billing_type" id="single" value="single">
                <label class="form-check-label" for="single">Only Single Resident</label>
              </div>
        </div>
    </div>

    <div class="row my-3">
        <div class="col">
            <div class="form-group d-flex flex-column">
                <select name="resident" id="resident" class="form-select form-control select2">
                    <option value="">-- Select Resident--</option>
                    @foreach($residents as $key => $resident)
                        <option value="{{ $resident->id }}">{{ $resident->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="save-close-btn">
        <button type="button" class="border_theme_btn close-btn cancel_btn" data-bs-dismiss="modal">Close</button>
        <button type="button" data-formtype="add" id="submitAddBilingForm" class="bg_theme_btn">Submit</button>

    </div>
</form>
