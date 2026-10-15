@extends('core-themes.core-backpage')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ $menus }}
                </div>
                <h2 class="page-title">
                    {{ $pages }}
                </h2>
            </div>
            <div class="col-12 col-md-auto ms-auto d-print-none">
                <div class="btn-list">
                    <form method="POST" action="{{ route($spref.'akademik.bobot-nilai-reset') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100 w-md-auto" onclick="return confirm('Yakin ingin mereset ke pengaturan default?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                            </svg>
                            <span class="d-none d-sm-inline">Reset ke Default</span>
                            <span class="d-sm-none">Reset</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10"/>
                        </svg>
                    </div>
                    <div>
                        {{ session('success') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z"/>
                            <path d="M12 9v4"/>
                            <path d="M12 17h.01"/>
                        </svg>
                    </div>
                    <div>
                        {{ session('error') }}
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pengaturan Bobot Komponen Nilai</h3>
                        <div class="card-actions">
                            <div class="text-secondary">
                                <small>Total bobot harus sama dengan 100%</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route($spref.'akademik.bobot-nilai-update') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <!-- Desktop Table Layout -->
                                    <div class="table-responsive d-none d-md-block">
                                        <table class="table table-vcenter">
                                            <thead>
                                                <tr>
                                                    <th>Komponen Nilai</th>
                                                    <th>Nama Tampilan</th>
                                                    <th>Bobot (%)</th>
                                                    <th>Warna</th>
                                                    <th>Status</th>
                                                    <th>Preview</th>
                                                </tr>
                                            </thead>
                                            <tbody id="components-table">
                                                @foreach($bobotNilai as $index => $bobot)
                                                <tr>
                                                    <td>
                                                        <strong>{{ strtoupper($bobot->component_key) }}</strong>
                                                        <input type="hidden" name="components[{{ $index }}][id]" value="{{ $bobot->id }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="components[{{ $index }}][component_name]" 
                                                               value="{{ old('components.'.$index.'.component_name', $bobot->component_name) }}" 
                                                               placeholder="Nama komponen" required>
                                                        @error('components.'.$index.'.component_name')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control weight-input" 
                                                                   name="components[{{ $index }}][weight]" 
                                                                   value="{{ old('components.'.$index.'.weight', $bobot->weight) }}" 
                                                                   min="0" max="100" step="0.01" required>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        @error('components.'.$index.'.weight')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select class="form-select color-select" name="components[{{ $index }}][color_class]" required>
                                                            @foreach($colorClasses as $class => $label)
                                                                <option value="{{ $class }}" 
                                                                        {{ old('components.'.$index.'.color_class', $bobot->color_class) == $class ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('components.'.$index.'.color_class')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <label class="form-check form-switch">
                                                            <input class="form-check-input status-checkbox" type="checkbox" 
                                                                   name="components[{{ $index }}][is_active]" 
                                                                   {{ old('components.'.$index.'.is_active', $bobot->is_active) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Aktif</span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="preview-component">
                                                            <div class="text-center">
                                                                <div class="h4 fw-bold preview-weight {{ $bobot->color_class }}">{{ $bobot->weight }}%</div>
                                                                <div class="text-body-emphasis preview-name">{{ $bobot->component_name }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2"><strong>Total Bobot Aktif:</strong></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="total-weight" readonly>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div id="weight-status" class="text-muted">
                                                            <small>Menghitung...</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Mobile Card Layout -->
                                    <div class="d-block d-md-none" id="components-mobile">
                                        @foreach($bobotNilai as $index => $bobot)
                                        <div class="card mb-3 component-card">
                                            <div class="card-body">
                                                <div class="row align-items-center mb-3">
                                                    <div class="col">
                                                        <h5 class="card-title mb-1">{{ strtoupper($bobot->component_key) }}</h5>
                                                        <input type="hidden" name="components[{{ $index }}][id]" value="{{ $bobot->id }}">
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="preview-component">
                                                            <div class="text-center">
                                                                <div class="h4 fw-bold preview-weight {{ $bobot->color_class }}">{{ $bobot->weight }}%</div>
                                                                <div class="text-body-emphasis preview-name small">{{ $bobot->component_name }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Tampilan</label>
                                                    <input type="text" class="form-control" name="components[{{ $index }}][component_name]" 
                                                           value="{{ old('components.'.$index.'.component_name', $bobot->component_name) }}" 
                                                           placeholder="Nama komponen" required>
                                                    @error('components.'.$index.'.component_name')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="form-label">Bobot (%)</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control weight-input" 
                                                                   name="components[{{ $index }}][weight]" 
                                                                   value="{{ old('components.'.$index.'.weight', $bobot->weight) }}" 
                                                                   min="0" max="100" step="0.01" required>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        @error('components.'.$index.'.weight')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label">Warna</label>
                                                        <select class="form-select color-select" name="components[{{ $index }}][color_class]" required>
                                                            @foreach($colorClasses as $class => $label)
                                                                <option value="{{ $class }}" 
                                                                        {{ old('components.'.$index.'.color_class', $bobot->color_class) == $class ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('components.'.$index.'.color_class')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <label class="form-check form-switch">
                                                        <input class="form-check-input status-checkbox" type="checkbox" 
                                                               name="components[{{ $index }}][is_active]" 
                                                               {{ old('components.'.$index.'.is_active', $bobot->is_active) ? 'checked' : '' }}>
                                                        <span class="form-check-label">Komponen Aktif</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                        <!-- Mobile Total Weight Display -->
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <strong>Total Bobot Aktif:</strong>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="total-weight-mobile" readonly>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="weight-status-mobile" class="text-muted mt-2">
                                                    <small>Menghitung...</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary" id="save-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    Simpan Pengaturan
                                </button>
                                <a href="{{ route($spref.'akademik.bobot-nilai-render') }}" class="btn btn-secondary ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                    </svg>
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Scale Settings Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pengaturan Skala Nilai (Grade Scale)</h3>
                        <div class="card-actions">
                            <form method="POST" action="{{ route($spref.'akademik.grade-scales-reset') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Yakin ingin mereset skala nilai ke pengaturan default?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                    </svg>
                                    Reset Skala Nilai
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route($spref.'akademik.grade-scales-update') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <!-- Desktop Table Layout -->
                                    <div class="table-responsive d-none d-md-block">
                                        <table class="table table-vcenter">
                                            <thead>
                                                <tr>
                                                    <th>Huruf Nilai</th>
                                                    <th>Rentang Nilai Min</th>
                                                    <th>Rentang Nilai Max</th>
                                                    <th>Nilai GPA</th>
                                                    <th>Warna</th>
                                                    <th>Status</th>
                                                    <th>Preview</th>
                                                </tr>
                                            </thead>
                                            <tbody id="grade-scales-table">
                                                @foreach($gradeScales as $index => $scale)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control grade-letter-input" 
                                                               name="grade_scales[{{ $index }}][grade_letter]" 
                                                               value="{{ old('grade_scales.'.$index.'.grade_letter', $scale->grade_letter) }}" 
                                                               placeholder="A" maxlength="5" required>
                                                        <input type="hidden" name="grade_scales[{{ $index }}][id]" value="{{ $scale->id }}">
                                                        @error('grade_scales.'.$index.'.grade_letter')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control min-score-input" 
                                                               name="grade_scales[{{ $index }}][min_score]" 
                                                               value="{{ old('grade_scales.'.$index.'.min_score', $scale->min_score) }}" 
                                                               min="0" max="100" step="0.01" required>
                                                        @error('grade_scales.'.$index.'.min_score')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control max-score-input" 
                                                               name="grade_scales[{{ $index }}][max_score]" 
                                                               value="{{ old('grade_scales.'.$index.'.max_score', $scale->max_score) }}" 
                                                               min="0" max="100" step="0.01" required>
                                                        @error('grade_scales.'.$index.'.max_score')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control grade-point-input" 
                                                               name="grade_scales[{{ $index }}][grade_point]" 
                                                               value="{{ old('grade_scales.'.$index.'.grade_point', $scale->grade_point) }}" 
                                                               min="0" max="4" step="0.01" required>
                                                        @error('grade_scales.'.$index.'.grade_point')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <select class="form-select grade-color-select" name="grade_scales[{{ $index }}][color_class]" required>
                                                            @foreach($colorClasses as $class => $label)
                                                                <option value="{{ $class }}" 
                                                                        {{ old('grade_scales.'.$index.'.color_class', $scale->color_class) == $class ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('grade_scales.'.$index.'.color_class')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <label class="form-check form-switch">
                                                            <input class="form-check-input grade-status-checkbox" type="checkbox" 
                                                                   name="grade_scales[{{ $index }}][is_active]" 
                                                                   {{ old('grade_scales.'.$index.'.is_active', $scale->is_active) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Aktif</span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <div class="preview-grade">
                                                            <div class="text-center">
                                                                <div class="h5 fw-bold preview-grade-letter {{ $scale->color_class }}">{{ $scale->grade_letter }}</div>
                                                                <div class="small text-muted preview-grade-range">{{ $scale->min_score }}-{{ $scale->max_score }}</div>
                                                                <div class="small preview-grade-point">GPA: {{ $scale->grade_point }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Mobile Card Layout -->
                                    <div class="d-block d-md-none" id="grade-scales-mobile">
                                        @foreach($gradeScales as $index => $scale)
                                        <div class="card mb-3 grade-scale-card">
                                            <div class="card-body">
                                                <div class="row align-items-center mb-3">
                                                    <div class="col">
                                                        <h5 class="card-title mb-1">Grade Scale {{ $index + 1 }}</h5>
                                                        <input type="hidden" name="grade_scales[{{ $index }}][id]" value="{{ $scale->id }}">
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="preview-grade">
                                                            <div class="text-center">
                                                                <div class="h5 fw-bold preview-grade-letter {{ $scale->color_class }}">{{ $scale->grade_letter }}</div>
                                                                <div class="small text-muted preview-grade-range">{{ $scale->min_score }}-{{ $scale->max_score }}</div>
                                                                <div class="small preview-grade-point">GPA: {{ $scale->grade_point }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label">Huruf Nilai</label>
                                                        <input type="text" class="form-control grade-letter-input" 
                                                               name="grade_scales[{{ $index }}][grade_letter]" 
                                                               value="{{ old('grade_scales.'.$index.'.grade_letter', $scale->grade_letter) }}" 
                                                               placeholder="A" maxlength="5" required>
                                                        @error('grade_scales.'.$index.'.grade_letter')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label">Nilai GPA</label>
                                                        <input type="number" class="form-control grade-point-input" 
                                                               name="grade_scales[{{ $index }}][grade_point]" 
                                                               value="{{ old('grade_scales.'.$index.'.grade_point', $scale->grade_point) }}" 
                                                               min="0" max="4" step="0.01" required>
                                                        @error('grade_scales.'.$index.'.grade_point')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label">Nilai Min</label>
                                                        <input type="number" class="form-control min-score-input" 
                                                               name="grade_scales[{{ $index }}][min_score]" 
                                                               value="{{ old('grade_scales.'.$index.'.min_score', $scale->min_score) }}" 
                                                               min="0" max="100" step="0.01" required>
                                                        @error('grade_scales.'.$index.'.min_score')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label">Nilai Max</label>
                                                        <input type="number" class="form-control max-score-input" 
                                                               name="grade_scales[{{ $index }}][max_score]" 
                                                               value="{{ old('grade_scales.'.$index.'.max_score', $scale->max_score) }}" 
                                                               min="0" max="100" step="0.01" required>
                                                        @error('grade_scales.'.$index.'.max_score')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="form-label">Warna</label>
                                                        <select class="form-select grade-color-select" name="grade_scales[{{ $index }}][color_class]" required>
                                                            @foreach($colorClasses as $class => $label)
                                                                <option value="{{ $class }}" 
                                                                        {{ old('grade_scales.'.$index.'.color_class', $scale->color_class) == $class ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('grade_scales.'.$index.'.color_class')
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                    <div class="col-6 d-flex align-items-end">
                                                        <label class="form-check form-switch">
                                                            <input class="form-check-input grade-status-checkbox" type="checkbox" 
                                                                   name="grade_scales[{{ $index }}][is_active]" 
                                                                   {{ old('grade_scales.'.$index.'.is_active', $scale->is_active) ? 'checked' : '' }}>
                                                            <span class="form-check-label">Grade Aktif</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn btn-success" id="save-grade-scales-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10"/>
                                    </svg>
                                    Simpan Skala Nilai
                                </button>
                                <div id="grade-scale-validation-message" class="ms-3 d-inline-block"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Cara Penggunaan:</h4>
                                <ul>
                                    <li><strong>Nama Tampilan:</strong> Nama yang akan ditampilkan di interface dosen (bisa diubah sesuai kebutuhan)</li>
                                    <li><strong>Bobot:</strong> Persentase bobot untuk setiap komponen (total harus 100%)</li>
                                    <li><strong>Warna:</strong> Warna yang akan ditampilkan di interface</li>
                                    <li><strong>Status:</strong> Aktif/nonaktif komponen (hanya komponen aktif yang dihitung)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4>Contoh Penggunaan:</h4>
                                <ul>
                                    <li>Ubah "Quiz" menjadi "Keaktifan Mahasantri"</li>
                                    <li>Sesuaikan bobot sesuai kebijakan akademik</li>
                                    <li>Nonaktifkan komponen yang tidak digunakan</li>
                                    <li>Perubahan akan langsung terlihat di halaman input nilai dosen</li>
                                </ul>
                                <div class="alert alert-info">
                                    <strong>Catatan:</strong> Perubahan setting ini akan mempengaruhi semua halaman input nilai dosen secara real-time.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-css')
<style>
/* Dark mode fixes for mobile cards */
[data-bs-theme="dark"] .card.bg-light {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .card.bg-light .card-body {
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .component-card {
    background-color: var(--tblr-card-bg-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .grade-scale-card {
    background-color: var(--tblr-card-bg-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
}

[data-bs-theme="dark"] .form-control {
    background-color: var(--tblr-form-control-bg-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .form-select {
    background-color: var(--tblr-form-select-bg-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

/* Fix dropdown options in dark mode */
[data-bs-theme="dark"] .form-select option {
    background-color: var(--tblr-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

/* Alternative approach for better browser compatibility */
[data-bs-theme="dark"] select.form-select {
    background-color: var(--tblr-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] select.form-select option {
    background-color: var(--tblr-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .input-group-text {
    background-color: var(--tblr-input-group-addon-bg-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

/* Ensure mobile total weight card is properly styled in dark mode */
[data-bs-theme="dark"] .card.bg-light {
    background-color: rgba(var(--tblr-body-color-rgb), 0.03) !important;
}

/* Additional fixes for color and grade color selects */
[data-bs-theme="dark"] .color-select,
[data-bs-theme="dark"] .grade-color-select {
    background-color: var(--tblr-dark) !important;
    border-color: var(--tblr-border-color-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}

[data-bs-theme="dark"] .color-select option,
[data-bs-theme="dark"] .grade-color-select option {
    background-color: var(--tblr-dark) !important;
    color: var(--tblr-body-color-dark) !important;
}
</style>
@endsection

@section('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const weightInputs = document.querySelectorAll('.weight-input');
    const statusCheckboxes = document.querySelectorAll('.status-checkbox');
    const colorSelects = document.querySelectorAll('.color-select');
    const nameInputs = document.querySelectorAll('input[name*="component_name"]');
    const totalWeightInput = document.getElementById('total-weight');
    const totalWeightMobileInput = document.getElementById('total-weight-mobile');
    const weightStatus = document.getElementById('weight-status');
    const weightStatusMobile = document.getElementById('weight-status-mobile');
    const saveButton = document.getElementById('save-button');

    function updateTotalWeight() {
        let total = 0;
        
        // Only calculate from desktop table OR mobile cards, not both
        // Check if desktop table is visible
        const desktopTable = document.querySelector('#components-table');
        const mobileCards = document.querySelector('#components-mobile');
        
        if (desktopTable && window.getComputedStyle(desktopTable.closest('.table-responsive')).display !== 'none') {
            // Calculate from desktop table
            const rows = document.querySelectorAll('#components-table tr');
            rows.forEach((row) => {
                const weightInput = row.querySelector('.weight-input');
                const statusCheckbox = row.querySelector('.status-checkbox');
                
                if (weightInput && statusCheckbox && statusCheckbox.checked) {
                    total += parseFloat(weightInput.value) || 0;
                }
            });
        } else if (mobileCards) {
            // Calculate from mobile cards
            const cards = document.querySelectorAll('.component-card');
            cards.forEach((card) => {
                const weightInput = card.querySelector('.weight-input');
                const statusCheckbox = card.querySelector('.status-checkbox');
                
                if (weightInput && statusCheckbox && statusCheckbox.checked) {
                    total += parseFloat(weightInput.value) || 0;
                }
            });
        }

        // Update both desktop and mobile displays
        if (totalWeightInput) totalWeightInput.value = total.toFixed(2);
        if (totalWeightMobileInput) totalWeightMobileInput.value = total.toFixed(2);
        
        // Update status messages
        const statusMessage = Math.abs(total - 100) < 0.01 
            ? '<small class="text-success"><strong>✓ Total bobot valid (100%)</strong></small>'
            : `<small class="text-danger"><strong>⚠ Total bobot harus 100% (saat ini: ${total.toFixed(2)}%)</strong></small>`;
        
        if (weightStatus) weightStatus.innerHTML = statusMessage;
        if (weightStatusMobile) weightStatusMobile.innerHTML = statusMessage;
        
        // Update save button state
        if (saveButton) {
            saveButton.disabled = Math.abs(total - 100) >= 0.01;
        }
    }

    function updatePreview(container) {
        const nameInput = container.querySelector('input[name*="component_name"]');
        const weightInput = container.querySelector('.weight-input');
        const colorSelect = container.querySelector('.color-select');
        const statusCheckbox = container.querySelector('.status-checkbox');
        
        const previewWeight = container.querySelector('.preview-weight');
        const previewName = container.querySelector('.preview-name');
        const previewComponent = container.querySelector('.preview-component');
        
        if (nameInput && weightInput && colorSelect && previewWeight && previewName) {
            // Update preview content
            previewWeight.textContent = (parseFloat(weightInput.value) || 0).toFixed(0) + '%';
            previewName.textContent = nameInput.value || 'Nama Komponen';
            
            // Update color class
            previewWeight.className = `h4 fw-bold preview-weight ${colorSelect.value}`;
            
            // Update opacity based on status
            if (statusCheckbox && !statusCheckbox.checked) {
                previewComponent.style.opacity = '0.5';
                previewComponent.style.filter = 'grayscale(100%)';
            } else {
                previewComponent.style.opacity = '1';
                previewComponent.style.filter = 'none';
            }
        }
    }

    function updateAllPreviews() {
        // Update desktop table previews
        const tableRows = document.querySelectorAll('#components-table tr');
        tableRows.forEach(updatePreview);
        
        // Update mobile card previews
        const mobileCards = document.querySelectorAll('.component-card');
        mobileCards.forEach(updatePreview);
    }

    // Event listeners for both desktop and mobile
    weightInputs.forEach(input => {
        input.addEventListener('input', function() {
            updateTotalWeight();
            const container = this.closest('tr') || this.closest('.component-card');
            if (container) updatePreview(container);
        });
    });

    statusCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateTotalWeight();
            const container = this.closest('tr') || this.closest('.component-card');
            if (container) updatePreview(container);
        });
    });

    colorSelects.forEach(select => {
        select.addEventListener('change', function() {
            const container = this.closest('tr') || this.closest('.component-card');
            if (container) updatePreview(container);
        });
    });

    nameInputs.forEach(input => {
        input.addEventListener('input', function() {
            const container = this.closest('tr') || this.closest('.component-card');
            if (container) updatePreview(container);
        });
    });

    // Initial calculations
    updateTotalWeight();
    updateAllPreviews();

    // Grade Scale Management
    const gradeLetterInputs = document.querySelectorAll('.grade-letter-input');
    const minScoreInputs = document.querySelectorAll('.min-score-input');
    const maxScoreInputs = document.querySelectorAll('.max-score-input');
    const gradePointInputs = document.querySelectorAll('.grade-point-input');
    const gradeColorSelects = document.querySelectorAll('.grade-color-select');
    const gradeStatusCheckboxes = document.querySelectorAll('.grade-status-checkbox');
    const saveGradeScalesButton = document.getElementById('save-grade-scales-button');
    const gradeScaleValidationMessage = document.getElementById('grade-scale-validation-message');

    function validateGradeScales() {
        const activeScales = [];
        
        // Only validate from desktop table OR mobile cards, not both
        // Check if desktop table is visible
        const desktopTable = document.querySelector('#grade-scales-table');
        const mobileCards = document.querySelector('#grade-scales-mobile');
        
        if (desktopTable && window.getComputedStyle(desktopTable.closest('.table-responsive')).display !== 'none') {
            // Validate from desktop table
            const rows = document.querySelectorAll('#grade-scales-table tr');
            rows.forEach((row) => {
                const statusCheckbox = row.querySelector('.grade-status-checkbox');
                const minScoreInput = row.querySelector('.min-score-input');
                const maxScoreInput = row.querySelector('.max-score-input');
                
                if (statusCheckbox && statusCheckbox.checked && minScoreInput && maxScoreInput) {
                    activeScales.push({
                        min: parseFloat(minScoreInput.value) || 0,
                        max: parseFloat(maxScoreInput.value) || 0,
                        row: row
                    });
                }
            });
        } else if (mobileCards) {
            // Validate from mobile cards
            const cards = document.querySelectorAll('.grade-scale-card');
            cards.forEach((card) => {
                const statusCheckbox = card.querySelector('.grade-status-checkbox');
                const minScoreInput = card.querySelector('.min-score-input');
                const maxScoreInput = card.querySelector('.max-score-input');
                
                if (statusCheckbox && statusCheckbox.checked && minScoreInput && maxScoreInput) {
                    activeScales.push({
                        min: parseFloat(minScoreInput.value) || 0,
                        max: parseFloat(maxScoreInput.value) || 0,
                        card: card
                    });
                }
            });
        }

        // Sort by min score descending
        activeScales.sort((a, b) => b.min - a.min);

        // Check for overlaps and gaps
        let hasOverlap = false;
        let hasGap = false;
        let errorMessage = '';

        for (let i = 0; i < activeScales.length - 1; i++) {
            const current = activeScales[i];
            const next = activeScales[i + 1];
            
            // Check for overlap
            if (current.min <= next.max) {
                hasOverlap = true;
                errorMessage = 'Rentang nilai tidak boleh tumpang tindih';
                break;
            }
            
            // Check for gap (optional warning)
            if (current.min > next.max + 0.01) {
                hasGap = true;
            }
        }

        // Validate min <= max for each scale
        let hasInvalidRange = false;
        activeScales.forEach(scale => {
            if (scale.min > scale.max) {
                hasInvalidRange = true;
                errorMessage = 'Nilai minimum tidak boleh lebih besar dari nilai maksimum';
            }
        });

        // Update validation message and button state
        if (gradeScaleValidationMessage) {
            if (hasOverlap || hasInvalidRange) {
                gradeScaleValidationMessage.innerHTML = `<small class="text-danger"><strong>⚠ ${errorMessage}</strong></small>`;
                if (saveGradeScalesButton) saveGradeScalesButton.disabled = true;
            } else if (hasGap) {
                gradeScaleValidationMessage.innerHTML = '<small class="text-warning"><strong>⚠ Ada celah dalam rentang nilai</strong></small>';
                if (saveGradeScalesButton) saveGradeScalesButton.disabled = false;
            } else {
                gradeScaleValidationMessage.innerHTML = '<small class="text-success"><strong>✓ Skala nilai valid</strong></small>';
                if (saveGradeScalesButton) saveGradeScalesButton.disabled = false;
            }
        }
    }

    function updateGradePreview(container) {
        const letterInput = container.querySelector('.grade-letter-input');
        const minScoreInput = container.querySelector('.min-score-input');
        const maxScoreInput = container.querySelector('.max-score-input');
        const gradePointInput = container.querySelector('.grade-point-input');
        const colorSelect = container.querySelector('.grade-color-select');
        const statusCheckbox = container.querySelector('.grade-status-checkbox');
        
        const previewLetter = container.querySelector('.preview-grade-letter');
        const previewRange = container.querySelector('.preview-grade-range');
        const previewPoint = container.querySelector('.preview-grade-point');
        const previewGrade = container.querySelector('.preview-grade');
        
        if (letterInput && minScoreInput && maxScoreInput && gradePointInput && colorSelect && 
            previewLetter && previewRange && previewPoint) {
            
            // Update preview content
            previewLetter.textContent = letterInput.value || 'A';
            previewRange.textContent = `${parseFloat(minScoreInput.value || 0).toFixed(1)}-${parseFloat(maxScoreInput.value || 0).toFixed(1)}`;
            previewPoint.textContent = `GPA: ${parseFloat(gradePointInput.value || 0).toFixed(2)}`;
            
            // Update color class
            previewLetter.className = `h5 fw-bold preview-grade-letter ${colorSelect.value}`;
            
            // Update opacity based on status
            if (statusCheckbox && !statusCheckbox.checked) {
                previewGrade.style.opacity = '0.5';
                previewGrade.style.filter = 'grayscale(100%)';
            } else {
                previewGrade.style.opacity = '1';
                previewGrade.style.filter = 'none';
            }
        }
    }

    function updateAllGradePreviews() {
        // Update desktop table previews
        const tableRows = document.querySelectorAll('#grade-scales-table tr');
        tableRows.forEach(updateGradePreview);
        
        // Update mobile card previews
        const mobileCards = document.querySelectorAll('.grade-scale-card');
        mobileCards.forEach(updateGradePreview);
    }

    // Grade scale event listeners
    gradeLetterInputs.forEach(input => {
        input.addEventListener('input', function() {
            const container = this.closest('tr') || this.closest('.grade-scale-card');
            if (container) updateGradePreview(container);
        });
    });

    minScoreInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateGradeScales();
            const container = this.closest('tr') || this.closest('.grade-scale-card');
            if (container) updateGradePreview(container);
        });
    });

    maxScoreInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateGradeScales();
            const container = this.closest('tr') || this.closest('.grade-scale-card');
            if (container) updateGradePreview(container);
        });
    });

    gradePointInputs.forEach(input => {
        input.addEventListener('input', function() {
            const container = this.closest('tr') || this.closest('.grade-scale-card');
            if (container) updateGradePreview(container);
        });
    });

    gradeColorSelects.forEach(select => {
        select.addEventListener('change', function() {
            const container = this.closest('tr') || this.closest('.grade-scale-card');
            if (container) updateGradePreview(container);
        });
    });

    gradeStatusCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            validateGradeScales();
            const container = this.closest('tr') || this.closest('.grade-scale-card');
            if (container) updateGradePreview(container);
        });
    });

    // Initial grade scale calculations
    validateGradeScales();
    updateAllGradePreviews();
});
</script>
@endsection
