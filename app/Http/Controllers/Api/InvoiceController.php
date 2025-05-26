<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\History;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $query = Invoice::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('customer_id') && $request->customer_id != '') {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->has('user_id') && $request->user_id != '') {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%' . $search . '%');
                        });
                });
            }


            $perPage = $request->get('per_page', 10);
            $query->with(['customer.village', 'user']);
            $data = $query->orderBy('created_at', 'DESC')->paginate($perPage);

            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $data = Invoice::where('id', $id)->with(['customer.village', 'customer.package', 'user'])->first();
            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
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
                    'customer_id' => $customer->id,
                ]);
            }

            return response()->json([
                'message' => 'Berhasil membuat data invoice',
            ]);
        } else {
            return response()->json([
                'message' => 'Token tidak valid',
            ], 403);
        }
    }

    public function createInvoice(Request $request)
    {
        try {
            $data = $request->validate([
                'customer_id' => 'required',
                'created_at' => 'required',
            ]);

            $customer = Customer::where('id', $data['customer_id'])->first();

            $datePart = date("Ymd");
            $randomPart = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $code = "INV-$datePart-$randomPart";

            Invoice::create([
                'code' => $code,
                'price' => $customer->package->price,
                'price_in' => 0,
                'created_at' => $data['created_at'],
                'status' => 'Belum Lunas',
                'customer_id' => $customer->id,
            ]);

            return response()->json([
                'message' => 'Berhasil membuat data invoice',
            ]);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function payment(Request $request)
    {
        try {
            $data = $request->validate([
                'code' => 'required',
                'price' => 'required',
                'discount' => 'nullable',
            ]);

            $discount = $data['discount'] == null ? '0' : $data['discount'];
            $invoice =  Invoice::where('code', $data['code'])->first();
            $remaining = $invoice->price - $invoice->price_in;
            $status = ($data['price'] >= $remaining - $discount) ? 'Lunas' : 'Belum Lunas';

            $invoice->update([
                'status' => $status,
                'price_in' => $invoice->price_in + $data['price'],
                'user_id' => Auth::user()->id,
                'discount' => $discount,
            ]);

            History::create([
                'price' => $data['price'],
                'type' => 'in',
                'description' => 'melakukan penagihan kepada ' . $invoice->customer->name . ' (No. Tagihan: ' . $invoice->code . ')',
                'user_id' => Auth::user()->id,
            ]);

            return ResponseFormatter::success($invoice);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function confirm(Request $request)
    {
        try {
            $data = $request->validate([
                'code' => 'required',
            ]);

            $invoice =  Invoice::where('code', $data['code'])->first();

            $invoice->update([
                'status' => 'Konfirmasi',
                'user_id' => Auth::user()->id,
            ]);

            return ResponseFormatter::success($invoice);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }
}
