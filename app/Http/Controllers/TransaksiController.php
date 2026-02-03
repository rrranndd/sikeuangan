<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        $tanggal = date('Y-m-d');
        $userId  = session('user_id');

        $bulan = Carbon::parse($tanggal)->month;
        $tahun = Carbon::parse($tanggal)->year;

        $summary = DB::selectOne("
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'pemasukan' THEN nominal END), 0) AS pemasukan,
                COALESCE(SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal END), 0) AS pengeluaran
            FROM transaksi
            WHERE user_id = ?
            AND EXTRACT(MONTH FROM tanggal) = ?
            AND EXTRACT(YEAR FROM tanggal) = ?
            AND tanggal <= ?
        ", [$userId, $bulan, $tahun, $tanggal]);

        $transaksi = DB::select("
            SELECT tanggal, jenis, kategori, nominal
            FROM transaksi
            WHERE user_id = ?
            AND tanggal = ?
            ORDER BY id DESC
        ", [$userId, $tanggal]);

        return view('dashboard', [
            'tanggal' => $tanggal,
            'totalPemasukan' => $summary->pemasukan,
            'totalPengeluaran' => $summary->pengeluaran,
            'sisaKeuangan' => $summary->pemasukan - $summary->pengeluaran,
            'transaksi' => $transaksi
        ]);
    }

    public function ajaxDashboardHarian(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $userId  = session('user_id');

        $pemasukanHariIni = DB::selectOne("
            SELECT COALESCE(SUM(nominal), 0) AS total
            FROM transaksi
            WHERE user_id = ?
            AND jenis = 'pemasukan'
            AND tanggal = ?
        ", [$userId, $tanggal])->total;

        $pengeluaranHariIni = DB::selectOne("
            SELECT COALESCE(SUM(nominal), 0) AS total
            FROM transaksi
            WHERE user_id = ?
            AND jenis = 'pengeluaran'
            AND tanggal = ?
        ", [$userId, $tanggal])->total;

        $saldoSebelumnya = DB::selectOne("
            SELECT COALESCE(
                SUM(
                    CASE
                        WHEN jenis = 'pemasukan' THEN nominal
                        WHEN jenis = 'pengeluaran' THEN -nominal
                    END
                ), 0
            ) AS saldo
            FROM transaksi
            WHERE user_id = ?
            AND tanggal < ?
        ", [$userId, $tanggal])->saldo;

        $sisa = $saldoSebelumnya + $pemasukanHariIni - $pengeluaranHariIni;

        $transaksi = DB::select("
            SELECT tanggal, jenis, kategori, nominal
            FROM transaksi
            WHERE user_id = ?
            AND tanggal = ?
            ORDER BY id DESC
        ", [$userId, $tanggal]);

        return response()->json([
            'pemasukan' => $pemasukanHariIni,
            'pengeluaran' => $pengeluaranHariIni,
            'sisa' => $sisa,
            'label_tanggal' => Carbon::parse($tanggal)->translatedFormat('d F Y'),
            'transaksi' => $transaksi
        ]);
    }

    public function transaksiPage()
    {
        return view('transaksi');
    }

    public function ajaxTransaksi(Request $request)
    {
        $bulan  = $request->query('bulan');
        $tahun  = $request->query('tahun');
        $userId = session('user_id');

        $transaksi = DB::select("
            SELECT id, tanggal, jenis, kategori, nominal
            FROM transaksi
            WHERE user_id = ?
            AND EXTRACT(MONTH FROM tanggal) = ?
            AND EXTRACT(YEAR FROM tanggal) = ?
            ORDER BY tanggal DESC
        ", [$userId, $bulan, $tahun]);

        return response()->json($transaksi);
    }

    public function store(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'tanggal'  => 'required|date',
            'jenis'    => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string',
            'nominal'  => 'required|numeric'
        ]);

        DB::insert("
            INSERT INTO transaksi (user_id, tanggal, jenis, kategori, nominal)
            VALUES (?, ?, ?, ?, ?)
        ", [
            session('user_id'),
            $request->tanggal,
            $request->jenis,
            $request->kategori,
            $request->nominal
        ]);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'tanggal'  => 'required|date',
            'jenis'    => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string',
            'nominal'  => 'required|numeric'
        ]);

        DB::update("
            UPDATE transaksi
            SET tanggal = ?, jenis = ?, kategori = ?, nominal = ?
            WHERE id = ?
            AND user_id = ?
        ", [
            $request->tanggal,
            $request->jenis,
            $request->kategori,
            $request->nominal,
            $request->id,
            session('user_id')
        ]);

        return response()->json(['status' => 'success']);
    }

    public function delete(Request $request)
    {
        DB::delete("
            DELETE FROM transaksi
            WHERE id = ?
            AND user_id = ?
        ", [
            $request->id,
            session('user_id')
        ]);

        return response()->json(['status' => 'deleted']);
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $data = DB::select("
            SELECT tanggal, jenis, kategori, nominal
            FROM transaksi
            WHERE EXTRACT(MONTH FROM tanggal) = ?
            AND EXTRACT(YEAR FROM tanggal) = ?
            ORDER BY tanggal ASC
        ", [$bulan, $tahun]);

        $filename = "transaksi_{$bulan}_{$tahun}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Jenis', 'Kategori', 'Nominal']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->tanggal,
                    ucfirst($row->jenis),
                    $row->kategori,
                    $row->nominal
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
