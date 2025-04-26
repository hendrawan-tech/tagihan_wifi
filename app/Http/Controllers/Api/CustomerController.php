<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::all();
        return response()->json([
            'message' => 'Berhasil mengabil data desa',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'village_id' => 'required',
            'package_id' => 'required',
        ]);

        Customer::create($data);

        return response()->json([
            'message' => 'Berhasil menambah data',
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required',
            'village_id' => 'required',
            'package_id' => 'required',
        ]);

        $customer->update($data);

        return response()->json([
            'message' => 'Berhasil mengubah data',
        ]);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data',
        ]);
    }
}
