@extends(Auth::guard('dosen')->check() ? 'central.backpage.core-backpage-dosen' : 'core-themes.core-backpage')

@section('custom-css')
    <style>
        .absensi-card {
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .absensi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .time-display {
            font-size: 2.5rem;
            font-weight: 600;
            color: #4b6cb7;
        }

        .date-display {
            font-size: 1.2rem;
            color: #6c757d;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .photo-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .type-select {
            border-radius: 10px;
            padding: 0.5rem;
        }

        .history-item {
            border-left: 3px solid #4b6cb7;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        
        .zoom-hover {
            transition: transform 0.2s;
            cursor: zoom-in;
        }
        
        .zoom-hover:hover {
            transform: scale(1.02);
            opacity: 0.9;
        }
    </style>
@endsection

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                </div>
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                </div>
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ $user->photo ?? asset('dashboard/dist/img/default-profile.png') }}" alt="Profile" class="rounded-circle" style="width: 64px; height: 64px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">Welcome, {{ $user->name }}!</h4>
                            <p class="text-muted mb-0">Silahkan lakukan absensi sesuai dengan jadwal kerja Anda.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="time-display" id="current-time">00:00:00</div>
                            <div class="date-display" id="current-date">{{ date('l, d F Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card absensi-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Absen Masuk</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route($spref . 'absensi-handle') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Jenis Absensi</label>
                            <select name="type" class="form-select type-select" required>
                                <option value="0">Absen Regular</option>
                                <option value="1">Absen Lembur</option>
                                <option value="2">Absen Sakit</option>
                                <option value="3">Keperluan Berobat</option>
                                <option value="4">Masuk Telat</option>
                                <option value="5">Pulang Awal</option>
                                <option value="6">Keperluan Pribadi</option>
                                <option value="7">Cuti Tahunan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Masuk</label>
                            <input type="time" name="time_in" class="form-control" value="{{ date('H:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Selfie</label>
                            <input type="file" name="photo_in" class="form-control" accept="image/*" required>
                            <small class="text-muted">Pastikan wajah Anda terlihat jelas dalam foto</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="desc" class="form-control" rows="3" placeholder="Tambahkan keterangan jika diperlukan"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-login me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M20 12h-13l3 -3m0 6l-3 -3" />
                            </svg>
                            Absen Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card absensi-card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Absen Pulang</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route($spref . 'absensi-update', ['code' => 'CODE']) }}" method="POST" enctype="multipart/form-data" id="checkout-form">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">Waktu Pulang</label>
                            <input type="time" name="time_out" class="form-control" value="{{ date('H:i') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Selfie</label>
                            <input type="file" name="photo_out" class="form-control" accept="image/*" required>
                            <small class="text-muted">Pastikan wajah Anda terlihat jelas dalam foto</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="desc" class="form-control" rows="3" placeholder="Tambahkan keterangan jika diperlukan"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M9 12h12l-3 -3m0 6l-3 -3" />
                            </svg>
                            Absen Pulang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Riwayat Absensi Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Jenis</th>
                                    <th>Tanggal</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($absensi as $item)
                                    <tr>
                                        <td>{{ $item->code }}</td>
                                        <td>
                                            @switch($item->type)
                                                @case(0) <span class="badge bg-primary">Regular</span> @break
                                                @case(1) <span class="badge bg-warning">Lembur</span> @break
                                                @case(2) <span class="badge bg-danger">Sakit</span> @break
                                                @case(3) <span class="badge bg-info">Berobat</span> @break
                                                @case(4) <span class="badge bg-warning">Telat</span> @break
                                                @case(6) <span class="badge bg-secondary">Pribadi</span> @break
                                                @default <span class="badge bg-primary">Regular</span>
                                            @endswitch
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($item->date)) }}</td>
                                        <td>{{ $item->time_in }}</td>
                                        <td>{{ $item->time_out ?? '-' }}</td>
                                        <td>
                                            @switch($item->status)
                                                @case(0) <span class="badge bg-success text-white">Auto Approve</span> @break
                                                @case(1) <span class="badge bg-warning">Pending</span> @break
                                                @case(2) <span class="badge bg-success text-white">Accepted</span> @break
                                                @case(3) <span class="badge bg-danger">Declined</span> @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#photoModal{{ $item->id }}" title="Lihat Foto">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M15 8h.01" />
                                                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                                    </svg>
                                                </button>
                                                @if(!$item->time_out)
                                                    <button type="button" class="btn btn-sm btn-success" onclick="setCheckoutCode('{{ $item->code }}')" title="Catat Waktu Pulang">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout m-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                                            <path d="M9 12h12l-3 -3m0 6l-3 -3" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">Belum ada riwayat absensi hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($absensi as $item)
        <div class="modal modal-blur fade" id="photoModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Foto Absensi <span class="badge bg-primary ms-2">{{ $item->code }}</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="row">
                            <div class="col-md-6 text-center mb-3 mb-md-0">
                                <h6>Absen Masuk <span class="text-muted">({{ $item->time_in }})</span></h6>
                                <div class="bg-white p-2 rounded shadow-sm">
                                    <a href="{{ asset('storage/images/absensi/' . $item->photo_in) }}" target="_blank" title="Klik untuk lihat resolusi penuh">
                                        <img src="{{ asset('storage/images/absensi/' . $item->photo_in) }}" class="img-fluid rounded border zoom-hover" style="max-height: 400px; width: 100%; object-fit: contain; background: #000;" alt="Absensi Masuk">
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-6 text-center">
                                <h6>Absen Pulang <span class="text-muted">({{ $item->time_out ?? 'Belum Absen' }})</span></h6>
                                <div class="bg-white p-2 rounded shadow-sm h-100 d-flex flex-column justify-content-center">
                                    @if($item->photo_out)
                                        <a href="{{ asset('storage/images/absensi/' . $item->photo_out) }}" target="_blank" title="Klik untuk lihat resolusi penuh">
                                            <img src="{{ asset('storage/images/absensi/' . $item->photo_out) }}" class="img-fluid rounded border zoom-hover" style="max-height: 400px; width: 100%; object-fit: contain; background: #000;" alt="Absensi Pulang">
                                        </a>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center border border-dashed rounded bg-light" style="min-height: 200px;">
                                            <div class="text-muted">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock mb-2 d-block mx-auto" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                    <path d="M12 7v5l3 3" />
                                                </svg>
                                                Belum absen pulang
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@section('custom-js')
    <script>
        // Update time display
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const timeElement = document.getElementById('current-time');
            if(timeElement) timeElement.textContent = timeString;
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Preview image before upload
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('img');
                        preview.src = e.target.result;
                        preview.className = 'photo-preview mt-2 shadow-sm border';
                        const parent = this.parentElement;
                        const existingPreview = parent.querySelector('.photo-preview');
                        if (existingPreview) {
                            parent.removeChild(existingPreview);
                        }
                        parent.appendChild(preview);
                    }.bind(this);
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        // Handle check-out form
        function setCheckoutCode(code) {
            const form = document.getElementById('checkout-form');
            if(!form) return;
            
            const action = form.getAttribute('action').replace('CODE', code);
            form.setAttribute('action', action);
            
            // Enable the form elements
            form.querySelectorAll('input, select, textarea, button').forEach(element => {
                element.disabled = false;
            });
            
            // Auto scroll to checkout form
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Highlight the card temporarily
            const card = form.closest('.card');
            card.style.boxShadow = '0 0 0 3px #28a745';
            setTimeout(() => { card.style.boxShadow = ''; }, 2000);
        }

        // Disable check-out form by default
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('checkout-form');
            if(form) {
                form.querySelectorAll('input, select, textarea, button').forEach(element => {
                    element.disabled = true;
                });
            }
        });
    </script>
@endsection