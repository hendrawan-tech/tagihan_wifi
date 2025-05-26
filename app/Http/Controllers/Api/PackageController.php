<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Package::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }


            $data = $query->orderBy('created_at', 'DESC')->get();

            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'price' => 'required',
                'speed' => 'required',
                'description' => 'nullable',
            ]);

            Package::create($data);

            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function update(Request $request, Package $package)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'price' => 'required',
                'speed' => 'required',
                'description' => 'nullable',
            ]);

            $package->update($data);

            return ResponseFormatter::success($package);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function destroy(Package $package)
    {
        try {
            $package->delete();

            return ResponseFormatter::success($package);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }
}
