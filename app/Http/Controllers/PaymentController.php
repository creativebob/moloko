<?php

namespace App\Http\Controllers;

use App\Payment;
use App\PaymentsSign;
use App\PaymentsType;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * PaymentController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->input();

        $paymentsSignId = PaymentsSign::where('alias', 'sell')
            ->value('id');
        $data['payments_sign_id'] = $paymentsSignId;

        $payment = Payment::create($data);

        // Получаем все поля, а не только заполненные
        $payment = Payment::with([
            'sign',
            'currency',
            'method'
        ])
            ->find($payment->id);

        return response()->json($payment);
    }

    /**
     * Remove the specified resource from storage.
     * Отмена платежа
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $payment = Payment::destroy($id);
        return response()->json($payment);
    }

    /**
     * Отмена платежа
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel($id)
    {
        $canceledPayment = Payment::find($id);

        $payment = $canceledPayment->replicate();

        $canceledPayment->update([
           'canceled_at' => now()
        ]);

        $payment->canceled_payment_id = $canceledPayment->id;

        $cancelPaymentsSignId = PaymentsSign::where('alias', 'sellReturn')
            ->value('id');
        $payment->payments_sign_id = $cancelPaymentsSignId;

        $payment->registered_at = now();
        $payment->save();

        $canceledPayment = Payment::find($id);

        return response()->json($canceledPayment);
    }
}
