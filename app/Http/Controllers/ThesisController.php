<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

class ThesisController extends Controller
{
    /**
     * Display the specified thesis details (Public).
     */
    public function show(Thesis $thesis)
    {
        $user = auth()->user();
        $isOwner = auth()->check() && auth()->id() === $thesis->user_id;
        $isAdmin = auth()->check() && ($user && $user->isAdmin());

        if ($thesis->status !== 'approved' && !$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        return view('theses.show', compact('thesis'));
    }

    /**
     * Display the PDF viewer (Protected).
     */
    public function read(Thesis $thesis)
    {
        $user = auth()->user();
        $isOwner = auth()->check() && auth()->id() === $thesis->user_id;
        $isAdmin = auth()->check() && ($user && $user->isAdmin());

        if ($thesis->status !== 'approved' && !$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Embargo Check
        if ($thesis->embargo_until && now()->lt($thesis->embargo_until) && !$isOwner && !$isAdmin) {
            return redirect()->route('theses.show', $thesis->id)->with('error', 'Maaf, dokumen ini sedang dalam masa embargo hingga ' . \Carbon\Carbon::parse($thesis->embargo_until)->format('d M Y'));
        }

        return view('theses.view', compact('thesis'));
    }

    /**
     * Stream the PDF for the viewer.
     */
    public function stream(Thesis $thesis)
    {
        $user = auth()->user();
        $isOwner = auth()->check() && auth()->id() === $thesis->user_id;
        $isAdmin = auth()->check() && ($user && $user->isAdmin());

        if ($thesis->status !== 'approved' && !$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Embargo Check (Double check for security)
        if ($thesis->embargo_until && now()->lt($thesis->embargo_until) && !$isOwner && !$isAdmin) {
            abort(403, 'Dokumen sedang dalam masa embargo.');
        }

        $cleanPath = $thesis->file_path;
        if (empty($cleanPath) && $thesis->files()->count() > 0) {
            $cleanPath = $thesis->files()->first()->file_path;
        }
        $cleanPath = $cleanPath ?? '';
        
        if (empty($cleanPath)) {
            return redirect()->route('theses.show', $thesis->id)->with('error', 'Maaf, file PDF fisik belum tersedia untuk dokumen ini.');
        }
        
        $prefixes = ['/storage/', 'storage/', '/public/', 'public/'];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($cleanPath, $prefix)) {
                $cleanPath = substr($cleanPath, strlen($prefix));
            }
        }
        $cleanPath = ltrim($cleanPath, '/');

        if (!Storage::disk('public')->exists($cleanPath)) {
            \Log::error("Thesis File Not Found on Disk: " . $cleanPath);
            return redirect()->route('theses.show', $thesis->id)->with('error', 'Maaf, file PDF fisik tidak ditemukan di server. Silakan hubungi administrator.');
        }

        $fullPath = Storage::disk('public')->path($cleanPath);
        
        // Penting bagi PDF.js untuk mendapatkan Content-Length
        $fileSize = filesize($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="' . basename($cleanPath) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Download the thesis file.
     */
    public function download(Thesis $thesis)
    {
        $user = auth()->user();
        $isOwner = auth()->check() && auth()->id() === $thesis->user_id;
        $isAdmin = auth()->check() && $user->isAdmin();

        // Guest cannot download
        if (auth()->check() && $user->isGuest()) {
            abort(403, 'Guest tidak diperbolehkan mengunduh dokumen.');
        }

        if ($thesis->status !== 'approved' && !$isOwner && !$isAdmin) {
            abort(403);
        }

        // Embargo Check
        if ($thesis->embargo_until && now()->lt($thesis->embargo_until) && !$isOwner && !$isAdmin) {
            abort(403, 'Gagal: Dokumen dalam masa embargo.');
        }

        // Record Statistics
        \App\Models\Download::create([
            'thesis_id' => $thesis->id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Clean Path logic for download
        $cleanPath = $thesis->file_path;
        if (empty($cleanPath) && $thesis->files()->count() > 0) {
            $cleanPath = $thesis->files()->first()->file_path;
        }
        $cleanPath = $cleanPath ?? '';

        if (empty($cleanPath)) {
            return redirect()->route('theses.show', $thesis->id)->with('error', 'Maaf, file PDF fisik belum tersedia untuk dokumen ini.');
        }

        $prefixes = ['/storage/', 'storage/', '/public/', 'public/'];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($cleanPath, $prefix)) {
                $cleanPath = substr($cleanPath, strlen($prefix));
            }
        }
        $cleanPath = ltrim($cleanPath, '/');

        return Storage::disk('public')->download($cleanPath, "{$thesis->title}.pdf");
    }

    /**
     * Download a specific file from the thesis files collection.
     */
    /**
     * Stream a specific file from the thesis files collection (for Admin Review).
     */
    public function streamFile(\App\Models\ThesisFile $file)
    {
        $thesis = $file->thesis;
        $user = auth()->user();
        $isOwner = auth()->check() && auth()->id() === $thesis->user_id;
        $isAdmin = auth()->check() && $user && $user->isAdmin();

        if ($thesis->status !== 'approved' && !$isOwner && !$isAdmin) {
            abort(403);
        }

        $fullPath = Storage::disk('public')->path($file->file_path);
        
        if (!file_exists($fullPath)) {
            abort(404, 'File fisik tidak ditemukan.');
        }

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($file->file_path) . '"',
        ]);
    }

    public function downloadFile(\App\Models\ThesisFile $file)
    {
        $thesis = $file->thesis;
        $user = auth()->user();
        $isOwner = auth()->check() && auth()->id() === $thesis->user_id;
        $isAdmin = auth()->check() && $user && $user->isAdmin();

        if (auth()->check() && $user->isGuest()) {
            abort(403, 'Guest tidak diperbolehkan mengunduh dokumen.');
        }

        if ($thesis->status !== 'approved' && !$isOwner && !$isAdmin) {
            abort(403);
        }

        // Embargo Check
        if ($thesis->embargo_until && now()->lt($thesis->embargo_until) && !$isOwner && !$isAdmin) {
            abort(403, 'Gagal: Dokumen dalam masa embargo.');
        }

        // Record Statistics
        \App\Models\Download::create([
            'thesis_id' => $thesis->id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Storage::disk('public')->download($file->file_path, "{$file->label} - {$thesis->title}.pdf");
    }

    /**
     * View/Print the thesis certificate.
     */
    public function certificate(Thesis $thesis)
    {
        if ($thesis->status !== 'approved') {
            abort(403, 'Sertifikat belum tersedia.');
        }

        // Allow if current user is the owner, OR if current user is an admin
        $isOwner = auth()->check() && auth()->id() === $thesis->user_id;
        $isAdmin = auth()->check() && auth()->user()->role === 'admin';

        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        return view('admin.certificates.print', compact('thesis'));
    }

    /**
     * Show the form for creating a new thesis.
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isMahasiswa() && !$user->isDosen()) {
            return redirect()->route('dashboard')->with('error', 'Halaman unggah hanya untuk Mahasiswa atau Dosen.');
        }
        
        $existingThesis = $user->theses()->orderBy('created_at', 'desc')->first();
        
        // Mahasiswa limit: 1 upload only
        if ($user->isMahasiswa() && $existingThesis && ($existingThesis->status === 'approved' || $existingThesis->status === 'pending')) {
             return redirect()->route('dashboard')->with('info', 'Setiap mahasiswa hanya diperbolehkan mengunggah satu kali.');
        }

        return view('theses.create', compact('existingThesis'));
    }

    /**
     * Store a newly created thesis in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isMahasiswa() && !$user->isDosen()) {
            abort(403);
        }

        $isAcademic = in_array($request->type, ['Skripsi', 'Thesis', 'Disertasi']);
        $existingThesis = null;

        // Check limits for Academic types
        if ($user->isMahasiswa() || ($user->isDosen() && $isAcademic)) {
            $existingThesis = $user->theses()->whereIn('type', ['Skripsi', 'Thesis', 'Disertasi'])->first();
            if ($existingThesis && ($existingThesis->status === 'approved' || $existingThesis->status === 'pending')) {
                return redirect()->route('dashboard')->with('error', 'Gagal: Anda sudah memiliki data karya ilmiah dalam proses atau sudah disetujui.');
            }
        }

        $isAcademic = in_array($request->type, ['Skripsi', 'Thesis', 'Disertasi']);

        $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:Skripsi,Thesis,Disertasi,Jurnal,Buku,Artikel,Lainnya',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'abstract' => 'required|string',
            'keywords' => 'required|string',
            'supervisor_name' => $isAcademic ? 'required|string' : 'nullable|string',
            'files' => 'required|array|min:1',
            'files.*' => 'required|mimes:pdf|max:20480',
            'file_labels' => 'required|array|min:1',
            // Extended Metadata Validation
            'journal_name' => 'nullable|string',
            'volume' => 'nullable|string',
            'issue' => 'nullable|string',
            'pages' => 'nullable|string',
            'issn' => 'nullable|string',
            'doi' => 'nullable|string',
            'isbn' => 'nullable|string',
            'publisher' => 'nullable|string',
            'edition' => 'nullable|string',
        ]);

        $filesData = [];
        $year = $request->year;
        $typeSlug = \Illuminate\Support\Str::slug($request->type);
        $facultySlug = \Illuminate\Support\Str::slug($user->department->faculty->name ?? 'Umum');
        $deptSlug = \Illuminate\Support\Str::slug($user->department->name ?? 'Umum');
        $nim = preg_replace('/[^A-Za-z0-9\-]/', '', $user->nim ?: 'Unknown');
        $nameSlug = \Illuminate\Support\Str::slug($user->name);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $label = $request->file_labels[$index] ?? 'Document ' . ($index + 1);
                $labelSlug = \Illuminate\Support\Str::slug($label);
                
                // Temp save
                $tempPath = $file->store('temp-uploads', 'local');
                
                // Final destination logic
                if (in_array($request->type, ['Skripsi', 'Thesis', 'Disertasi'])) {
                    $finalPath = "{$year}/{$typeSlug}/{$facultySlug}/{$deptSlug}/{$nim}/{$nim}_{$labelSlug}.pdf";
                } else {
                    $finalPath = "{$typeSlug}/{$year}/{$nim}/{$nim}_{$nameSlug}_{$labelSlug}.pdf";
                }
                
                $filesData[] = [
                    'temp' => $tempPath,
                    'final' => $finalPath,
                    'label' => $label
                ];
            }
        }

        $data = [
            'title' => $request->title,
            'type' => $request->type,
            'year' => $request->year,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'supervisor_name' => $request->supervisor_name,
            'journal_name' => $request->journal_name,
            'volume' => $request->volume,
            'issue' => $request->issue,
            'pages' => $request->pages,
            'issn' => $request->issn,
            'doi' => $request->doi,
            'isbn' => $request->isbn,
            'publisher' => $request->publisher,
            'edition' => $request->edition,
            'status' => 'pending', 
        ];

        if ($existingThesis) {
            $existingThesis->update($data);
            $thesis = $existingThesis;
        } else {
            $data['user_id'] = $user->id;
            $thesis = \App\Models\Thesis::create($data);
        }

        // 3. Dispatch Job with multi-files data
        \App\Jobs\ProcessThesisSubmission::dispatch(
            $thesis, 
            $filesData, 
            $existingThesis ? true : false
        );

        $message = $existingThesis 
            ? 'Revisi skripsi telah masuk antrean pemrosesan.' 
            : 'Skripsi telah berhasil diunggah dan sedang diproses dalam antrean.';

        if ($request->ajax() || $request->wantsJson()) {
            session()->flash('success', $message);
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
}
