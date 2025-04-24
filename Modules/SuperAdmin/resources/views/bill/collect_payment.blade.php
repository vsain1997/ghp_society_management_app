<div id="processing" style="display:none;">
    <x-processing-component message="Bill creating please wait..." />
</div>

<div id="form_div">
    <form method="POST" action="{{ route($thisModule . '.billing.collect.cash.payment', ['id' => $bill->id]) }}" id="collectPaymentForm">
        @csrf
        <div class="row my-3">
            <div class="col-md-6" id="payment_mood">
                <div class="form-group d-flex flex-column">
                    <label class="form-check-label" for="payment_mood">Payment Mood <span class="text-danger">*</span></label>
                    <select name="payment_mood" id="payment_mood" class="form-select form-control select2" required>
                        <option value="">-- Select Resident *--</option>
                        <option value="cash">CASH</option>
                        <option value="neft">NEFT</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-check-label" for="amount">Amount <span class="text-danger">*</span></label>
                <input type="number" min="0" class="form-control lh-lg" id="amount" name="amount" placeholder="Amount.." readonly required value="{{ $bill->amount }}">
            </div>
        </div>
        <div class="save-close-btn">
            <button type="button" class="border_theme_btn close-btn cancel_btn" data-bs-dismiss="modal" data-togleid="collect_payment_toggle" data-modalid="collectCashPayment" onclick="uncheckToggle(this)">Close</button>
            <button type="button" data-formtype="add" class="bg_theme_btn" onclick="submitForm(this, 'collectPaymentForm', 'collectCashPayment', 'You want to collect bill?', false, true)" >Submit</button>
        </div>
    </form>
</div>

