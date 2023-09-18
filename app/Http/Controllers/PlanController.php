<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe;

class PlanController extends Controller
{
    // Show all Plans
    public function index()
    {
        $plans = Plan::get();
        return view('welcome', compact('plans'));
    }

    public function checkout($plan_id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $plan = Plan::find($plan_id);

        $metadata = [
            'plan_id' => $plan->id,
            'plan_name' => $plan->plan_name,
            'amount' => $plan->total_payment * 100,
            'customer_name' => auth()->user()->name, // Assuming you have user authentication
            'customer_email' => auth()->user()->email, // Assuming you have user authentication
        ];

        if (auth()->user()->stripe_id == "") {
            $customer = \Stripe\Customer::create([
                'email' => auth()->user()->email,
                'name' => auth()->user()->name,
            ]);
            User::find(auth()->user()->id)->update([
                'stripe_id' => $customer->id
            ]);
        }
        // Create a temporary Price object with the desired amount
        $recurring_price = \Stripe\Price::create([
            'unit_amount' => $plan->total_payment * 100, // Amount should be in cents
            'currency' => 'inr', // Change to the appropriate currency code
            'recurring' => [
                'interval' => $plan->recurring_interval, // Modify the interval as needed
            ],
            'product_data' => [
                'name' =>  $plan->plan_name . " " . ucfirst($plan->recurring_interval), // Replace with your product name
            ],
        ]);

        $line_items = [
            [
                'price' => $recurring_price->id,
                'quantity' => 1,
            ],
        ];


        $one_time_price = null;
        if ($plan->one_time_fees !== null && auth()->user()->plan_id == null) {
            $one_time_price = \Stripe\Price::create([
                'unit_amount' => $plan->one_time_fees * 100, // Amount should be in cents
                'currency' => 'inr', // Change to the appropriate currency code
                'product_data' => [
                    'name' =>  $plan->plan_name . " - One Time Fee", // Replace with your product name
                ],
            ]);

            $line_items[] = [
                'price' => $one_time_price->id,
                'quantity' => 1,
            ];
        }

        $checkout_session = \Stripe\Checkout\Session::create([
            'customer' => auth()->user()->stripe_id,
            'line_items' => $line_items,
            'phone_number_collection' => [
                'enabled' => false,
            ],
            'mode' => 'subscription',
            'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}', // Define your success route
            'cancel_url' => route('cancel') . '?session_id={CHECKOUT_SESSION_ID}', // Define your cancel route
            'metadata' => $metadata,
        ]);
        // echo "<pre>";
        // print_r($paymentIntent);
        // die();
        Transaction::create([
            'user_id' => auth()->user()->id,
            'plan_id' => $plan->id,
            'transactions_id' => $checkout_session->id,
            'amount' => $checkout_session->amount_total / 100,
            'currency' => strtoupper($checkout_session->currency),
            'payment_method_type' => $checkout_session->payment_method_types[0],
            'status' => $checkout_session->status,
            'payment_status' => $checkout_session->payment_status,
            'created_at' => now(),
        ]);
        // return redirect()->away($checkout_session->url);
        return redirect()->to($checkout_session->url);
    }

    public function paymentSuccess(Request $request)
    {
        $session_id = $request->input('session_id');

        // Fetch the session from Stripe
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);
            // echo "<pre>";
            // print_r($session);
            // die();
            // Check the status of the session
            if ($session->payment_status === 'paid') {
                $invoice = \Stripe\Invoice::retrieve($session->invoice);
                $subscription = \Stripe\Subscription::retrieve($invoice->subscription);
                $payment = \Stripe\PaymentMethod::retrieve($subscription->default_payment_method);
                $charge = \Stripe\Charge::retrieve($invoice->charge);
                // echo "<pre>";
                //             print_r($session);
                //             die();
                Transaction::where('transactions_id', $session->id)->update([
                    'transactions_id' => $invoice->payment_intent,
                    'status' => $session->status,
                    'payment_status' => $session->payment_status,
                    'mode' => $session->mode,
                    'subscription_id' => $session->subscription,
                    'invoice_id' => $session->invoice,
                    'updated_at' => now(),
                ]);

                User::where('stripe_id', $invoice->customer)->update([
                    'plan_id' => $session->metadata['plan_id'],
                    'plan_start_date' => date("Y-m-d H:i:s", $subscription->current_period_start),
                    'plan_end_date' => date("Y-m-d H:i:s", $subscription->current_period_end),
                ]);

                $res = [
                    "customer_name" => $invoice->customer_name,
                    "customer_email" => $invoice->customer_email,
                    "amount" => $invoice->total / 100,
                    "invoice_no" => $invoice->number,
                    "transaction_id" => $invoice->payment_intent,
                    "currency" => $invoice->currency,
                    "payment_method" => $session->payment_method_types[0],
                    'payment_method_type' => $payment->card['brand'],
                    'last4' => $payment->card['last4'],
                    'exp_month' => $payment->card['exp_month'],
                    'exp_year' => $payment->card['exp_year'],
                    'invoice_url'=>$invoice->hosted_invoice_url,
                    'invoice_pdf'=>$invoice->invoice_pdf,
                    'receipt_url'=>$charge->receipt_url,
                    'plan_name'=>$session->metadata['plan_name']
                ];
                // echo "<pre>";
                // print_r($res);
                // die();
                // return redirect('/thank-you')->with('res',$res);
                return view('payment_success',compact('res')); // Show a success page
                // return redirect()->to($invoice->hosted_invoice_url);
            } else {
                echo "payment failure";
                // Payment failed
                // return view('payment.failure'); // Show a failure page
            }
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Handle errors when fetching the session from Stripe
            // return view('payment.error');
            dd($e->getMessage());
        }
    }



    public function paymentCancel(Request $request)
    {
        $session_id = $request->input('session_id');

        // Fetch the session from Stripe
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            echo "<pre>";
            print_r($session);
            die();
            // Check the status of the session (optional for cancel)
            if ($session->payment_status === 'paid') {
                // Payment was successful (unlikely for cancel)
                return view('payment.success'); // Show a success page
            } else {
                Transaction::where('transactions_id', $session->id)->update([
                    'status' => $session->status,
                    'payment_status' => $session->payment_status,
                    'mode' => $session->mode,
                    'subscription_id' => $session->subscription,
                    'invoice_id' => $session->invoice,
                    'updated_at' => now(),
                ]);
                // Payment was canceled or failed
                return view('payment.cancel'); // Show a cancel page
            }
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Handle errors when fetching the session from Stripe
            return view('payment.error');
        }
    }


    public function invoicePdf(){
        // $invoicePdf = Transaction::where('user_id',auth()->user()->id)->letest();
        // $invoice = \Stripe\Invoice::retrieve($invoicePdf->invoice_id);
        // $subscription = \Stripe\Subscription::retrieve($invoice->subscription);
        // $payment = \Stripe\PaymentMethod::retrieve($subscription->default_payment_method);
        // $charge = \Stripe\Charge::retrieve($invoice->charge);
        // $res = [
        //     "customer_name" => $invoice->customer_name,
        //     "customer_email" => $invoice->customer_email,
        //     "amount" => $invoice->total / 100,
        //     "invoice_no" => $invoice->number,
        //     "transaction_id" => $invoice->payment_intent,
        //     "currency" => $invoice->currency,
        //     "payment_method" => $session->payment_method_types[0],
        //     'payment_method_type' => $payment->card['brand'],
        //     'last4' => $payment->card['last4'],
        //     'exp_month' => $payment->card['exp_month'],
        //     'exp_year' => $payment->card['exp_year'],
        //     'invoice_url'=>$invoice->hosted_invoice_url,
        //     'invoice_pdf'=>$invoice->invoice_pdf,
        //     'receipt_url'=>$charge->receipt_url,
        //     'plan_name'=>$session->metadata['plan_name']
        // ];

    }
}
