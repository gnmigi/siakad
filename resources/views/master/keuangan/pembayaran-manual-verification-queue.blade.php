@extends('core-themes.core-backpage')

@php
    $menus = 'Master Keuangan';
    $pages = 'Verifikasi Transfer Manual';
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Antrean Verifikasi Bukti Transfer</h3>
                <a href="{{ route('keuangan.pembayaran-manual-index') }}" class="btn btn-outline-primary btn-sm">
                    Kembali ke Pembayaran Manual
                </a>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama/NIM/kode...">
                    </div>
                    <div class="col-md-3">
                        <select id="statusFilter" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="resetFilterBtn" type="button" class="btn btn-outline-secondary w-100">Reset</button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    <input type="text" id="bulkApproveNotes" class="form-control" style="max-width: 260px;" placeholder="Catatan ACC massal (opsional)">
                    <input type="text" id="bulkRejectReason" class="form-control" style="max-width: 260px;" placeholder="Alasan tolak massal">
                    <button type="button" class="btn btn-success btn-sm" onclick="submitBulkAction(event, 'approve')">ACC Terpilih</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="submitBulkAction(event, 'reject')">Tolak Terpilih</button>
                </div>

                <form method="POST" id="bulkApproveForm" action="{{ route('keuangan.pembayaran-manual-verification-approve-bulk') }}" class="d-none">
                    @csrf
                </form>

                <form method="POST" id="bulkRejectForm" action="{{ route('keuangan.pembayaran-manual-verification-reject-bulk') }}" class="d-none">
                    @csrf
                </form>

                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th style="width:40px;"><input type="checkbox" id="checkAllPending"></th>
                                <th>Kode Verifikasi</th>
                                <th>Mahasantri</th>
                                <th>Transaksi</th>
                                <th>Jumlah</th>
                                <th>Bukti</th>
                                <th>Status</th>
                                <th style="width: 280px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="verificationTableBody">
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- <div id="ajaxDebugPanel" class="alert alert-warning py-2 px-3 mb-3" style="display:none; font-size:12px; white-space:pre-wrap;"></div> -->

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small" id="paginationInfo">-</div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="prevPageBtn">Sebelumnya</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="nextPageBtn">Berikutnya</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const VERIFICATION_DATA_URL = `${window.location.origin}/keuangan/pembayaran-manual/verifikasi/data`;
const VERIFICATION_DATA_URL_FALLBACK = `${window.location.origin}/keuangan/pembayaran-manual/verifikasi/data`;
const VERIFICATION_SHOW_URL_BASE = "{{ url('keuangan/pembayaran-manual') }}";
const APPROVE_URL_TEMPLATE = "{{ url('keuangan/pembayaran-manual/verifikasi/__ID__/approve') }}";
const REJECT_URL_TEMPLATE = "{{ url('keuangan/pembayaran-manual/verifikasi/__ID__/reject') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

let currentPage = 1;
let lastPage = 1;
let debounceTimer = null;

function setDebug(message, isError = false) {
    const panel = document.getElementById('ajaxDebugPanel');
    if (!panel) return;
    panel.style.display = 'block';
    panel.classList.remove('alert-warning', 'alert-danger', 'alert-success');
    panel.classList.add(isError ? 'alert-danger' : 'alert-warning');
    panel.textContent = message;
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '<')
        .replaceAll('>', '>')
        .replaceAll('"', '"')
        .replaceAll("'", '&#39;');
}

function statusBadge(status, label) {
    const s = (status || '').toLowerCase();
    if (s === 'pending') return `<span class="badge bg-warning-lt text-warning">${escapeHtml(label)}</span>`;
    if (s === 'approved') return `<span class="badge bg-success-lt text-success">${escapeHtml(label)}</span>`;
    if (s === 'rejected') return `<span class="badge bg-danger-lt text-danger">${escapeHtml(label)}</span>`;
    return `<span class="badge bg-secondary-lt text-secondary">${escapeHtml(label)}</span>`;
}

function renderRows(items) {
    const tbody = document.getElementById('verificationTableBody');
    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data verifikasi.</td></tr>`;
        return;
    }

    tbody.innerHTML = items.map(item => {
        const isPending = (item.status || '').toLowerCase() === 'pending';
        const checkbox = isPending
            ? `<input type="checkbox" class="verification-check" value="${item.id}">`
            : '';

        const proofHtml = item.proof_url
            ? `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;min-width:86px;">
                    <img src="${item.proof_url}" alt="Bukti Transfer"
                         style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1px solid #ddd;display:block;">
                    <a href="${item.proof_url}" target="_blank" class="btn btn-sm btn-outline-secondary"
                       style="width:72px;padding:2px 0;text-align:center;">Lihat Full</a>
                </div>
            `
            : `<span class="text-muted">Tidak ada</span>`;

        const transaksi = (item.transaction_code && item.transaction_id)
            ? `<a href="${VERIFICATION_SHOW_URL_BASE}/${item.transaction_id}" class="text-decoration-none">${escapeHtml(item.transaction_code)}</a>`
            : '-';

        const aksiHtml = isPending
            ? `
                <form action="${APPROVE_URL_TEMPLATE.replace('__ID__', item.id)}" method="POST" class="mb-2">
                    <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                    <input type="text" name="notes" class="form-control form-control-sm mb-2" placeholder="Catatan ACC (opsional)">
                    <button class="btn btn-sm btn-success w-100" type="submit">ACC (Verifikasi)</button>
                </form>
                <form action="${REJECT_URL_TEMPLATE.replace('__ID__', item.id)}" method="POST">
                    <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                    <input type="text" name="reason" class="form-control form-control-sm mb-2" placeholder="Alasan tolak" required>
                    <button class="btn btn-sm btn-danger w-100" type="submit">Tolak</button>
                </form>
            `
            : `<div class="text-muted small">Sudah diproses</div>`;

        return `
            <tr>
                <td>${checkbox}</td>
                <td>${escapeHtml(item.verification_code || '-')}</td>
                <td>
                    <div class="fw-bold">${escapeHtml(item.mahasiswa_name || '-')}</div>
                    <div class="text-muted small">${escapeHtml(item.mahasiswa_nim || '-')}</div>
                </td>
                <td>
                    ${transaksi}
                    <div class="text-primary small fw-semibold">${escapeHtml(item.tagihan_name || 'Tagihan Kuliah')}</div>
                    <div class="text-muted small">${escapeHtml(item.created_at || '-')}</div>
                </td>

                <td>${escapeHtml(item.amount_formatted || 'Rp 0')}</td>
                <td>${proofHtml}</td>
                <td>${statusBadge(item.status, item.status_label || item.status || '-')}</td>
                <td>${aksiHtml}</td>
            </tr>
        `;
    }).join('');
}

async function loadData(page = 1) {
    const search = document.getElementById('searchInput').value.trim();
    const status = document.getElementById('statusFilter').value.trim();

    const params = new URLSearchParams();
    params.set('page', page);
    if (search) params.set('search', search);
    if (status) params.set('status', status);

    const tbody = document.getElementById('verificationTableBody');
    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">Memuat data...</td></tr>`;

    try {
        const primaryUrl = `${VERIFICATION_DATA_URL}?${params.toString()}`;
        const fallbackUrl = `${VERIFICATION_DATA_URL_FALLBACK}?${params.toString()}`;

        setDebug(`AJAX DEBUG\nPrimary URL: ${primaryUrl}\nFallback URL: ${fallbackUrl}\nStatus filter: ${status || '-'}\nSearch: ${search || '-'}`);

        let response = await fetch(primaryUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        });

        let usedUrl = primaryUrl;

        if (!response.ok || !String(response.headers.get('content-type') || '').includes('application/json')) {
            response = await fetch(fallbackUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            usedUrl = fallbackUrl;
        }

        const raw = await response.text();
        let result = null;
        try {
            result = JSON.parse(raw);
        } catch (jsonErr) {
            const preview = raw ? raw.substring(0, 300) : '(empty)';
            console.error('Verification AJAX non-JSON response:', raw);
            setDebug(`AJAX DEBUG ERROR\nUsed URL: ${usedUrl}\nHTTP: ${response.status}\nContent-Type: ${response.headers.get('content-type')}\nJSON parse failed.\nResponse preview:\n${preview}`, true);
            throw new Error('Response bukan JSON');
        }

        if (!response.ok || !result || !result.success) {
            console.error('Verification AJAX failed response:', result);
            setDebug(`AJAX DEBUG ERROR\nUsed URL: ${usedUrl}\nHTTP: ${response.status}\nResult success: ${result?.success}\nMessage: ${result?.message || '-'}`, true);
            throw new Error('Gagal memuat data');
        }

        setDebug(`AJAX DEBUG OK\nUsed URL: ${usedUrl}\nHTTP: ${response.status}\nRows: ${(result.data || []).length}\nPage: ${result.pagination?.current_page || '-'} / ${result.pagination?.last_page || '-'}`, false);

        renderRows(result.data || []);
        currentPage = result.pagination?.current_page || 1;
        lastPage = result.pagination?.last_page || 1;

        document.getElementById('paginationInfo').textContent =
            `Menampilkan ${result.pagination?.from || 0}-${result.pagination?.to || 0} dari ${result.pagination?.total || 0} data`;

        document.getElementById('prevPageBtn').disabled = currentPage <= 1;
        document.getElementById('nextPageBtn').disabled = currentPage >= lastPage;
        document.getElementById('checkAllPending').checked = false;
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadData(1);

    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => loadData(1), 400);
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        loadData(1);
    });

    document.getElementById('resetFilterBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        loadData(1);
    });

    document.getElementById('prevPageBtn').addEventListener('click', function() {
        if (currentPage > 1) loadData(currentPage - 1);
    });

    document.getElementById('nextPageBtn').addEventListener('click', function() {
        if (currentPage < lastPage) loadData(currentPage + 1);
    });

    document.getElementById('checkAllPending').addEventListener('change', function() {
        document.querySelectorAll('.verification-check').forEach(cb => cb.checked = this.checked);
    });
});

function submitBulkAction(event, type) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const selected = Array.from(document.querySelectorAll('.verification-check:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Pilih minimal 1 verifikasi pending.');
        return;
    }

    if (type === 'approve') {
        const form = document.getElementById('bulkApproveForm');
        form.querySelectorAll('input[name="verification_ids[]"], input[name="notes"]').forEach(el => el.remove());

        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'verification_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        const notesVal = document.getElementById('bulkApproveNotes').value.trim();
        if (notesVal) {
            const notesInput = document.createElement('input');
            notesInput.type = 'hidden';
            notesInput.name = 'notes';
            notesInput.value = notesVal;
            form.appendChild(notesInput);
        }

        form.submit();
        return;
    }

    const reason = document.getElementById('bulkRejectReason').value.trim();
    if (!reason) {
        alert('Alasan tolak massal wajib diisi.');
        return;
    }

    const form = document.getElementById('bulkRejectForm');
    form.querySelectorAll('input[name="verification_ids[]"], input[name="reason"]').forEach(el => el.remove());

    selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'verification_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    const reasonInput = document.createElement('input');
    reasonInput.type = 'hidden';
    reasonInput.name = 'reason';
    reasonInput.value = reason;
    form.appendChild(reasonInput);

    form.submit();
}
</script>
@endsection
