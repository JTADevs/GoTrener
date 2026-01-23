<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\PaymentInterface;

class PaymentsController extends Controller
{
    protected $payment;
    public function __construct(PaymentInterface $payment) {
        $this->payment = $payment;
    }

    public function promotion(Request $request)
    {

        $this->payment->promotion($request->all());
    }
}
