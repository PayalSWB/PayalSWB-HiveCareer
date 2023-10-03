<?php

namespace App\Services\Website;

use App\Models\ManualPayment;
use App\Models\PaymentSetting;
use App\Services\Midtrans\CreateSnapTokenService;
use Modules\Currency\Entities\Currency;
use Modules\Plan\Entities\Plan;

class PricePlanService
{
    /**
     * Get price plan details
     *
     * @return void
     */
    public function details(string $label): array
    {
        // session data storing
        $plan = Plan::where('label', $label)->firstOrFail();
        session(['stripe_amount' => currencyConversion($plan->price) * 100]);
        session(['razor_amount' => currencyConversion($plan->price, null, 'INR', 1) * 100]);
        session(['ssl_amount' => currencyConversion($plan->price, null, 'BDT', 1)]);
        session(['plan' => $plan]);
        session(['job_payment_type' => 'package_job']);
        session()->forget('job_request');

        $payment_setting = PaymentSetting::first();
        $manual_payments = ManualPayment::whereStatus(1)->get();

        // midtrans snap token
        if (config('zakirsoft.midtrans_active') && config('zakirsoft.midtrans_merchat_id') && config('zakirsoft.midtrans_client_key') && config('zakirsoft.midtrans_server_key')) {
            $usd = $plan->price;
            $amount = (int) Currency::convert()
                ->from(config('zakirsoft.currency'))
                ->to('IDR')
                ->amount($usd)
                ->round(2)
                ->get();

            $order['order_no'] = uniqid();
            $order['total_price'] = $amount;

            $midtrans = new CreateSnapTokenService($order);
            $snapToken = $midtrans->getSnapToken();

            session([
                'midtrans_details' => [
                    'order_no' => $order['order_no'],
                    'total_price' => $order['total_price'],
                    'snap_token' => $snapToken,
                    'plan_id' => $plan->id,
                ],
            ]);

            session([
                'order_payment' => [
                    'payment_provider' => 'midtrans',
                    'amount' => $amount,
                    'currency_symbol' => 'Rp',
                    'usd_amount' => $usd,
                ],
            ]);
        }

        // Flutterwave Amount
        if (config('zakirsoft.flw_public_key') && config('zakirsoft.flw_secret') && config('zakirsoft.flw_secret_hash') && config('zakirsoft.flw_active')) {
            $flutterwave_amount = currencyConversion($plan->price, null, 'NGN', 1);
        }

        return [
            'plan' => $plan,
            'payment_setting' => $payment_setting,
            'mid_token' => $snapToken ?? null,
            'manual_payments' => $manual_payments,
            'flutterwave_amount' => $flutterwave_amount ?? null,
        ];
    }
}
