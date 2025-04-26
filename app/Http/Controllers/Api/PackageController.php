<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $data = Package::all();
        return ResponseFormatter::success($data);
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

        return ResponseFormatter::success($data);
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

        return ResponseFormatter::success($package);
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return ResponseFormatter::success($package);
    }
}
