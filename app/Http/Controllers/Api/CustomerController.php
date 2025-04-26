<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::all();
        return ResponseFormatter::success($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'village_id' => 'required',
            'package_id' => 'required',
        ]);

        Customer::create($data);

        return ResponseFormatter::success($data);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required',
            'village_id' => 'required',
            'package_id' => 'required',
        ]);

        $customer->update($data);

        return ResponseFormatter::success($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return ResponseFormatter::success($customer);
    }
}
