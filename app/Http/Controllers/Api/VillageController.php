<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    public function index()
    {
        $data = Village::all();
        return response()->json([
            'message' => 'Berhasil mengabil data desa',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        Village::create($data);

        return response()->json([
            'message' => 'Berhasil menambah data',
        ]);
    }

    public function update(Request $request, Village $village)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        $village->update($data);

        return response()->json([
            'message' => 'Berhasil mengubah data',
        ]);
    }

    public function destroy(Village $village)
    {
        $village->delete();

        return response()->json([
            'message' => 'Berhasil menghapus data',
        ]);
    }
}
