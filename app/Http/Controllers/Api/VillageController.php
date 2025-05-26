<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Village::query();

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search != '') {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $data = $query->orderBy('name', 'ASC')->get();

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
            ]);

            Village::create($data);

            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function update(Request $request, Village $village)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
            ]);

            $village->update($data);

            return ResponseFormatter::success($village);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    public function destroy(Village $village)
    {
        try {
            $village->delete();

            return ResponseFormatter::success($village);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }
}
