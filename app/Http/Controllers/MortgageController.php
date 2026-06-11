<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MortgageController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'price'        => 'required|numeric|min:0',
            'downpayment'  => 'required|numeric|min:0',
            'years'        => 'required|integer|min:1|max:30',
        ]);

        $price       = $request->price;
        $downpayment = $request->downpayment;
        $years       = $request->years;

        if ($downpayment >= $price) {
            return response()->json([
                'message' => 'Downpayment ne može biti veći od cene nekretnine.',
            ], 422);
        }

        
        $response = Http::get('https://open.er-api.com/v6/latest/EUR');

        if ($response->failed()) {
            return response()->json([
                'message' => 'Nije moguće dohvatiti kurs valute.',
            ], 500);
        }

        $exchangeRate = $response->json()['rates']['RSD'] ?? 117;

        
        $loanAmount    = $price - $downpayment;
        $annualRate    = 0.035; 
        $monthlyRate   = $annualRate / 12;
        $numPayments   = $years * 12;

        
        $monthlyPayment = $loanAmount * 
            ($monthlyRate * pow(1 + $monthlyRate, $numPayments)) / 
            (pow(1 + $monthlyRate, $numPayments) - 1);

        return response()->json([
            'loan_amount'          => round($loanAmount, 2),
            'monthly_payment_eur'  => round($monthlyPayment, 2),
            'monthly_payment_rsd'  => round($monthlyPayment * $exchangeRate, 2),
            'exchange_rate'        => round($exchangeRate, 2),
            'total_payment_eur'    => round($monthlyPayment * $numPayments, 2),
            'annual_interest_rate' => '3.5%',
            'years'                => $years,
        ]);
    }
}