<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use App\Models\Tenant\Installment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function view($type) {
        if(in_array($type, ['pending','charged','all'])){
            return view('Tenant.installment.view', compact('type'));
        }
        return view('403');
    }
    public function delete(int $id) {
        $installment = Installment::findOrFail($id);
        $installment->delete();
        return response()->json(['message' => true]);
    }

    public function edit(int $id) {
        $installment = Installment::findOrFail($id);
        return view('Tenant.installment.edit', compact('installment'));
    }

    public function editAction(int $id, Request $request) {
        $installment = Installment::findOrFail($id);
        $request->validate([
            'charge_amount' => ['required', 'numeric', 'gt:0'],
            'pay_amount' => ['required', 'numeric', 'gt:0'],
            'charge_date' => 'required',
            'pay_date' => 'required',
        ]);
        // Cleaning Data
        $pay_date = Carbon::parse($request->pay_date);
        $charge_date = Carbon::parse($request->charge_date);
        $charge_amount = intval($request->charge_amount);
        $pay_amount =  intval($request->pay_amount);
        // Updating Values
        $installment->pay_date = $pay_date;
        $installment->charge_date = $charge_date;
        $installment->charge_amount = $charge_amount;
        $installment->pay_amount = $pay_amount;
        $installment->save();
        return response()->json($installment);
    }
}
