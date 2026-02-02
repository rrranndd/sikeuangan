@extends('layout')

@section('title', 'Dashboard | SiKeuangan')

@section('content')

<div class="page-title">
    <h1>Dashboard</h1>
    <p id="label-tanggal">
        Ringkasan Keuangan —
        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
    </p>
</div>

<div class="date-filter">
    <label for="tanggal">Tanggal</label>
    <div class="date-input">
        <svg viewBox="0 0 24 24" class="icon-calendar">
            <path d="M7 2v2H5a2 2 0 0 0-2 2v14
                     a2 2 0 0 0 2 2h14
                     a2 2 0 0 0 2-2V6
                     a2 2 0 0 0-2-2h-2V2h-2v2H9V2H7zm12 18H5V9h14v11z"/>
        </svg>

        <input
            type="date"
            id="tanggal"
            value="{{ date('Y-m-d') }}"
        >
    </div>
</div>


<div class="summary">
    <div class="card">
        <h3>Pemasukan</h3>
        <p class="income" id="pemasukan">
            Rp {{ number_format($totalPemasukan,0,',','.') }}
        </p>
    </div>

    <div class="card">
        <h3>Pengeluaran</h3>
        <p class="expense" id="pengeluaran">
            Rp {{ number_format($totalPengeluaran,0,',','.') }}
        </p>
    </div>

    <div class="card">
        <h3>Sisa</h3>
        <p
            id="sisa"
            class="{{ $sisaKeuangan >= 0 ? 'income' : 'expense' }}"
        >
            Rp {{ number_format($sisaKeuangan,0,',','.') }}
        </p>
    </div>
</div>

<div class="card">
    <h3>Transaksi Tanggal Ini</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody id="tabel-transaksi">
                @forelse ($transaksi as $t)
                    <tr>
                        <td>{{ $t->tanggal }}</td>
                        <td>{{ ucfirst($t->jenis) }}</td>
                        <td>{{ $t->kategori }}</td>
                        <td>
                            Rp {{ number_format($t->nominal,0,',','.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            Belum ada transaksi pada tanggal ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function loadDashboard() {
    const tanggal = document.getElementById('tanggal').value;

    fetch(`/dashboard/ajax?tanggal=${tanggal}`)
        .then(res => res.json())
        .then(data => {

            document.getElementById('pemasukan').innerText =
                'Rp ' + Number(data.pemasukan).toLocaleString('id-ID');

            document.getElementById('pengeluaran').innerText =
                'Rp ' + Number(data.pengeluaran).toLocaleString('id-ID');

            document.getElementById('sisa').innerText =
                'Rp ' + Number(data.sisa).toLocaleString('id-ID');

            document.getElementById('label-tanggal').innerText =
                'Ringkasan Keuangan — ' + data.label_tanggal;

            let html = '';
            if (data.transaksi.length === 0) {
                html = `
                    <tr>
                        <td colspan="4">
                            Belum ada transaksi pada tanggal ini
                        </td>
                    </tr>
                `;
            } else {
                data.transaksi.forEach(t => {
                    html += `
                        <tr>
                            <td>${t.tanggal}</td>
                            <td>${t.jenis}</td>
                            <td>${t.kategori}</td>
                            <td>
                                Rp ${Number(t.nominal).toLocaleString('id-ID')}
                            </td>
                        </tr>
                    `;
                });
            }

            document.getElementById('tabel-transaksi').innerHTML = html;
        });
}

/* EVENT */
document
    .getElementById('tanggal')
    .addEventListener('change', loadDashboard);
</script>
@endpush
