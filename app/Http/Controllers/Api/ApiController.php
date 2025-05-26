<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\History;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $counts = Invoice::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            $invoiceLunas = $counts->get('Lunas', 0);
            $invoiceBelumLunas = $counts->get('Belum Lunas', 0);
            $invoiceCount = $counts->sum();
            $customerCount = Customer::count();

            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);

            // Ambil data detail
            $dataQuery = History::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereIn('type', ['in', 'out']);

            if (Auth::user()->role == 'Teknisi') {
                $dataQuery->where('user_id', Auth::user()->id);
            }

            $data = $dataQuery->get(); // simpan hasilnya ke $data

            // Hitung total pemasukan dan pengeluaran
            $totalsQuery = History::selectRaw('type, SUM(CAST(price AS UNSIGNED)) as total')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereIn('type', ['in', 'out']);

            if (Auth::user()->role == 'Teknisi') {
                $totalsQuery->where('user_id', Auth::user()->id); // <- diperbaiki jadi $totalsQuery
            }

            $totals = $totalsQuery->groupBy('type')->pluck('total', 'type');


            // Ambil nilai, default 0 kalau tidak ada
            $totalIn = $totals->get('in', 0);
            $totalOut = $totals->get('out', 0);

            // Hitung selisih
            $difference = $totalIn - $totalOut;

            $perWeekQuery = History::selectRaw("FLOOR((DAY(created_at) - 1) / 7) + 1 as week_of_month,type,SUM(CAST(price AS UNSIGNED)) as total")
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereIn('type', ['in', 'out']);

            if (Auth::user()->role == 'Teknisi') {
                $perWeekQuery->where('user_id', Auth::user()->id);
            }

            $perWeek = $perWeekQuery
                ->groupBy('week_of_month', 'type')
                ->get()
                ->groupBy('week_of_month'); // hasilnya: Collection<int, Collection>



            return ResponseFormatter::success([
                'counter' => [
                    'invoice_lunas_count' => $invoiceLunas,
                    'invoice_belum_lunas_count' => $invoiceBelumLunas,
                    'invoice_count' => $invoiceCount,
                    'customer_count' => $customerCount
                ],
                'summaries' => [
                    'summary' => [
                        'in' => $totalIn,
                        'out' => $totalOut,
                        'diff' => $difference,
                    ],
                    'data' => $data,
                    'perWeek' => $perWeek,
                ],
            ]);
        } catch (\Throwable $th) {
            return ResponseFormatter::error(null, $th->getMessage());
        }
    }
}
