<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Operational;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OperationalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Operational::query();

            if (Auth::user()->role !== 'Admin') {
                $query->where('user_id', Auth::user()->id);
            }

            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('user_id') && $request->user_id != '') {
                $query->where('user_id', $request->user_id);
            }

            $perPage = $request->get('per_page', 10);
            $query->with(['user']);
            $data = $query->orderBy('created_at', 'DESC')->paginate($perPage);

            return ResponseFormatter::success($data);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'status' => 'required',
                'price' => 'required|numeric',
                'description' => 'required|min:5',
            ]);
            $data['user_id'] = Auth::user()->id;

            $descInput = $data['description'];
            $status = $data['status'];

            switch ($status) {
                case 'Operasional':
                    $description = "melakukan pembelian operasional berupa $descInput";
                    break;
                case 'Alat':
                    $description = "melakukan pembelian alat berupa $descInput";
                    break;
                case 'Gaji':
                    $description = "memberikan gaji kepada $descInput";
                    break;
                case 'Setoran':
                    $description = "melakukan setoran pembayaran ke $descInput";
                    break;
            }
            $data['description'] = $description;

            $op = Operational::create($data);

            History::create([
                'type' => 'out',
                'price' => $op->price,
                'description' => $op->description,
                'user_id' => $op->user_id,
            ]);

            return ResponseFormatter::success();
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Operational $operational)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operational $operational)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Operational $operational)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operational $operational)
    {
        //
    }
}
