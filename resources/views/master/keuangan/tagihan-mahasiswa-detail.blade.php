@extends('core-themes.core-backpage')

@section('title')
    {{ $pages }} - {{ $academy }}
@endsection

@section('menu')
    {{ $menus }}
@endsection

@section('submenu')
    {{ $pages }}
@endsection

@section('subdesc')
    Detail tagihan untuk {{ $mahasiswa->name }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Detail Tagihan - {{ $mahasiswa->name }}
                    </h5>
                    <a href="{{ route('keuangan.tagihan-individual-index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div class="card-body">
                    <!-- Mahasiswa Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-secondary bg-opacity-10 border-secondary">
                                <div class="card-body">
                                    <h6 class="card-title">Informasi Mahasiswa</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Nama</strong></td>
                                            <td>: {{ $mahasiswa->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIM</strong></td>
                                            <td>: {{ $mahasiswa->numb_nim }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Program Studi</strong></td>
                                            <td>: {{ $mahasiswa->prodi->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kelas</strong></td>
                                            <td>: {{ $mahasiswa->kelas->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Deposit Saldo</strong></td>
                                            <td>: <span class="badge bg-success text-white">Rp {{ number_format($mahasiswa->deposit_balance ?? 0, 0, ',', '.') }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-secondary bg-opacity-10 border-secondary">
                                <div class="card-body">
                                    <h6 class="card-title">Ringkasan Tagihan</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ $tagihans->count() }}</h4>
                                                <small>Total Tagihan</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border-end">
                                                <h4 class="text-warning">{{ $tagihans->where('status', 'Pending')->count() }}</h4>
                                                <small>Pending</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-success">{{ $tagihans->whereIn('status', ['Paid', 'Sukses'])->count() }}</h4>
                                            <small>Lunas</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h5 class="text-warning">Rp {{ number_format($tagihans->where('status', 'Pending')->sum('amount'), 0, ',', '.') }}</h5>
                                                <small>Total Pending</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-success">Rp {{ number_format($tagihans->whereIn('status', ['Paid', 'Sukses'])->sum('amount'), 0, ',', '.') }}</h5>
                                            <small>Total Lunas</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tagihan List -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Tahun Akademik</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tagihans as $key => $tagihan)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <span class="badge bg-primary text-white">{{ $tagihan->code }}</span>
                                        </td>
                                        <td>{{ $tagihan->desc }}</td>
                                        <td>
                                            <strong class="text-primary">Rp {{ number_format($tagihan->amount, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @if($tagihan->due_date)
                                                {{ \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">Tidak ada tempo</span>
                                            @endif
                                        </td>
                                        <td>{{ $tagihan->tahunAkademik->name ?? '-' }}</td>
                                        <td>
                                            @if($tagihan->status == 'Pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($tagihan->status == 'Paid' || $tagihan->status == 'Sukses')
                                                <span class="badge bg-success">Lunas</span>
                                            @elseif($tagihan->status == 'Cancelled')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $tagihan->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tagihan->group_id)
                                                <span class="badge bg-info text-white">Group</span>
                                            @else
                                                <span class="badge bg-dark text-white">Individual</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if($tagihan->status == 'Pending')
                                                    <button type="button" class="btn btn-sm btn-success px-2" 
                                                            onclick="markAsPaid({{ $tagihan->id }})"
                                                            title="Tandai Lunas">
                                                        ✓
                                                    </button>
                                                @endif
                                                
                                                <button type="button" class="btn btn-sm btn-primary px-2" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal{{ $tagihan->id }}"
                                                        title="Edit">
                                                    ✎
                                                </button>
                                                
                                                @if($tagihan->status != 'Paid' && $tagihan->status != 'Sukses')
                                                    <button type="button" class="btn btn-sm btn-danger px-2" 
                                                            onclick="deleteTagihan({{ $tagihan->id }})"
                                                            title="Hapus">
                                                        🗑
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada tagihan untuk mahasiswa ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach($tagihans as $tagihan)
        <div class="modal fade" id="editModal{{ $tagihan->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('keuangan.tagihan-individual-update', $tagihan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Tagihan - {{ $tagihan->code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_mahasiswa_id{{ $tagihan->id }}" class="form-label">Mahasiswa</label>
                                    <input type="text" class="form-control" value="{{ $mahasiswa->numb_nim }} - {{ $mahasiswa->name }}" readonly>
                                    <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_taka_id{{ $tagihan->id }}" class="form-label">Tahun Akademik</label>
                                    <select class="form-select" name="taka_id" id="edit_taka_id{{ $tagihan->id }}" required>
                                        @foreach($tahunAkademiks as $taka)
                                            <option value="{{ $taka->id }}" {{ $tagihan->taka_id == $taka->id ? 'selected' : '' }}>
                                                {{ $taka->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_desc{{ $tagihan->id }}" class="form-label">Deskripsi</label>
                                    <input type="text" class="form-control" name="desc" id="edit_desc{{ $tagihan->id }}" value="{{ $tagihan->desc }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_amount{{ $tagihan->id }}" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" name="amount" id="edit_amount{{ $tagihan->id }}" value="{{ $tagihan->amount }}" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_due_date{{ $tagihan->id }}" class="form-label">Jatuh Tempo</label>
                                    <input type="date" class="form-control" name="due_date" id="edit_due_date{{ $tagihan->id }}" value="{{ $tagihan->due_date ? \Carbon\Carbon::parse($tagihan->due_date)->format('Y-m-d') : '' }}">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="edit_no_due_date{{ $tagihan->id }}" name="no_due_date" value="1" {{ !$tagihan->due_date ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_no_due_date{{ $tagihan->id }}">
                                            Tidak ada tempo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_status{{ $tagihan->id }}" class="form-label">Status</label>
                                    <select class="form-select" name="status" id="edit_status{{ $tagihan->id }}" required>
                                        <option value="Pending" {{ $tagihan->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Paid" {{ $tagihan->status == 'Paid' ? 'selected' : '' }}>Lunas</option>
                                        <option value="Sukses" {{ $tagihan->status == 'Sukses' ? 'selected' : '' }}>Sukses</option>
                                        <option value="Cancelled" {{ $tagihan->status == 'Cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('custom-css')
<style>
    /* Fix badge text colors */
    .badge {
        color: white !important;
    }

    .badge.bg-primary {
        color: white !important;
    }

    .badge.bg-warning {
        color: white !important;
    }

    .badge.bg-success {
        color: white !important;
    }

    .badge.bg-danger {
        color: white !important;
    }

    .badge.bg-info {
        color: white !important;
    }

    .badge.bg-secondary {
        color: white !important;
    }

    .badge.bg-dark {
        color: white !important;
    }

    /* Dark mode badge fixes */
    [data-bs-theme="dark"] .badge {
        color: white !important;
    }

    [data-bs-theme="dark"] .badge.bg-primary {
        color: white !important;
    }

    [data-bs-theme="dark"] .badge.bg-warning {
        color: white !important;
    }

    [data-bs-theme="dark"] .badge.bg-success {
        color: white !important;
    }

    [data-bs-theme="dark"] .badge.bg-danger {
        color: white !important;
    }

    [data-bs-theme="dark"] .badge.bg-info {
        color: white !important;
    }

    [data-bs-theme="dark"] .badge.bg-secondary {
        color: white !important;
    }

    [data-bs-theme="dark"] .badge.bg-dark {
        color: white !important;
    }
</style>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {
            // Handle "Tidak ada tempo" checkbox for edit forms
            $('[id^="edit_no_due_date"]').change(function() {
                const tagihanId = $(this).attr('id').replace('edit_no_due_date', '');
                const dueDateInput = $('#edit_due_date' + tagihanId);
                
                if ($(this).is(':checked')) {
                    dueDateInput.val('').prop('disabled', true);
                } else {
                    dueDateInput.prop('disabled', false);
                }
            });

            // Initialize edit form checkboxes state
            $('[id^="edit_no_due_date"]').each(function() {
                const tagihanId = $(this).attr('id').replace('edit_no_due_date', '');
                const dueDateInput = $('#edit_due_date' + tagihanId);
                
                if ($(this).is(':checked')) {
                    dueDateInput.prop('disabled', true);
                }
            });
        });

        function markAsPaid(id) {
            if (confirm('Apakah Anda yakin ingin menandai tagihan ini sebagai lunas?')) {
                fetch(`{{ url('keuangan/tagihan-individual') }}/${id}/mark-paid`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal memperbarui status tagihan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
            }
        }

        function deleteTagihan(id) {
            if (confirm('Apakah Anda yakin ingin menghapus tagihan ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('keuangan/tagihan-individual') }}/${id}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
