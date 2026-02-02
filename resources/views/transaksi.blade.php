@extends('layout')

@section('title', 'Transaksi | SiKeuangan')

@section('content')

<div class="page-title">
    <h1>Transaksi</h1>
    <p>Kelola pemasukan & pengeluaran</p>
</div>

<div class="actions">
    <button class="btn-primary" id="btnTambah">
        <svg viewBox="0 0 24 24">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Tambah Transaksi
    </button>

    <div class="filter">
        <select id="bulan">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $i == date('m') ? 'selected' : '' }}>
                    Bulan {{ $i }}
                </option>
            @endfor
        </select>

        <select id="tahun">
            @for ($y = date('Y') - 5; $y <= date('Y'); $y++)
                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
    </div>
</div>

<div class="card">
    <h3>Daftar Transaksi</h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Nominal</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-transaksi">
                <tr>
                    <td colspan="5">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="modalTransaksi">
    <div class="modal-content fancy">

        <div class="modal-header">
            <h3 id="modalTitle">Tambah Transaksi</h3>
            <button type="button" class="modal-close" id="btnClose">
                <svg viewBox="0 0 24 24">
                    <path d="M6 6l12 12M18 6l-12 12"/>
                </svg>
            </button>
        </div>

        <form id="formTransaksi" class="form-grid">
            @csrf
            <input type="hidden" name="id" id="transaksiId">

            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required>
            </div>

            <div class="form-group">
                <label>Jenis Transaksi</label>
                <select name="jenis" id="jenis" required>
                    <option value="">Pilih jenis</option>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                </select>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <input type="text" name="kategori" id="kategori" required>
            </div>

            <div class="form-group">
                <label>Nominal</label>
                <input type="number" name="nominal" id="nominal" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="btnBatal">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const modal = document.getElementById('modalTransaksi');
const form  = document.getElementById('formTransaksi');

let transaksiCache = [];

const openModal  = () => modal.classList.add('show');
const closeModal = () => modal.classList.remove('show');

document.getElementById('btnTambah').onclick = () => {
    form.reset();
    document.getElementById('transaksiId').value = '';
    document.getElementById('modalTitle').innerText = 'Tambah Transaksi';
    openModal();
};

document.getElementById('btnClose').onclick = closeModal;
document.getElementById('btnBatal').onclick = closeModal;

function loadTransaksi() {
    const bulan = document.getElementById('bulan').value;
    const tahun = document.getElementById('tahun').value;

    fetch(`/transaksi/ajax?bulan=${bulan}&tahun=${tahun}`)
        .then(res => res.json())
        .then(data => {
            transaksiCache = data;
            let html = '';

            if (!data || data.length === 0) {
                html = `<tr><td colspan="5">Tidak ada data</td></tr>`;
            } else {
                data.forEach(t => {
                    html += `
                        <tr>
                            <td>${t.tanggal}</td>
                            <td>${t.jenis}</td>
                            <td>${t.kategori}</td>
                            <td>Rp ${Number(t.nominal).toLocaleString('id-ID')}</td>
                            <td class="aksi">
                                <button class="btn-edit" onclick="editTransaksiById(${t.id})" title="Edit">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                    </svg>
                                </button>
                                <button class="btn-delete" onclick="hapusTransaksi(${t.id})" title="Hapus">
                                    <svg viewBox="0 0 24 24">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6"/>
                                        <path d="M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            document.getElementById('tabel-transaksi').innerHTML = html;
        });
}

document.addEventListener('DOMContentLoaded', loadTransaksi);
document.getElementById('bulan').onchange = loadTransaksi;
document.getElementById('tahun').onchange = loadTransaksi;

function editTransaksiById(id) {
    const t = transaksiCache.find(x => x.id === id);
    if (!t) return;

    document.getElementById('modalTitle').innerText = 'Edit Transaksi';
    document.getElementById('transaksiId').value = t.id;
    document.getElementById('tanggal').value = t.tanggal;
    document.getElementById('jenis').value = t.jenis;
    document.getElementById('kategori').value = t.kategori;
    document.getElementById('nominal').value = t.nominal;

    openModal();
}

function hapusTransaksi(id) {
    if (!confirm('Hapus transaksi ini?')) return;

    fetch('/transaksi/delete', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id })
    })
    .then(() => loadTransaksi());
}

form.addEventListener('submit', function(e){
    e.preventDefault();

    const id  = document.getElementById('transaksiId').value;
    const url = id ? '/transaksi/update' : '/transaksi/store';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
        },
        body: new FormData(form)
    })
    .then(() => {
        closeModal();
        loadTransaksi();
    });
});
</script>
@endpush
