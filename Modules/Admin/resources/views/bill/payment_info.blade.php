@if(!$payments->isEmpty())
    @foreach($payments as $key => $payment)
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Txn Id:</strong> {{ $payment->txn_id }}</li>
            <li class="list-group-item"><strong>Amount:</strong> {{ toRupeeCurrency($payment->amount) }}</li>
            <li class="list-group-item"><strong>Payment Mode:</strong> {{ Str::upper($payment->payment_mood) }}</li>
            <li class="list-group-item"><strong>Payment Date:</strong> {{ formateDate($payment->created_at) }}</li>
        </ul>
    @endforeach
@else
@endif
