<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function getAll()
    {
        $data = Invoice::orderBy('created_at', 'DESC')->get();

        return ResponseFormatter::success($data);
    }

    public function getByCustomer($customer)
    {
        $data = Invoice::where('customer_id', $customer)->orderBy('created_at', 'DESC')->get();

        return ResponseFormatter::success($data);
    }

    public function bulkInvoice($token)
    {
        if ($token == 'NaviaNet123') {
            $data = Customer::all();

            foreach ($data as $customer) {
                $datePart = date("Ymd");
                $randomPart = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $code = "INV-$datePart-$randomPart";

                Invoice::create([
                    'code' => $code,
                    'price' => $customer->package->price,
                    'price_in' => 0,
                    'status' => 'Belum Lunas',
                ]);
            }

            return response()->json([
                'message' => 'Berhasil mengambil data',
            ]);
        } else {
            return response()->json([
                'message' => 'Token tidak valid',
            ], 403);
        }
    }

    public function payment(Request $request)
    {
        $data = $request->validate([
            'code' => 'required',
            'price' => 'required',
        ]);

        $invoice =  Invoice::where('code', $data['code'])->first();
        $remaining = $invoice->price - $invoice->price_in;
        $status = ($data['price'] >= $remaining) ? 'Lunas' : 'Belum Lunas';

        $invoice->update([
            'status' => $status,
            'price_in' => $invoice->price_in + $data['price'],
        ]);

        return ResponseFormatter::success($invoice);
    }
}
