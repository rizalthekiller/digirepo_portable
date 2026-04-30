<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Setting;
use App\Jobs\ProcessThesisApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ... dashboard and queue methods ...

    /**
     * Master Data: Fakultas
     */
    public function faculties()
    {
        $faculties = Faculty::withCount('departments')->paginate(10);
        return view('admin.master.faculties', compact('faculties'));
    }

    public function storeFaculty(Request $request)
    {
        $request->validate(['name' => 'required', 'level' => 'required', 'code' => 'nullable']);
        Faculty::create($request->all());
        return back()->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function updateFaculty(Request $request, Faculty $faculty)
    {
        $request->validate(['name' => 'required', 'level' => 'required', 'code' => 'nullable']);
        $faculty->update($request->all());
        return back()->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroyFaculty(Faculty $faculty)
    {
        if ($faculty->departments()->count() > 0) {
            return back()->with('error', 'Fakultas tidak dapat dihapus karena masih memiliki Program Studi terkait.');
        }
        $faculty->delete();
        return back()->with('success', 'Fakultas berhasil dihapus.');
    }

    /**
     * Master Data: Program Studi
     */
    public function departments()
    {
        $faculties = Faculty::all();
        $departments = Department::with('faculty')->paginate(10);
        return view('admin.master.departments', compact('departments', 'faculties'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required', 'faculty_id' => 'required|exists:faculties,id', 'level' => 'required', 'code' => 'required']);
        Department::create($request->all());
        return back()->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate(['name' => 'required', 'faculty_id' => 'required|exists:faculties,id', 'level' => 'required', 'code' => 'required']);
        $department->update($request->all());
        return back()->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroyDepartment(Department $department)
    {
        $department->delete();
        return back()->with('success', 'Program Studi berhasil dihapus.');
    }

    /**
     * Manajemen User
     */
    public function users(Request $request)
    {
        $query = User::with('department.faculty');

        if ($request->role_group === 'admins') {
            $query->whereIn('role', ['superadmin', 'admin']);
        } else {
            // Default to showing general users (mahasiswa, dosen, guest)
            $query->whereIn('role', ['mahasiswa', 'dosen', 'guest']);
        }

        $users = $query->latest()->paginate(10);
        $departments = Department::with('faculty')->get();
        return view('admin.users.index', compact('users', 'departments'));
    }

    public function verifyUser(User $user)
    {
        $user->update(['is_verified' => true]);
        return back()->with('success', "User {$user->name} telah diverifikasi.");
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|unique:users,nim',
            'role' => 'required|in:superadmin,admin,dosen,mahasiswa,guest',
            'password' => 'required|min:8',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nim' => $request->nim,
            'role' => $request->role,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'department_id' => $request->department_id,
            'is_verified' => true, 
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nim' => 'required|string|unique:users,nim,' . $user->id,
            'role' => 'required|in:superadmin,admin,dosen,mahasiswa,guest',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $user->update($request->only('name', 'email', 'nim', 'role', 'department_id'));

        return back()->with('success', "Data user {$user->name} berhasil diperbarui.");
    }

    public function updateUserPassword(Request $request, User $user)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya Superadmin yang dapat mengubah password user.');
        }

        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return back()->with('success', "Password untuk user {$user->name} berhasil diperbarui.");
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        $user->delete();
        return back()->with('success', "User telah berhasil dihapus dari sistem.");
    }

    /**
     * Data Surat / Sertifikat
     */
    public function certificates(Request $request)
    {
        $query = Thesis::where('status', 'approved')
            ->whereHas('user', function($q) {
                $q->where('role', '!=', 'dosen');
            })
            ->with('user.department');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $theses = $query->latest()->paginate(10)->withQueryString();
        return view('admin.certificates.index', compact('theses'));
    }

    /**
     * Manajemen Skripsi (All Status)
     */
    public function theses(Request $request)
    {
        $query = Thesis::with('user.department')->withCount('downloads');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('file_status')) {
            if ($request->file_status === 'exists') {
                $query->whereNotNull('file_path');
            } elseif ($request->file_status === 'missing') {
                $query->whereNull('file_path');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $theses = $query->latest()->paginate(10)->withQueryString();
        return view('admin.theses.index', compact('theses'));
    }



    /**
     * Pengaturan Sertifikat - Tampilkan
     */
    public function certificateSettings()
    {
        $settings = [
            'cert_institution_name' => Setting::get('cert_institution_name', 'UNIVERSITAS ISLAM NEGERI'),
            'cert_institution_ministry' => Setting::get('cert_institution_ministry', 'KEMENTERIAN AGAMA REPUBLIK INDONESIA'),
            'cert_institution_sub' => Setting::get('cert_institution_sub', 'SULTAN AJI MUHAMMAD IDRIS SAMARINDA'),
            'cert_institution_unit' => Setting::get('cert_institution_unit', 'UPT. PERPUSTAKAAN'),
            'cert_institution_address' => Setting::get('cert_institution_address', 'Jalan H.A.M. Rifaddin Samarinda 75131 Samarinda'),
            'cert_institution_phone' => Setting::get('cert_institution_phone', '(0541) 7270222'),
            'cert_institution_fax' => Setting::get('cert_institution_fax', '(0541) 4114393'),
            'cert_institution_city' => Setting::get('cert_institution_city', 'Samarinda'),
            'cert_institution_email' => Setting::get('cert_institution_email', 'perpustakaan@uinsi.ac.id'),
            'cert_institution_website' => Setting::get('cert_institution_website', 'http://lib.uinsi.ac.id'),
            'cert_logo_path' => Setting::get('cert_logo_path', ''),
            'cert_number_format' => Setting::get('cert_number_format', 'Perpus.B-{ID}/Un.21/1/PP.009/{ROMAN}/{YEAR}'),
            'cert_opening_text' => Setting::get('cert_opening_text', 'Kepala Perpustakaan Universitas Islam Negeri Sultan Aji Muhammad Idris (UINSI) Samarinda menerangkan bahwa :'),
            'cert_main_content' => Setting::get('cert_main_content', 'Yang bersangkutan telah selesai melakukan unggah Skripsi/Disertasi pada Repositori Perpustakaan Universitas Islam Negeri Sultan Aji Muhammad Idris (UINSI) Samarinda.'),
            'cert_closing_content' => Setting::get('cert_closing_content', 'Demikian Surat Keterangan ini diberikan, agar dapat dipergunakan sebagaimana mestinya.'),
            'cert_signatory_prefix' => Setting::get('cert_signatory_prefix', 'Plt.'),
            'cert_signatory_title' => Setting::get('cert_signatory_title', 'Kepala UPT. Perpustakaan'),
            'cert_signatory_name' => Setting::get('cert_signatory_name', 'Administrator'),
            'cert_signatory_nip' => Setting::get('cert_signatory_nip', '19800101 200501 1 001'),
            'cert_issued_city' => Setting::get('cert_issued_city', 'Samarinda'),
        ];
        return view('admin.settings.certificates', compact('settings'));
    }

    /**
     * Pengaturan Sertifikat - Simpan
     */
    public function updateCertificateSettings(Request $request)
    {
        $keys = [
            'cert_number_format', 'cert_opening_text', 'cert_main_content', 
            'cert_closing_content', 'cert_signatory_prefix', 'cert_signatory_title', 
            'cert_signatory_name', 'cert_signatory_nip', 'cert_issued_city',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key, ''));
            }
        }

        // Handle Kop Surat (Logo) Upload (Smart Upload)
        if ($request->hasFile('cert_logo')) {
            // Delete old file if exists
            $oldPath = Setting::get('cert_logo_path');
            if ($oldPath && file_exists(public_path($oldPath))) {
                @unlink(public_path($oldPath));
            }

            $file = $request->file('cert_logo');
            $fileName = 'kop_surat_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            
            $path = 'images/' . $fileName;
            Setting::set('cert_logo_path', $path);
            
            return back()->with('success', 'Kop surat berhasil diperbarui (Smart Upload).');
        }

        return back()->with('success', 'Pengaturan sertifikat berhasil disimpan.');
    }

    /**
     * Pengaturan Situs - Tampilkan
     */
    public function siteSettings()
    {
        $settings = [
            'site_name'        => Setting::get('site_name', 'DigiRepo'),
            'site_tagline'     => Setting::get('site_tagline', 'Sistem Repositori Digital Perpustakaan'),
            'site_institution' => Setting::get('site_institution', 'Universitas'),
            'site_address'     => Setting::get('site_address', 'Jl. Kampus Terpadu No. 1'),
            'site_city'        => Setting::get('site_city', 'Padang'),
            'site_email'       => Setting::get('site_email', 'repo@universitas.ac.id'),
            'site_website'     => Setting::get('site_website', 'digirepo.universitas.ac.id'),
            'site_footer_text' => Setting::get('site_footer_text', ''),
            'site_logo_path'   => Setting::get('site_logo_path', ''),
            'site_favicon_path' => Setting::get('site_favicon_path', ''),
            'site_watermark_path' => Setting::get('site_watermark_path', ''),
            'site_hero_title'  => Setting::get('site_hero_title', ''),
        ];
        return view('admin.settings.site', compact('settings'));
    }

    /**
     * Pengaturan Situs - Simpan
     */
    public function updateSiteSettings(Request $request)
    {
        $keys = [
            'site_name', 'site_tagline', 'site_institution',
            'site_address', 'site_city', 'site_email',
            'site_website', 'site_footer_text', 'site_hero_title',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key, ''));
            }
        }

        // Handle File Uploads (Smart Upload)
        $files = [
            'site_logo'      => 'site_logo_path',
            'site_favicon'   => 'site_favicon_path',
            'site_watermark' => 'site_watermark_path',
        ];

        $uploadedCount = 0;
        foreach ($files as $inputName => $settingKey) {
            if ($request->hasFile($inputName)) {
                // Delete old file if exists
                $oldPath = Setting::get($settingKey);
                if ($oldPath && file_exists(public_path($oldPath))) {
                    @unlink(public_path($oldPath));
                }

                $file = $request->file($inputName);
                $fileName = $inputName . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $fileName);
                Setting::set($settingKey, 'images/' . $fileName);
                $uploadedCount++;
            }
        }

        // Handle App Environment Toggle
        if ($request->has('app_environment')) {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);
                
                if ($request->app_environment === 'production') {
                    $envContent = preg_replace('/^APP_ENV=.*$/m', 'APP_ENV=production', $envContent);
                    $envContent = preg_replace('/^APP_DEBUG=.*$/m', 'APP_DEBUG=false', $envContent);
                } elseif ($request->app_environment === 'developer') {
                    $envContent = preg_replace('/^APP_ENV=.*$/m', 'APP_ENV=local', $envContent);
                    $envContent = preg_replace('/^APP_DEBUG=.*$/m', 'APP_DEBUG=true', $envContent);
                }
                
                file_put_contents($envPath, $envContent);
                try {
                    \Illuminate\Support\Facades\Artisan::call('config:clear');
                } catch (\Exception $e) {
                    // Ignore exceptions
                }
            }
        }

        $message = $uploadedCount > 0 
            ? "Pengaturan berhasil disimpan ($uploadedCount file diperbarui dengan Smart Upload)."
            : "Pengaturan situs berhasil disimpan.";

        return back()->with('success', $message);
    }

    public function restartQueue()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('queue:restart');
            return back()->with('success', 'Sinyal restart antrean (queue) berhasil dikirim. Pekerja (worker) akan memuat ulang kode dalam beberapa detik.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal merestart antrean: ' . $e->getMessage());
        }
    }

    public function downloadDatabase()
    {
        try {
            $dbConfig = config('database.connections.mysql');
            $filename = "backup_db_" . date('Y-m-d_H-i-s') . ".sql";
            $path = storage_path('app/' . $filename);

            // Path Detection
            $dumpBinary = 'mysqldump';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                if (file_exists('C:\xampp\mysql\bin\mysqldump.exe')) {
                    $dumpBinary = 'C:\xampp\mysql\bin\mysqldump.exe';
                }
            }

            // Build command
            $passwordArg = $dbConfig['password'] !== '' ? '--password=' . escapeshellarg($dbConfig['password']) : '';
            $command = sprintf(
                '%s --user=%s %s --host=%s %s > %s',
                $dumpBinary,
                escapeshellarg($dbConfig['username']),
                $passwordArg,
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($path)
            );

            // Execute
            $output = [];
            $returnVar = -1;
            exec($command, $output, $returnVar);

            // If first attempt failed and on Windows, try another common path just in case
            if ($returnVar !== 0 && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && $dumpBinary === 'mysqldump') {
                // Fallback attempt with full XAMPP path if we haven't tried it
                $dumpBinary = 'C:\xampp\mysql\bin\mysqldump.exe';
                $command = sprintf(
                    '%s --user=%s %s --host=%s %s > %s',
                    $dumpBinary,
                    escapeshellarg($dbConfig['username']),
                    $passwordArg,
                    escapeshellarg($dbConfig['host']),
                    escapeshellarg($dbConfig['database']),
                    escapeshellarg($path)
                );
                exec($command, $output, $returnVar);
            }

            if ($returnVar !== 0) {
                return back()->with('error', 'Gagal membuat backup. Sistem tidak dapat menemukan perintah "mysqldump". Jika di server aaPanel, pastikan MySQL Client terinstal.');
            }

            if (!file_exists($path)) {
                return back()->with('error', 'Gagal: File backup tidak ditemukan setelah proses selesai.');
            }

            return response()->download($path)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reports()
    {
        // Monthly Trends (Theses - Last 6 Months)
        $monthlyTrends = Thesis::selectRaw('MONTHNAME(created_at) as month, count(*) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('created_at', 'asc')
            ->get();

        // Visit Trends (Last 12 Months)
        $visitTrends = \App\Models\Visit::selectRaw('MONTHNAME(created_at) as month, count(*) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('created_at', 'asc')
            ->get();

        // Status Distribution
        $statusStats = [
            'approved' => Thesis::where('status', 'approved')->count(),
            'pending' => Thesis::where('status', 'pending')->count(),
            'rejected' => Thesis::where('status', 'rejected')->count(),
        ];

        // Type Distribution
        $typeStats = Thesis::selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->get();

        return view('admin.reports.index', compact('monthlyTrends', 'visitTrends', 'statusStats', 'typeStats'));
    }

    public function exportReports(Request $request)
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return redirect()->back()->with('error', 'Gagal: Library PhpSpreadsheet tidak ditemukan.');
        }

        $startDate = $request->start_date ? $request->start_date . ' 00:00:00' : '2000-01-01 00:00:00';
        $endDate = $request->end_date ? $request->end_date . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $totalVisit = \App\Models\Visit::whereBetween('created_at', [$startDate, $endDate])->count();
        $approved = Thesis::where('status', 'approved')->whereBetween('created_at', [$startDate, $endDate])->count();
        $pending = Thesis::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count();
        $rejected = Thesis::where('status', 'rejected')->whereBetween('created_at', [$startDate, $endDate])->count();

        $typeStats = Thesis::selectRaw('type, count(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('type')
            ->get();

        $monthlyTrends = Thesis::selectRaw('MONTHNAME(created_at) as month, count(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->get();

        $visitTrends = \App\Models\Visit::selectRaw('MONTHNAME(created_at) as month, count(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Statistik');

        // Styles
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e3a8a']
            ],
        ];

        // Section 1: Ringkasan
        $sheet->setCellValue('A1', 'RINGKASAN STATISTIK REPOSITORI');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?: 'Awal') . ' s/d ' . ($request->end_date ?: 'Sekarang'));
        
        $sheet->setCellValue('A4', 'METRIK');
        $sheet->setCellValue('B4', 'TOTAL');
        $sheet->getStyle('A4:B4')->applyFromArray($headerStyle);
        
        $sheet->setCellValue('A5', 'Total Kunjungan');
        $sheet->setCellValue('B5', $totalVisit);
        $sheet->setCellValue('A6', 'Dokumen Disetujui');
        $sheet->setCellValue('B6', $approved);
        $sheet->setCellValue('A7', 'Dokumen Menunggu Review');
        $sheet->setCellValue('B7', $pending);
        $sheet->setCellValue('A8', 'Dokumen Ditolak/Revisi');
        $sheet->setCellValue('B8', $rejected);

        // Section 2: Distribusi Tipe
        $sheet->setCellValue('D4', 'TIPE DOKUMEN');
        $sheet->setCellValue('E4', 'TOTAL');
        $sheet->getStyle('D4:E4')->applyFromArray($headerStyle);
        
        $row = 5;
        foreach($typeStats as $type) {
            $sheet->setCellValue('D'.$row, $type->type);
            $sheet->setCellValue('E'.$row, $type->total);
            $row++;
        }

        // Section 3: Tren Unggahan Bulanan
        $startRow = max(10, $row + 2);
        $sheet->setCellValue('A'.$startRow, 'BULAN');
        $sheet->setCellValue('B'.$startRow, 'TOTAL UNGGAHAN');
        $sheet->getStyle('A'.$startRow.':B'.$startRow)->applyFromArray($headerStyle);

        $row = $startRow + 1;
        foreach($monthlyTrends as $trend) {
            $sheet->setCellValue('A'.$row, $trend->month);
            $sheet->setCellValue('B'.$row, $trend->total);
            $row++;
        }

        // Section 4: Tren Kunjungan Bulanan
        $sheet->setCellValue('D'.$startRow, 'BULAN');
        $sheet->setCellValue('E'.$startRow, 'TOTAL KUNJUNGAN');
        $sheet->getStyle('D'.$startRow.':E'.$startRow)->applyFromArray($headerStyle);

        $row = $startRow + 1;
        foreach($visitTrends as $visit) {
            $sheet->setCellValue('D'.$row, $visit->month);
            $sheet->setCellValue('E'.$row, $visit->total);
            $row++;
        }

        // Autosize
        foreach(range('A','E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "Laporan_Statistik_" . date('Ymd_His') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function dashboard()
    {
        $totalTheses = Thesis::count();
        $pendingTheses = Thesis::where('status', 'pending')->count();
        $totalUsers = User::where('role', 'mahasiswa')->count();
        $totalFaculties = Faculty::count();
        $recentTheses = Thesis::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalTheses', 
            'pendingTheses', 
            'totalUsers', 
            'totalFaculties', 
            'recentTheses'
        ));
    }

    public function queue(Request $request)
    {
        $query = Thesis::with('user.department.faculty')
            ->where('status', 'pending');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('nim', 'like', "%{$search}%");
                  });
            });
        }

        $pendingTheses = $query->orderBy('created_at', 'asc')->paginate(10);

        return view('admin.queue', compact('pendingTheses'));
    }

    public function approve(Request $request, Thesis $thesis)
    {
        $fullNumber = null;
        $certContent = null;

        if ($thesis->user && !$thesis->user->isDosen()) {
            $format = Setting::get('cert_number_format', 'Perpus.B-{ID}/Un.21/1/PP.009/{ROMAN}/{YEAR}');
            $seq = $request->cert_number_seq ?? '000';
            $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            
            $fullNumber = str_replace(
                ['{ID}', '{YEAR}', '{MONTH}', '{ROMAN}'],
                [$seq, date('Y'), date('m'), $romanMonths[intval(date('n'))]],
                $format
            );
            $certContent = $request->cert_content;
        }

        $thesis->update([
            'status' => 'approved',
            'certificate_number' => $fullNumber,
            'certificate_content' => $certContent,
            'certificate_date' => now(),
            'verification_hash' => bin2hex(random_bytes(16)),
            'embargo_until' => $request->embargo_until,
        ]);

        // Trigger Student Notification
        if ($thesis->user) {
            $thesis->user->notify(new \App\Notifications\ThesisNotification($thesis, 'approved'));
        }

        // Dispatch Job to Background (Handles Watermark & Email)
        ProcessThesisApproval::dispatch($thesis);

        return redirect()->back()->with('success', 'Skripsi telah disetujui. Proses pembuatan sertifikat dan pengiriman email sedang berjalan di background.');
    }

    public function reject(Request $request, Thesis $thesis)
    {
        $thesis->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'was_rejected' => true,
        ]);

        // Trigger Student Notification
        if ($thesis->user) {
            $thesis->user->notify(new \App\Notifications\ThesisNotification($thesis, 'rejected'));
        }
        
        return redirect()->back()->with('success', 'Skripsi telah ditolak.');
    }

    public function resetStatus(Thesis $thesis)
    {
        $thesis->update([
            'status' => 'pending',
            'certificate_number' => null,
            'certificate_content' => null,
            'verification_hash' => null,
            'rejection_reason' => null
        ]);

        return redirect()->back()->with('success', 'Dokumen telah dikembalikan ke antrean verifikasi.');
    }

    public function updateThesis(Request $request, Thesis $thesis)
    {
        $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'year' => 'required|integer',
            'abstract' => 'required|string',
            'supervisor_name' => 'required|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $data = $request->only(['title', 'type', 'year', 'abstract', 'supervisor_name', 'keywords']);

        // Handle File Replacement (Smart Replace)
        if ($request->hasFile('pdf_file')) {
            $user = $thesis->user;
            $dept = $user->department;
            $facName = \Illuminate\Support\Str::slug($dept->faculty->name ?? 'Unknown_Faculty', '-');
            $deptName = \Illuminate\Support\Str::slug($dept->name ?? 'Unknown_Dept', '-');
            $typePath = \Illuminate\Support\Str::slug($request->type, '-');
            $yearPath = $request->year;
            $nim = \Illuminate\Support\Str::slug($user->nim ?? 'Unknown_NIM', '-');
            $nameSlug = \Illuminate\Support\Str::slug($user->name, '-');
            
            if (in_array($request->type, ['Skripsi', 'Thesis', 'Disertasi'])) {
                $folderPath = "{$yearPath}/{$typePath}/{$facName}/{$deptName}/{$nim}";
                $fileName = "{$nim}.pdf";
            } else {
                $folderPath = "{$typePath}/{$yearPath}/{$nim}";
                $fileName = "{$nim}_{$nameSlug}.pdf";
            }
            
            // Smart Replace: Delete old file if exists
            if ($thesis->file_path) {
                $oldPath = str_replace('storage/', '', $thesis->file_path);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }

            // Store new file
            $filePath = $request->file('pdf_file')->storeAs($folderPath, $fileName, 'public');
            $data['file_path'] = 'storage/' . $filePath;
        }

        $thesis->update($data);

        return redirect()->back()->with('success', 'Data skripsi berhasil diperbarui.');
    }

    public function printCertificate(Thesis $thesis)
    {
        if ($thesis->status !== 'approved') {
            abort(403, 'Sertifikat belum tersedia untuk dokumen ini.');
        }
        return view('admin.certificates.print', compact('thesis'));
    }

    public function types()
    {
        $types = \App\Models\ThesisType::paginate(10);
        return view('admin.master.types', compact('types'));
    }

    public function storeType(Request $request)
    {
        $request->validate(['name' => 'required|unique:thesis_types']);
        \App\Models\ThesisType::create($request->all());
        return redirect()->back()->with('success', 'Tipe skripsi berhasil ditambahkan.');
    }

    public function updateType(Request $request, \App\Models\ThesisType $type)
    {
        $request->validate(['name' => 'required|unique:thesis_types,name,' . $type->id]);
        $type->update($request->all());
        return redirect()->back()->with('success', 'Tipe skripsi berhasil diperbarui.');
    }

    public function destroyType(\App\Models\ThesisType $type)
    {
        $type->delete();
        return redirect()->back()->with('success', 'Tipe skripsi berhasil dihapus.');
    }

    public function storeManualThesis(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'student_name' => 'required',
            'title' => 'required',
            'department_id' => 'required|exists:departments,id',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        // Find or create user - Logic matched with digirepo original
        $user = User::where('nim', $request->nim)->first();
        if (!$user) {
            $user = User::create([
                'name' => $request->student_name,
                'nim' => $request->nim,
                'email' => $request->nim . '@archive.local',
                'password' => bcrypt(uniqid()),
                'role' => 'mahasiswa',
                'department_id' => $request->department_id,
                'is_verified' => true
            ]);
        }

        // Generate Path - Logic matched with digirepo original (Type/Year/Faculty/Dept/Nim.pdf)
        $dept = \App\Models\Department::with('faculty')->find($request->department_id);
        $facName = Str::slug($dept->faculty->name ?? 'Unknown_Faculty', '-');
        $deptName = Str::slug($dept->name, '-');
        $typePath = Str::slug($request->type, '-');
        $yearPath = $request->year;
        $nim = Str::slug($request->nim, '-');
        $nameSlug = Str::slug($request->student_name, '-');
        
        if (in_array($request->type, ['Skripsi', 'Thesis', 'Disertasi'])) {
            $folderPath = "{$yearPath}/{$typePath}/{$facName}/{$deptName}/{$nim}";
            $fileName = "{$nim}.pdf";
        } else {
            $folderPath = "{$typePath}/{$yearPath}/{$nim}";
            $fileName = "{$nim}_{$nameSlug}.pdf";
        }
        
        $filePath = $request->file('pdf_file')->storeAs($folderPath, $fileName, 'public');

        Thesis::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'type' => $request->type,
            'year' => $request->year,
            'abstract' => $request->abstract,
            'supervisor_name' => $request->supervisor_name,
            'file_path' => 'storage/' . $filePath, // Adjust to match public access
            'status' => 'approved',
            'certificate_number' => 'REG/' . date('Ymd') . '/' . strtoupper(Str::random(5))
        ]);

        return redirect()->back()->with('success', 'Data skripsi berhasil ditambahkan secara manual (Logika Sinkron dengan DigiRepo).');
    }

    public function exportTheses(Request $request)
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return redirect()->back()->with('error', 'Gagal: Library PhpSpreadsheet tidak ditemukan.');
        }

        $query = Thesis::with('user.department')->latest();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $theses = $query->get();

        if ($theses->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal: Tidak ada data skripsi pada rentang tanggal tersebut.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];

        // Headers
        $headers = ['NIM', 'PENULIS', 'JUDUL', 'TIPE', 'TAHUN', 'PRODI', 'KODE PRODI', 'PEMBIMBING', 'STATUS', 'ABSTRAK', 'KATA KUNCI', 'FILE PDF'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->applyFromArray($headerStyle);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        // Data
        $row = 2;
        foreach ($theses as $t) {
            // Set NIM as string to prevent scientific notation
            $sheet->setCellValueExplicit('A' . $row, $t->user->nim ?? '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $row, $t->user->name ?? 'User Terhapus');
            $sheet->setCellValue('C' . $row, $t->title);
            $sheet->setCellValue('D' . $row, $t->type);
            $sheet->setCellValue('E' . $row, $t->year);
            $sheet->setCellValue('F' . $row, $t->user->department->name ?? '-');
            $sheet->setCellValueExplicit('G' . $row, $t->user->department->code ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('H' . $row, $t->supervisor_name);
            $sheet->setCellValue('I' . $row, $t->status);
            $sheet->setCellValue('J' . $row, $t->abstract);
            $sheet->setCellValue('K' . $row, $t->keywords);
            $sheet->setCellValue('L' . $row, $t->file_path ? basename($t->file_path) : '');
            $row++;
        }

        if ($request->format === 'zip') {
            if (!class_exists('ZipArchive')) {
                return redirect()->back()->with('error', 'Gagal: PHP ZipArchive extension tidak aktif di server.');
            }

            $tempDir = storage_path('app/temp_export_' . time());
            if (!\Illuminate\Support\Facades\File::exists($tempDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($tempDir, 0755, true);
            }

            // Save Excel to temp folder
            $excelFilename = "Arsip_Skripsi_" . date('Ymd_His') . ".xlsx";
            $excelPath = $tempDir . '/' . $excelFilename;
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($excelPath);

            $zipFilename = "Export_Repositori_" . date('Ymd_His') . ".zip";
            $zipPath = storage_path('app/' . $zipFilename);

            $zip = new \ZipArchive;
            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($excelPath, $excelFilename);

                // Add PDFs
                foreach ($theses as $t) {
                    if ($t->file_path) {
                        $publicPath = str_replace('storage/', '', $t->file_path);
                        $absolutePath = storage_path('app/public/' . $publicPath);
                        if (\Illuminate\Support\Facades\File::exists($absolutePath)) {
                            // avoid name collision
                            $pdfName = basename($t->file_path);
                            $zip->addFile($absolutePath, $pdfName);
                        }
                    }
                }
                $zip->close();
            }

            // Cleanup temp dir
            \Illuminate\Support\Facades\File::deleteDirectory($tempDir);

            if (file_exists($zipPath)) {
                return response()->download($zipPath)->deleteFileAfterSend(true);
            } else {
                return redirect()->back()->with('error', 'Gagal membuat file ZIP.');
            }
        } else {
            $filename = "Arsip_Skripsi_" . date('Ymd_His') . ".xlsx";
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }
    }

    public function importTheses(Request $request)
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return redirect()->back()->with('error', 'Gagal: Library PhpSpreadsheet tidak ditemukan.');
        }
        
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv,zip|max:512000' // Max 500MB
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $isZip = $extension === 'zip';
        $tempDir = null;
        $excelPath = $file->getRealPath();

        if ($isZip) {
            if (!class_exists('ZipArchive')) {
                return redirect()->back()->with('error', 'Gagal: PHP ZipArchive extension tidak aktif di server.');
            }

            $tempDir = storage_path('app/temp_import_' . time());
            if (!\Illuminate\Support\Facades\File::exists($tempDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($tempDir, 0755, true);
            }

            $zip = new \ZipArchive;
            if ($zip->open($excelPath) === TRUE) {
                $zip->extractTo($tempDir);
                $zip->close();
            } else {
                \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
                return redirect()->back()->with('error', 'Gagal mengekstrak file ZIP.');
            }

            // Cari file Excel di folder hasil ekstrak (bisa di root atau subfolder pertama)
            $excelFiles = [];
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempDir));
            foreach ($iterator as $info) {
                if ($info->isFile() && in_array(strtolower($info->getExtension()), ['xls', 'xlsx', 'csv'])) {
                    $excelFiles[] = $info->getRealPath();
                }
            }

            if (empty($excelFiles)) {
                \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
                return redirect()->back()->with('error', 'Gagal: Tidak ada file Excel (.xlsx/.xls/.csv) di dalam file ZIP.');
            }
            $excelPath = $excelFiles[0]; // Ambil file excel pertama yang ditemukan
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelPath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $success = 0;
            $skipped = 0;
            $pdfsFound = 0;

            for ($i = 2; $i <= $highestRow; $i++) {
                $nim            = trim($sheet->getCell('A' . $i)->getValue());
                $studentName    = trim($sheet->getCell('B' . $i)->getValue());
                $title          = trim($sheet->getCell('C' . $i)->getValue());
                $type           = trim($sheet->getCell('D' . $i)->getValue());
                $year           = trim($sheet->getCell('E' . $i)->getValue());
                $deptCode       = trim($sheet->getCell('G' . $i)->getValue());
                $supervisor     = trim($sheet->getCell('H' . $i)->getValue());
                $status         = strtolower(trim($sheet->getCell('I' . $i)->getValue())) ?: 'approved';
                $abstract       = $sheet->getCell('J' . $i)->getValue();
                $keywords       = $sheet->getCell('K' . $i)->getValue();
                $pdfFileName    = trim($sheet->getCell('L' . $i)->getValue());

                if (empty($nim) || empty($title)) {
                    $skipped++;
                    continue;
                }

                // Check for duplicate thesis (Same user and title)
                $existingUser = User::where('nim', $nim)->first();
                if ($existingUser) {
                    $duplicate = Thesis::where('user_id', $existingUser->id)
                                      ->where('title', $title)
                                      ->exists();
                    if ($duplicate) {
                        $skipped++;
                        continue;
                    }
                }

                // Find/Create User
                $user = $existingUser;
                $dept = \App\Models\Department::with('faculty')->where('code', $deptCode)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $studentName ?: 'Mahasiswa ' . $nim,
                        'nim' => $nim,
                        'email' => strtolower($nim) . '@uinsi.ac.id',
                        'password' => bcrypt('password123'),
                        'role' => 'mahasiswa',
                        'department_id' => $dept->id ?? null,
                        'is_verified' => true
                    ]);
                }

                $savedFilePath = null;

                // Proses file PDF jika mode ZIP dan kolom L ada isinya
                if ($isZip && !empty($pdfFileName)) {
                    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempDir));
                    $pdfFile = null;
                    foreach ($iterator as $info) {
                        // Pencocokan nama file dengan mengabaikan case (huruf besar/kecil)
                        if ($info->isFile() && strtolower($info->getFilename()) === strtolower($pdfFileName)) {
                            $pdfFile = $info->getRealPath();
                            break;
                        }
                    }

                    if ($pdfFile) {
                        $facName = \Illuminate\Support\Str::slug($dept->faculty->name ?? 'Unknown_Faculty', '_');
                        $deptName = \Illuminate\Support\Str::slug($dept->name ?? 'Unknown_Dept', '_');
                        $typePath = \Illuminate\Support\Str::slug($type ?: 'Skripsi', '_');
                        $yearPath = $year ?: date('Y');
                        
                        $folderPath = "theses/{$typePath}/{$yearPath}/{$facName}/{$deptName}";
                        $finalFileName = \Illuminate\Support\Str::slug($nim, '_') . ".pdf";
                        
                        $destinationPath = storage_path('app/public/' . $folderPath);
                        if (!\Illuminate\Support\Facades\File::exists($destinationPath)) {
                            \Illuminate\Support\Facades\File::makeDirectory($destinationPath, 0755, true);
                        }

                        \Illuminate\Support\Facades\File::copy($pdfFile, $destinationPath . '/' . $finalFileName);
                        $savedFilePath = 'storage/' . $folderPath . '/' . $finalFileName;
                        $pdfsFound++;
                    }
                }

                Thesis::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'year' => $year ?: date('Y'),
                    'abstract' => $abstract,
                    'keywords' => $keywords,
                    'supervisor_name' => $supervisor,
                    'type' => $type ?: 'Skripsi',
                    'status' => $status,
                    'file_path' => $savedFilePath
                ]);
                
                $success++;
            }

            if ($isZip && $tempDir) {
                \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            }

            $msg = "$success data berhasil diimport, $skipped data dilewati (kosong atau duplikat).";
            if ($isZip) {
                $msg .= " Total PDF yang berhasil dipasangkan: $pdfsFound file.";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            if ($isZip && $tempDir) {
                \Illuminate\Support\Facades\File::deleteDirectory($tempDir);
            }
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function destroyThesis(Thesis $thesis)
    {
        $thesis->delete();
        return redirect()->back()->with('success', 'Skripsi telah berhasil dihapus secara permanen.');
    }

    public function uploadFile(Request $request, Thesis $thesis)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        // Generate Path - Logic matched with storeManualThesis
        $user = $thesis->user;
        $dept = $user->department;
        $facName = \Illuminate\Support\Str::slug($dept->faculty->name ?? 'Unknown_Faculty', '_');
        $deptName = \Illuminate\Support\Str::slug($dept->name ?? 'Unknown_Dept', '_');
        $typePath = \Illuminate\Support\Str::slug($thesis->type, '_');
        $yearPath = $thesis->year;
        
        $folderPath = "theses/{$typePath}/{$yearPath}/{$facName}/{$deptName}";
        $fileName = \Illuminate\Support\Str::slug($user->nim ?? 'Unknown_NIM', '_') . ".pdf";
        
        $filePath = $request->file('pdf_file')->storeAs($folderPath, $fileName, 'public');

        $thesis->update([
            'file_path' => 'storage/' . $filePath,
        ]);

        return redirect()->back()->with('success', 'File PDF berhasil diunggah untuk skripsi ini.');
    }

    public function updateCertificate(Request $request, Thesis $thesis)
    {
        $request->validate([
            'certificate_number' => 'required|string',
            'certificate_date' => 'required|date',
            'certificate_content' => 'required|string',
        ]);

        $thesis->update([
            'certificate_number' => $request->certificate_number,
            'certificate_date' => $request->certificate_date,
            'certificate_content' => $request->certificate_content,
        ]);

        if ($request->has('resend_email')) {
            $thesis->update(['delivery_status' => 'pending']);
            \App\Jobs\ResendCertificateJob::dispatch($thesis);
            return redirect()->back()->with('success', 'Data diperbarui dan email sedang dikirim ulang.');
        }

        return redirect()->back()->with('success', 'Data surat keterangan berhasil diperbarui.');
    }

    public function resendCertificate(Thesis $thesis)
    {
        if ($thesis->status !== 'approved') {
            return redirect()->back()->with('error', 'Gagal: Dokumen belum disetujui.');
        }

        // Reset status for UI feedback
        $thesis->update(['delivery_status' => 'pending']);

        // Dispatch Job (Kirim email & sertifikat)
        \App\Jobs\ResendCertificateJob::dispatch($thesis);

        return redirect()->back()->with('success', 'Email sertifikat sedang dikirim ulang di background.');
    }
    public function systemControl()
    {
        $maintenanceMode = app()->isDownForMaintenance();
        return view('admin.system.control', compact('maintenanceMode'));
    }

    public function runArtisanCommand(Request $request)
    {
        $request->validate(['command' => 'required|string']);
        
        $command = $request->command;
        $allowedCommands = [
            'optimize:clear' => 'Membersihkan seluruh cache sistem',
            'storage:link'   => 'Membuat tautan penyimpanan (Storage Link)',
            'view:clear'     => 'Membersihkan cache view',
            'config:clear'   => 'Membersihkan cache config',
            'route:clear'    => 'Membersihkan cache route',
            'cache:clear'    => 'Membersihkan cache aplikasi',
        ];

        if (!array_key_exists($command, $allowedCommands)) {
            return back()->with('error', 'Perintah tidak diizinkan demi keamanan.');
        }

        try {
            // Khusus untuk queue:work (tanpa --once), jalankan di background agar tidak hang
            if ($command === 'queue:work') {
                $phpPath = PHP_BINARY;
                $artisanPath = base_path('artisan');
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // Beri judul kosong "" di awal agar start tidak menganggap path PHP sebagai judul
                    pclose(popen("start \"QueueWorker\" /B \"$phpPath\" \"$artisanPath\" queue:work > nul 2>&1", "r"));
                } else {
                    exec("\"$phpPath\" \"$artisanPath\" queue:work > /dev/null 2>&1 &");
                }
                return back()->with('success', 'Worker antrean berhasil dijalankan di latar belakang.');
            }

            \Illuminate\Support\Facades\Artisan::call($command);
            $output = \Illuminate\Support\Facades\Artisan::output();
            return back()->with('success', "Berhasil: {$allowedCommands[$command]}. Output: " . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleMaintenance(Request $request)
    {
        try {
            if (app()->isDownForMaintenance()) {
                \Illuminate\Support\Facades\Artisan::call('up');
                return back()->with('success', 'Website berhasil diaktifkan kembali (UP Mode).');
            } else {
                \Illuminate\Support\Facades\Artisan::call('down', [
                    '--secret' => 'zenith_access',
                    '--render' => 'errors::503'
                ]);
                return back()->with('success', 'Website sekarang dalam Mode Perbaikan (Maintenance Mode). Anda bisa mengakses via: ' . url('/zenith_access'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status server: ' . $e->getMessage());
        }
    }
}
