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
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');
        $userId = session('user_id');

        $summary = DB::selectOne("
            SELECT
            SUM(CASE WHEN jenis='pemasukan' THEN nominal ELSE 0 END) pemasukan,
            SUM(CASE WHEN jenis='pengeluaran' THEN nominal ELSE 0 END) pengeluaran
            FROM transaksi
            WHERE user_id = ?
            AND MONTH(tanggal)=?
            AND YEAR(tanggal)=?
        ", [$userId, $bulan, $tahun]);

        $kategori = DB::select("
            SELECT kategori, SUM(nominal) total
            FROM transaksi
            WHERE user_id = ?
            AND MONTH(tanggal)=?
            AND YEAR(tanggal)=?
            GROUP BY kategori
            ORDER BY total DESC
        ", [$userId, $bulan, $tahun]);

        return response()->json([
            'pemasukan'   => $summary->pemasukan ?? 0,
            'pengeluaran' => $summary->pengeluaran ?? 0,
            'sisa'        => ($summary->pemasukan ?? 0) - ($summary->pengeluaran ?? 0),
            'kategori'    => $kategori
        ]);
    }

    public function exportExcel(Request $request)
    {
        $bulan  = $request->get('bulan');
        $tahun  = $request->get('tahun');
        $userId = session('user_id');

        $data = DB::select("
            SELECT tanggal, jenis, kategori, nominal
            FROM transaksi
            WHERE user_id = ?
            AND MONTH(tanggal)=?
            AND YEAR(tanggal)=?
            ORDER BY tanggal
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
