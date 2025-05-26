<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }

            $perPage = $request->get('per_page', 10);
            $query->with(['village', 'package'])
                ->withCount(['invoicesLunas', 'invoicesBelumLunas', 'invoicesKonfirmasi']);
            $data = $query->orderBy('name', 'ASC')->paginate($perPage);

            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        try {
            $customer->load(['village', 'package']);
            $customer->loadCount(['invoicesLunas', 'invoicesBelumLunas', 'invoicesKonfirmasi']);

            return ResponseFormatter::success($customer);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'village_id' => 'required',
                'package_id' => 'required',
            ]);

            Customer::create($data);

            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function update(Request $request, Customer $customer)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'village_id' => 'required',
                'package_id' => 'required',
            ]);

            $customer->update($data);

            return ResponseFormatter::success($customer);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            return ResponseFormatter::success($customer);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }
}
