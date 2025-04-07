<?php
namespace App;

use App\Models\Plan;
use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Razorpay\Api\Api;

class RazorpayPayments {

    public $api;

    public function __construct()
    {
        $this->api = new Api(
            env("RAZORPAY_KEY_ID"),
            env("RAZORPAY_SECRET_KEY")
        );
    }


    /**
     * Create Rezorpay Order
     * @param Object $bill
     * @return mixed
    */
    public function createOrder($bill){
        try{
            $amount = intval($bill->amount * 100);
            $user = $bill->user;
            $orderId = rand(111111,999999);

            $orderData = [
                'receipt'   => 'rcptid_id',
                'amount'    =>  $amount,  //razorpay tak amount in paisa so we multipley with 100
                'currency'  => 'INR',
                'notes'     => [
                    'order_id'  => $orderId,
                    'bill'   => $bill->id,
                    'user' => $user->uid,
                    'username'  => $user->name,
                    'email'  => $user->email,
                ]
            ];

            $orderResp = $this->api->order->create($orderData);
            return $orderResp;
        } catch(Exception $e){
            return $e;
        }
    }



    /**
     * Create Payment Link
     * @param Object $bill
     * @return mixed
    */
    public function createPaymentLink($bill)
    {
        try {
            $order = $this->createOrder($bill);
            $orderId = $order['notes']['order_id'];
            $amount = intval($bill->amount * 100); // Razorpay accepts amount in paisa
            $user = $bill->user;

            $payload = [
                'upi_link' => true,
                'amount' => $amount,
                'currency' => 'INR',
                'accept_partial' => false,
                //'first_min_partial_amount' => 100, // Optional, only if partial is accepted
                'expire_by' => now()->addMonth(1)->timestamp,
                'reference_id' => 'REF' . $orderId,
                'description' => 'Payment for bill #' . $bill->id,
                'customer' => [
                    'name' => $user->member->name,
                    'contact' => $user->member->phone ?? null,
                    'email' => $user->member->email,
                ],
                'notify' => [
                    'sms' => true,
                    'email' => true,
                ],
                'reminder_enable' => true,
                'notes' => [
                    'order_id' => $orderId,
                    'bill' => $bill->id,
                    'user' => $user->uid,
                    'username' => $user->name,
                    'email' => $user->email,
                ],
                'callback_url' => 'https://example-callback-url.com/',
                'callback_method' => 'get',
            ];

            $response = Http::withBasicAuth(config('services.razorpay.key'), config('services.razorpay.secret_key'))
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.razorpay.com/v1/payment_links/', $payload);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception($response);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }



    /**
     * Get Razorpay Payment Status
     * @param payment_id Razorpay payment id
     * @return renderable
    */
    public function getPayment($paymentId){
        $resp = $this->api->payment->fetch($paymentId);
        return $resp;
    }

}
