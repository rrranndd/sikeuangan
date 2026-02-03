<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        return view('laporan', [
            'bulan' => $request->get('bulan', date('m')),
            'tahun' => $request->get('tahun', date('Y'))
        ]);
    }

    public function ajax(Request $request)
    {
        $bulan  = (int) $request->get('bulan');
        $tahun  = (int) $request->get('tahun');
        $userId = session('user_id');

        $summary = DB::selectOne("
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'pemasukan' THEN nominal END), 0) AS pemasukan,
                COALESCE(SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal END), 0) AS pengeluaran
            FROM transaksi
            WHERE user_id = ?
            AND EXTRACT(MONTH FROM tanggal) = ?
            AND EXTRACT(YEAR FROM tanggal) = ?
        ", [$userId, $bulan, $tahun]);

        $kategori = DB::select("
            SELECT kategori, COALESCE(SUM(nominal), 0) AS total
            FROM transaksi
            WHERE user_id = ?
            AND jenis = 'pengeluaran'
            AND EXTRACT(MONTH FROM tanggal) = ?
            AND EXTRACT(YEAR FROM tanggal) = ?
            GROUP BY kategori
            ORDER BY total DESC
        ", [$userId, $bulan, $tahun]);

        return response()->json([
            'pemasukan'   => $summary->pemasukan,
            'pengeluaran' => $summary->pengeluaran,
            'sisa'        => $summary->pemasukan - $summary->pengeluaran,
            'kategori'    => $kategori
        ]);
    }

    public function exportExcel(Request $request)
    {
        $bulan  = (int) $request->get('bulan');
        $tahun  = (int) $request->get('tahun');
        $userId = session('user_id');

        $data = DB::select("
            SELECT tanggal, jenis, kategori, nominal
            FROM transaksi
            WHERE user_id = ?
            AND EXTRACT(MONTH FROM tanggal) = ?
            AND EXTRACT(YEAR FROM tanggal) = ?
            ORDER BY tanggal ASC
        ", [$userId, $bulan, $tahun]);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=laporan_{$bulan}_{$tahun}.xls");

        echo "Tanggal\tJenis\tKategori\tNominal\n";
        foreach ($data as $d) {
            echo "{$d->tanggal}\t{$d->jenis}\t{$d->kategori}\t{$d->nominal}\n";
        }
        exit;
    }
}
