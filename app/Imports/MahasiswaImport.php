<?php

namespace App\Imports;

use App\Models\Mahasantri;
use App\Models\Akademik\ProgramStudi;
use App\Models\Akademik\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    protected $replaceExisting;
    protected $importStats = [
        'imported' => 0,
        'skipped' => 0,
        'updated' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
        'success_details' => []
    ];

    public function __construct($replaceExisting = false)
    {
        $this->replaceExisting = $replaceExisting;
    }

    public function getImportStats()
    {
        return $this->importStats;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        $importedCount = 0;

        try {
            foreach ($rows as $index => $row) {
                // Debug: Log row data with more detail
                Log::info('Processing row ' . ($index + 1), [
                    'raw_data' => $row->toArray(),
                    'program_studi_value' => $row['program studi'] ?? 'NOT_FOUND',
                    'all_keys' => array_keys($row->toArray())
                ]);

                // Skip completely empty rows
                if (empty($row['nama']) && empty($row['email']) && empty($row['nim'])) {
                    Log::warning('Skipping empty row ' . $index);
                    continue;
                }

                // Validate required fields first before processing
                $requiredFields = ['nama', 'email', 'nim'];
                $missingFields = [];
                foreach ($requiredFields as $field) {
                    if (empty(trim($row[$field] ?? ''))) {
                        $missingFields[] = $field;
                    }
                }
                if (count($missingFields) > 0) {
                    $this->importStats['failed']++;
                    $this->importStats['errors'][] = "Baris " . ($index + 2) . ": Field wajib kosong (" . implode(', ', $missingFields) . ")";
                    Log::warning('Skipping row due to missing required fields: ' . implode(', ', $missingFields));
                    continue;
                }

                // Find existing mahasiswa with strict identity matching:
                // 1) NIM (primary identity)
                // 2) Email (fallback)
                // NOTE: Do NOT match by name because duplicate names are common and can cause wrong updates.
                $nim = trim($row['nim'] ?? '');
                $emailInput = trim($row['email'] ?? '');
                $existing = null;

                if (!empty($nim)) {
                    $existing = Mahasantri::where('numb_nim', $nim)->first();
                }

                if (!$existing && !empty($emailInput)) {
                    $existing = Mahasantri::where('email', $emailInput)->first();
                }

                // Map Program Studi by name (case-insensitive and flexible matching)
                $prodi = null;
                $prodiName = null;
                
                // Try different possible column names for Program Studi
                $possibleProdiColumns = [
                    'program studi', 'program_studi', 'programstudi', 'prodi', 
                    'jurusan', 'fakultas', 'study program', 'major'
                ];
                
                foreach ($possibleProdiColumns as $column) {
                    if (!empty($row[$column])) {
                        $prodiName = trim($row[$column]);
                        Log::info("Found Program Studi in column '{$column}': {$prodiName}");
                        break;
                    }
                }
                
                if (!empty($prodiName)) {
                    // Try exact match first (case-insensitive)
                    $prodi = ProgramStudi::whereRaw('LOWER(name) = ?', [strtolower($prodiName)])->first();
                    
                    // If not found, try partial match
                    if (!$prodi) {
                        $prodi = ProgramStudi::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($prodiName) . '%'])->first();
                    }
                    
                    // If still not found, try reverse partial match
                    if (!$prodi) {
                        $prodi = ProgramStudi::whereRaw('LOWER(?) LIKE CONCAT("%", LOWER(name), "%")', [$prodiName])->first();
                    }
                    
                    if (!$prodi) {
                        Log::warning('Program Studi not found: ' . $prodiName);
                        $this->importStats['warnings'][] = "Baris " . ($index + 2) . ": Program Studi tidak ditemukan (" . $prodiName . ")";
                        
                        // Log available program studi for debugging
                        $availableProdi = ProgramStudi::pluck('name')->toArray();
                        Log::info('Available Program Studi: ' . implode(', ', $availableProdi));
                    } else {
                        Log::info('Found Program Studi: ' . $prodi->name . ' for: ' . $prodiName);
                    }
                } else {
                    Log::warning('No Program Studi column found in row ' . ($index + 2));
                    $this->importStats['warnings'][] = "Baris " . ($index + 2) . ": Kolom Program Studi tidak ditemukan";
                }

                // Map Kelas by name with improved matching logic
                $kelas = null;
                if (!empty($row['kelas'])) {
                    $kelasName = trim($row['kelas']);
                    Log::info('Processing kelas: ' . $kelasName);
                    
                    // Strategy 1: Try exact match with full name (including jenis kelas)
                    $kelas = $this->findKelasByFullName($kelasName);
                    
                    // Strategy 2: If not found, try parsing class name and jenis kelas separately
                    if (!$kelas) {
                        $kelas = $this->findKelasByParsedName($kelasName);
                    }
                    
                    // IMPORTANT:
                    // Do NOT use partial-match fallback because it can map
                    // "BAS 1 ... 2025" into "BAS 1 ... 2024" (wrong target class).
                    
                    if (!$kelas) {
                        Log::warning('Kelas not found with strict strategies: ' . $kelasName);
                        $this->importStats['failed']++;
                        $this->importStats['errors'][] = [
                            'row' => $index + 2,
                            'columns' => 'Kelas',
                            'errors' => "Kelas tidak ditemukan secara spesifik ({$kelasName}). Gunakan nama kelas yang sama persis dengan data master.",
                            'type' => 'invalid_reference'
                        ];

                        // Log available kelas for debugging
                        $availableKelas = Kelas::with('jenisKelas')->get()->map(function($k) {
                            $jenisKelas = $k->jenisKelas ? $k->jenisKelas->name : 'Reguler';
                            return $k->name . ' - ' . $jenisKelas;
                        })->toArray();
                        Log::info('Available Kelas: ' . implode(', ', $availableKelas));

                        // Skip this row to avoid wrong class assignment
                        continue;
                    } else {
                        $jenisKelas = $kelas->jenisKelas ? $kelas->jenisKelas->name : 'Reguler';
                        Log::info('Successfully matched kelas: ' . $kelas->name . ' - ' . $jenisKelas . ' for: ' . $kelasName);
                    }
                }

                // Handle password from Excel
                $password = $this->handlePassword($row['password'] ?? null);

                // Handle phone number - generate unique phone if empty or duplicate
                $phone = trim($row['no telepon'] ?? '');
                if (empty($phone)) {
                    // Generate unique phone number for empty values
                    $phone = '08' . time() . rand(100, 999);
                } else {
                    // If we're not replacing existing data and phone already exists (excluding current record)
                    if (!$this->replaceExisting) {
                        $phoneQuery = Mahasantri::where('phone', $phone);
                        if ($existing) {
                            $phoneQuery->where('id', '!=', $existing->id);
                        }
                        if ($phoneQuery->exists()) {
                            // Generate unique phone by appending timestamp
                            $phone = $phone . '_' . time() . rand(100, 999);
                            Log::info('Phone already exists, generated unique phone: ' . $phone);
                        }
                    }
                }

                // Handle email - generate unique email if duplicate
                $email = trim($row['email'] ?? '');
                if (empty($email)) {
                    // Generate unique email if empty
                    $email = 'mahasiswa' . time() . rand(100, 999) . '@example.com';
                } else {
                    // If we're not replacing existing data and email already exists (excluding current record)
                    if (!$this->replaceExisting) {
                        $emailQuery = Mahasantri::where('email', $email);
                        if ($existing) {
                            $emailQuery->where('id', '!=', $existing->id);
                        }
                        if ($emailQuery->exists()) {
                            // Generate unique email by appending timestamp
                            $emailParts = explode('@', $email);
                            $email = $emailParts[0] . '_' . time() . rand(100, 999) . '@' . $emailParts[1];
                            Log::info('Email already exists, generated unique email: ' . $email);
                        }
                    }
                }

                $data = [
                    'code' => 'MHS-' . uniqid(),
                    'password' => $password,
                    'name' => trim($row['nama'] ?? ''),
                    'email' => $email,
                    'phone' => $phone,
                    'numb_nim' => trim($row['nim'] ?? ''),
                    'prodi_id' => $prodi ? $prodi->id : null,
                    'kelas_id' => $kelas ? $kelas->id : null,
                    'semester' => $row['semester'] ?? null,
                    'takhasus' => $row['takhasus'] ?? null,
                    'type' => $this->mapMahasiswaType($row['type mahasiswa'] ?? null) ?? 1, // Default to Mahasantri Aktif
                    'bio_gender' => $row['jenis kelamin'] ?? null,
                    'bio_placebirth' => $row['tempat lahir'] ?? null,
                    'bio_datebirth' => $row['tanggal lahir'] ?? null,
                    'bio_religion' => $row['agama'] ?? null,
                    'bio_nationality' => $row['kewarganegaraan'] ?? null,
                    'numb_ktp' => $row['no ktp nik'] ?? null,
                    'numb_kk' => $row['no kartu keluarga'] ?? null,
                    'numb_nisn' => $row['nisn'] ?? null,
                    'ktp_addres' => $row['alamat ktp'] ?? null,
                    'ktp_rt' => $row['rt'] ?? null,
                    'ktp_rw' => $row['rw'] ?? null,
                    'ktp_village' => $row['kelurahan desa'] ?? null,
                    'ktp_subdistrict' => $row['kecamatan'] ?? null,
                    'ktp_city' => $row['kota kabupaten'] ?? null,
                    'ktp_province' => $row['provinsi'] ?? null,
                    'ktp_poscode' => $row['kode pos'] ?? null,
                    'father_name' => $row['nama ayah'] ?? null,
                    'father_phone' => $row['no telepon ayah'] ?? null,
                    'father_occupation' => $row['pekerjaan ayah'] ?? null,
                    'mother_name' => $row['nama ibu'] ?? null,
                    'mother_phone' => $row['no telepon ibu'] ?? null,
                    'mother_occupation' => $row['pekerjaan ibu'] ?? null,
                    'edu1_type' => $this->mapEdu1Type($row['jenjang pendidikan terakhir'] ?? null) ?? 'SMA/SMK',
                    'edu1_place' => $row['nama sekolah universitas'] ?? null,
                    'edu1_major' => $row['jurusan pendidikan terakhir'] ?? null,
                    'edu1_graduate_year' => $row['tahun lulus pendidikan terakhir'] ?? null,
                    'updated_by' => auth()->id() ?? null,
                    'created_by' => auth()->id() ?? null,
                ];

                try {
                    if ($existing) {
                        if ($this->replaceExisting) {
                            // When replacing, we need to handle potential conflicts with other records
                            // Check for phone conflicts with other records (not the current one)
                            $phoneConflict = Mahasantri::where('phone', $phone)
                                ->where('id', '!=', $existing->id)
                                ->first();
                            
                            if ($phoneConflict) {
                                // Generate unique phone to avoid conflict
                                $phone = $phone . '_' . time() . rand(100, 999);
                                $data['phone'] = $phone;
                                Log::info('Phone conflict detected during replace, generated unique phone: ' . $phone);
                            }
                            
                            // Check for email conflicts with other records (not the current one)
                            $emailConflict = Mahasantri::where('email', $email)
                                ->where('id', '!=', $existing->id)
                                ->first();
                            
                            if ($emailConflict) {
                                // Generate unique email to avoid conflict
                                $emailParts = explode('@', $email);
                                $email = $emailParts[0] . '_' . time() . rand(100, 999) . '@' . $emailParts[1];
                                $data['email'] = $email;
                                Log::info('Email conflict detected during replace, generated unique email: ' . $email);
                            }
                            
                            $existing->update($data);
                            $this->importStats['updated']++;
                            $this->importStats['success_details'][] = [
                                'row' => $index + 2,
                                'action' => 'updated',
                                'message' => 'Data mahasiswa berhasil diperbarui',
                                'type' => 'info',
                                'name' => $data['name'],
                                'nim' => $data['numb_nim'],
                                'email' => $data['email']
                            ];
                            Log::info('Updated existing mahasiswa: ' . $data['name']);
                        } else {
                            $this->importStats['skipped']++;
                            $this->importStats['success_details'][] = [
                                'row' => $index + 2,
                                'action' => 'skipped',
                                'message' => 'Data mahasiswa dilewati (duplikat)',
                                'type' => 'warning',
                                'name' => $data['name'],
                                'nim' => $data['numb_nim'],
                                'email' => $data['email']
                            ];
                            $this->importStats['warnings'][] = "Baris " . ($index + 2) . ": Data sudah ada, dilewati (" . $data['name'] . ")";
                            Log::info('Skipping existing mahasiswa: ' . $data['name']);
                        }
                    } else {
                        // For new records, check for conflicts and generate unique values if needed
                        $phoneConflict = Mahasantri::where('phone', $phone)->first();
                        if ($phoneConflict) {
                            $phone = $phone . '_' . time() . rand(100, 999);
                            $data['phone'] = $phone;
                            Log::info('Phone conflict detected for new record, generated unique phone: ' . $phone);
                        }
                        
                        $emailConflict = Mahasantri::where('email', $email)->first();
                        if ($emailConflict) {
                            $emailParts = explode('@', $email);
                            $email = $emailParts[0] . '_' . time() . rand(100, 999) . '@' . $emailParts[1];
                            $data['email'] = $email;
                            Log::info('Email conflict detected for new record, generated unique email: ' . $email);
                        }
                        
                        $mahasiswa = Mahasantri::create($data);
                        $this->importStats['imported']++;
                        $this->importStats['success_details'][] = [
                            'row' => $index + 2,
                            'action' => 'imported',
                            'message' => 'Data mahasiswa berhasil diimport',
                            'type' => 'success',
                            'name' => $data['name'],
                            'nim' => $data['numb_nim'],
                            'email' => $data['email']
                        ];
                        Log::info('Created new mahasiswa: ' . $data['name'] . ' with ID: ' . $mahasiswa->id);
                    }
                } catch (\Exception $rowException) {
                    $this->importStats['failed']++;
                    $errorMessage = $rowException->getMessage();
                    
                    // If it's still a constraint violation, try one more time with completely unique values
                    if (strpos($errorMessage, 'Integrity constraint violation') !== false) {
                        try {
                            // Generate completely unique phone and email
                            $data['phone'] = '08' . time() . rand(1000, 9999);
                            $data['email'] = 'import_' . time() . rand(1000, 9999) . '@generated.com';
                            
                            if ($existing && $this->replaceExisting) {
                                $existing->update($data);
                                $this->importStats['updated']++;
                                $this->importStats['failed']--; // Remove from failed count
                                Log::info('Retry successful - Updated existing mahasiswa with unique values: ' . $data['name']);
                            } else if (!$existing) {
                                $mahasiswa = Mahasantri::create($data);
                                $this->importStats['imported']++;
                                $this->importStats['failed']--; // Remove from failed count
                                Log::info('Retry successful - Created new mahasiswa with unique values: ' . $data['name']);
                            }
                        } catch (\Exception $retryException) {
                            $this->importStats['errors'][] = [
                                'row' => $index + 2,
                                'columns' => 'Phone/Email',
                                'errors' => 'Gagal mengatasi duplikasi data: ' . $retryException->getMessage(),
                                'type' => 'duplicate_data'
                            ];
                            Log::error('Retry failed for row ' . ($index + 2) . ': ' . $retryException->getMessage());
                        }
                    } else {
                        $this->importStats['errors'][] = [
                            'row' => $index + 2,
                            'columns' => 'Data tidak valid',
                            'errors' => $errorMessage,
                            'type' => 'validation'
                        ];
                        Log::error('Failed to process row ' . ($index + 2) . ': ' . $errorMessage);
                    }
                }
            }

            DB::commit();
            Log::info('Import completed successfully. Total imported: ' . $importedCount);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Map education level from Excel to database ENUM values
     * 
     * @param string|null $educationLevel
     * @return string|null
     */
    private function mapEdu1Type($educationLevel)
    {
        if (empty($educationLevel)) {
            return null;
        }

        // Convert to lowercase and trim for case-insensitive matching
        $educationLevel = strtolower(trim($educationLevel));

        // Define mapping patterns
        $mappings = [
            'SMA/SMK' => [
                'sma', 'smk', 'sma/smk', 'sekolah menengah atas', 'sekolah menengah kejuruan',
                'ma', 'mak', 'madrasah aliyah', 'madrasah aliyah kejuruan',
                'high school', 'senior high school', 'vocational high school'
            ],
            'Diploma' => [
                'diploma', 'd1', 'd2', 'd3', 'd4', 'diploma 1', 'diploma 2', 'diploma 3', 'diploma 4',
                'ahli madya', 'sarjana terapan', 'associate degree', 'polytechnic'
            ],
            'Sarjana' => [
                'sarjana', 's1', 'strata 1', 'strata satu', 'bachelor', 'bachelor degree',
                'sarjana pendidikan', 'sarjana ekonomi', 'sarjana teknik', 'sarjana hukum',
                'sarjana komputer', 'sarjana sains', 'undergraduate'
            ],
            'Magister' => [
                'magister', 's2', 'strata 2', 'strata dua', 'master', 'master degree',
                'magister pendidikan', 'magister manajemen', 'magister teknik', 'magister hukum',
                'magister komputer', 'magister sains', 'graduate', 'postgraduate'
            ],
            'Doktor' => [
                'doktor', 's3', 'strata 3', 'strata tiga', 'phd', 'ph.d', 'doctor', 'doctorate',
                'doktor pendidikan', 'doktor teknik', 'doktor hukum', 'doktor sains'
            ]
        ];

        // Check each mapping
        foreach ($mappings as $enumValue => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($educationLevel, $pattern) !== false) {
                    return $enumValue;
                }
            }
        }

        // If no match found, return null (will use default 'SMA/SMK')
        return null;
    }

    /**
     * Map mahasiswa type from Excel to database integer values
     * 
     * @param string|null $mahasiswaType
     * @return int|null
     */
    private function mapMahasiswaType($mahasiswaType)
    {
        if (empty($mahasiswaType)) {
            return null;
        }

        // Convert to lowercase and trim for case-insensitive matching
        $mahasiswaType = strtolower(trim($mahasiswaType));

        // Define mapping patterns based on Mahasantri model
        $mappings = [
            0 => [ // Calon Mahasantri Baru
                'calon mahasiswa baru', 'camaba', 'calon mahasiswa', 'pendaftar', 'new student'
            ],
            1 => [ // Mahasantri Aktif
                'mahasiswa aktif', 'aktif', 'active', 'active student', 'mahasiswa'
            ],
            2 => [ // Mahasantri Non-Aktif
                'mahasiswa non-aktif', 'non-aktif', 'nonaktif', 'inactive', 'inactive student', 'cuti'
            ],
            3 => [ // Mahasantri Alumni
                'mahasiswa alumni', 'alumni', 'graduate', 'graduated', 'lulus'
            ]
        ];

        // Check each mapping
        foreach ($mappings as $typeValue => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($mahasiswaType, $pattern) !== false) {
                    return $typeValue;
                }
            }
        }

        // If no match found, return null (will use default 1 = Mahasantri Aktif)
        return null;
    }

    /**
     * Handle password from Excel - detect if encrypted or plain text
     * 
     * @param string|null $password
     * @return string
     */
    private function handlePassword($password)
    {
        // If password is empty, use default
        if (empty($password)) {
            return bcrypt('12345678'); // Default password
        }

        $password = trim($password);

        // Check if password is already encrypted (Laravel bcrypt starts with $2y$)
        if (preg_match('/^\$2[ayb]\$.{56}$/', $password)) {
            // Password is already encrypted, use as is
            Log::info('Using encrypted password from Excel');
            return $password;
        } else {
            // Password is plain text, encrypt it
            Log::info('Encrypting plain text password from Excel: ' . $password);
            return bcrypt($password);
        }
    }

    /**
     * Strategy 1: Find kelas by exact full name match (including jenis kelas)
     * This handles cases where Excel contains "bas 1 - reg A" format
     * 
     * @param string $kelasName
     * @return \App\Models\Akademik\Kelas|null
     */
    private function findKelasByFullName($kelasName)
    {
        // Try to find kelas where the combination of name + jenis kelas matches
        $kelas = Kelas::with('jenisKelas')->get()->first(function($k) use ($kelasName) {
            $jenisKelas = $k->jenisKelas ? $k->jenisKelas->name : 'Reguler';
            $fullName = $k->name . ' - ' . $jenisKelas;
            
            // Case-insensitive exact match
            return strtolower(trim($fullName)) === strtolower(trim($kelasName));
        });

        if ($kelas) {
            Log::info('Found kelas by full name match: ' . $kelas->name . ' - ' . ($kelas->jenisKelas ? $kelas->jenisKelas->name : 'Reguler'));
        }

        return $kelas;
    }

    /**
     * Strategy 2: Find kelas by parsing class name and jenis kelas separately
     * This handles cases where we need to extract parts from "bas 1 - reg A"
     * 
     * @param string $kelasName
     * @return \App\Models\Akademik\Kelas|null
     */
    private function findKelasByParsedName($kelasName)
    {
        // Parse the kelas name to extract base name and jenis kelas
        if (strpos($kelasName, ' - ') !== false) {
            $parts = explode(' - ', $kelasName, 2);
            $baseName = trim($parts[0]);
            $jenisKelasName = trim($parts[1]);
            
            Log::info("Parsing kelas: base='{$baseName}', jenis='{$jenisKelasName}'");
            
            // Find kelas by base name and jenis kelas
            $kelas = Kelas::with('jenisKelas')
                ->whereRaw('LOWER(name) = ?', [strtolower($baseName)])
                ->get()
                ->first(function($k) use ($jenisKelasName) {
                    if (!$k->jenisKelas) {
                        return strtolower('reguler') === strtolower($jenisKelasName);
                    }
                    return strtolower($k->jenisKelas->name) === strtolower($jenisKelasName);
                });

            if ($kelas) {
                Log::info('Found kelas by parsed name: ' . $kelas->name . ' - ' . ($kelas->jenisKelas ? $kelas->jenisKelas->name : 'Reguler'));
                return $kelas;
            }
            
            // Try partial matching on jenis kelas name
            $kelas = Kelas::with('jenisKelas')
                ->whereRaw('LOWER(name) = ?', [strtolower($baseName)])
                ->get()
                ->first(function($k) use ($jenisKelasName) {
                    if (!$k->jenisKelas) {
                        return strpos(strtolower('reguler'), strtolower($jenisKelasName)) !== false ||
                               strpos(strtolower($jenisKelasName), strtolower('reguler')) !== false;
                    }
                    return strpos(strtolower($k->jenisKelas->name), strtolower($jenisKelasName)) !== false ||
                           strpos(strtolower($jenisKelasName), strtolower($k->jenisKelas->name)) !== false;
                });

            if ($kelas) {
                Log::info('Found kelas by parsed name (partial jenis match): ' . $kelas->name . ' - ' . ($kelas->jenisKelas ? $kelas->jenisKelas->name : 'Reguler'));
            }

            return $kelas;
        }

        return null;
    }

}
