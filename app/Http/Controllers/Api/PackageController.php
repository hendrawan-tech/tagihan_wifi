<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $data = Package::all();
        return response()->json([
            'message' => 'Berhasil mengabil data desa',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'speed' => 'required',
            'description' => 'nullable',
        ]);

        Package::create($data);

        return response()->json([
            'message' => 'Berhasil menambah data',
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'speed' => 'required',
            'description' => 'nullable',
        ]);

        $package->update($data);

        return response()->json([
            'message' => 'Berhasil mengubah data',
        ]);
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data',
        ]);
    }
}
