<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index()
    {
        $data = Village::all();
        return ResponseFormatter::success($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        Village::create($data);

        return ResponseFormatter::success($data);
    }

    public function update(Request $request, Village $village)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        $village->update($data);

        return ResponseFormatter::success($village);
    }

    public function destroy(Village $village)
    {
        $village->delete();

        return ResponseFormatter::success($village);
    }
}
