@forelse($mahasiswa as $mhs)
    <tr data-prodi-id="{{ $mhs->prodi->id ?? '' }}" data-prodi-name="{{ $mhs->prodi->name ?? '' }}">
        <td>
            <input type="checkbox" class="form-check-input mahasiswa-checkbox" value="{{ $mhs->id }}">
        </td>
        <td>
            <div class="d-flex align-items-center">
                <img src="{{ $mhs->photo }}" alt="Photo" class="profile-image me-2">
                <div>
                    <div class="fw-bold">{{ $mhs->name }}</div>
                    <small class="text-muted">{{ $mhs->nim }}</small>
                </div>
            </div>
        </td>
        <td>
            <span class="badge bg-info">{{ $mhs->prodi->name ?? '-' }}</span>
        </td>
        <td>{{ $mhs->kelas->name ?? '-' }}</td>
        <td>
            @if($mhs->dosenPenasehat)
                <div class="d-flex align-items-center">
                    <img src="{{ $mhs->dosenPenasehat->photo }}" alt="Photo" class="profile-image-small me-2">
                    <div>
                        <div class="fw-bold">{{ $mhs->dosenPenasehat->name }}</div>
                        <small class="text-muted">{{ $mhs->dosenPenasehat->nidn ?? $mhs->dosenPenasehat->nip }}</small>
                    </div>
                </div>
            @else
                <span class="badge bg-warning">Belum Ditugaskan</span>
            @endif
        </td>
        <td>
            @if($mhs->dosenPenasehat)
                <button type="button" class="btn btn-sm btn-outline-warning me-1" 
                        onclick="changeAdvisor({{ $mhs->id }}, '{{ $mhs->name }}', {{ $mhs->dosen_penasehat_id }})">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="removeAdvisor({{ $mhs->id }}, '{{ $mhs->name }}')">
                    <i class="fas fa-times"></i>
                </button>
            @else
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="assignAdvisor({{ $mhs->id }}, '{{ $mhs->name }}')">
                    <i class="fas fa-plus"></i> Tugaskan
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <p class="text-muted">Tidak ada data mahasiswa</p>
        </td>
    </tr>
@endforelse
