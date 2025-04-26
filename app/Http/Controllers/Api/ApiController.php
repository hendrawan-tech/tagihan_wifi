<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getByVillage($village)
    {
        $data = Customer::where('village_id', $village)->get();

        return response()->json([
            'message' => 'Berhasil mengambil data',
            'data' => $data,
        ]);
    }
}
