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

        $cleanPath = $thesis->file_path;
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

        return Storage::disk('public')->download($thesis->file_path, "{$thesis->title}.pdf");
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

        $isThesisType = in_array($request->type, ['Skripsi', 'Thesis', 'Disertasi']);

        // Check limits
        if ($user->isMahasiswa() || ($user->isDosen() && $isThesisType)) {
            $existingThesis = $user->theses()->whereIn('type', ['Skripsi', 'Thesis', 'Disertasi'])->first();
            if ($existingThesis && ($existingThesis->status === 'approved' || $existingThesis->status === 'pending')) {
                return redirect()->route('dashboard')->with('error', 'Gagal: Anda sudah memiliki data karya ilmiah dalam proses atau sudah disetujui.');
            }
        }

        $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:Skripsi,Thesis,Disertasi,Jurnal,Buku,Artikel,Lainnya',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'abstract' => 'required|string',
            'keywords' => 'required|string',
            'supervisor_name' => 'required|string',
            'file' => 'required|mimes:pdf|max:20480', // 20MB
        ]);

        $file = $request->file('file');
        
        // 1. Save to temporary location (Persistent for queue worker)
        $tempPath = $file->store('temp-uploads', 'local');

        // 2. Prepare final path logic
        $year = $request->year;
        $typeSlug = \Illuminate\Support\Str::slug($request->type);
        $facultySlug = \Illuminate\Support\Str::slug($user->department->faculty->name ?? 'Umum');
        $deptSlug = \Illuminate\Support\Str::slug($user->department->name ?? 'Umum');
        $nim = preg_replace('/[^A-Za-z0-9\-]/', '', $user->nim ?: 'Unknown');
        $finalPath = "{$year}/{$typeSlug}/{$facultySlug}/{$deptSlug}/{$nim}.pdf";

        $data = [
            'title' => $request->title,
            'type' => $request->type,
            'year' => $request->year,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'supervisor_name' => $request->supervisor_name,
            'file_path' => null, // Reset path while processing in queue
            'status' => 'pending', 
        ];

        if ($existingThesis) {
            $existingThesis->update($data);
            $thesis = $existingThesis;
        } else {
            $data['user_id'] = $user->id;
            $thesis = \App\Models\Thesis::create($data);
        }

        // 3. Dispatch Job to handle file move and notifications
        \App\Jobs\ProcessThesisSubmission::dispatch(
            $thesis, 
            $tempPath, 
            $finalPath, 
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
