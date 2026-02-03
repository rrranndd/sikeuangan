@extends('layout')

@section('title', 'Laporan | SiKeuangan')

@section('content')

<div class="page-title">
    <h1>Laporan Keuangan</h1>
    <p>Analisis pemasukan & pengeluaran</p>
</div>

<div class="actions">
    <div class="filter">
        <select id="bulan">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $i == $bulan ? 'selected' : '' }}>
                    Bulan {{ $i }}
                </option>
            @endfor
        </select>

        <select id="tahun">
            @for ($y = date('Y') - 5; $y <= date('Y'); $y++)
                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>

        <a id="btnExport"
           class="btn-secondary btn-export"
           href="/laporan/export?bulan={{ $bulan }}&tahun={{ $tahun }}">
            Export Excel
        </a>
    </div>
</div>

<div class="laporan-summary">
    <div>
        <span>Pemasukan</span>
        <strong id="pemasukan">Rp 0</strong>
    </div>
    <div>
        <span>Pengeluaran</span>
        <strong id="pengeluaran">Rp 0</strong>
    </div>
    <div>
        <span>Sisa</span>
        <strong id="sisa">Rp 0</strong>
    </div>
</div>

<div class="card">
    <h3>Grafik Pengeluaran per Kategori</h3>

    <div class="chart-wrapper">
        <canvas id="chartKategori"></canvas>
    </div>
</div>


<div class="card">
    <h3>Rekap per Kategori</h3>

    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tabel-kategori">
            <tr>
                <td colspan="2">Memuat data...</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chart;

function loadLaporan() {
    const bulan = document.getElementById('bulan').value;
    const tahun = document.getElementById('tahun').value;

    fetch(`/laporan/ajax?bulan=${bulan}&tahun=${tahun}`)
        .then(res => res.json())
        .then(data => {

            document.getElementById('pemasukan').innerText =
                'Rp ' + Number(data.pemasukan).toLocaleString('id-ID');

            document.getElementById('pengeluaran').innerText =
                'Rp ' + Number(data.pengeluaran).toLocaleString('id-ID');

            document.getElementById('sisa').innerText =
                'Rp ' + Number(data.sisa).toLocaleString('id-ID');

            let html = '';
            const labels = [];
            const values = [];

            if (data.kategori.length === 0) {
                html = `<tr><td colspan="2">Tidak ada data</td></tr>`;
            } else {
                data.kategori.forEach(k => {
                    html += `
                        <tr>
                            <td>${k.kategori}</td>
                            <td>Rp ${Number(k.total).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                    labels.push(k.kategori);
                    values.push(k.total);
                });
            }

            document.getElementById('tabel-kategori').innerHTML = html;

            document.getElementById('btnExport').href =
                `/laporan/export?bulan=${bulan}&tahun=${tahun}`;

            renderChart(labels, values);
        });
}

function renderChart(labels, data) {
    const canvas = document.getElementById('chartKategori');
    const ctx = canvas.getContext('2d');

    if (chart) chart.destroy();

    if (labels.length === 0) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.font = '14px Arial';
        ctx.fillStyle = '#888';
        ctx.textAlign = 'center';
        ctx.fillText('Belum ada data pengeluaran', canvas.width / 2, canvas.height / 2);
        return;
    }

    chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#1b7f5c',
                    '#2ecc71',
                    '#27ae60',
                    '#16a085',
                    '#95a5a6'
                ]
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', loadLaporan);
document.getElementById('bulan').addEventListener('change', loadLaporan);
document.getElementById('tahun').addEventListener('change', loadLaporan);
</script>
@endpush
