@extends('layouts.viewer')

@section('title', 'Baca: ' . $thesis->title)

@section('styles')
<style>
    :root {
        --header-height: 72px;
        --accent-blue: #3b82f6;
    }

    body {
        background-color: #0f172a;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .viewer-header {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: var(--header-height) !important;
        background: rgba(15, 23, 42, 0.85) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0 30px !important;
        z-index: 9999 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        color: white;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 18px;
        flex: 1;
        min-width: 0;
    }

    .btn-back-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-back-circle:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(-3px);
    }

    .title-wrapper {
        min-width: 0;
    }
    .thesis-title-text {
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        letter-spacing: -0.01em;
        color: #f8fafc;
    }
    .thesis-meta-text {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 2px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .header-center {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 4px 8px;
        border-radius: 14px;
        margin: 0 20px;
    }

    .nav-btn {
        background: transparent;
        border: none;
        color: white;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        opacity: 0.6;
    }
    .nav-btn:hover:not(:disabled) {
        background: rgba(255, 255, 255, 0.1);
        opacity: 1;
        color: var(--accent-blue);
    }
    .nav-btn:disabled { opacity: 0.1; cursor: not-allowed; }

    .page-input-container {
        display: flex;
        align-items: center;
        padding: 0 12px;
        font-family: 'Outfit', sans-serif;
    }
    .page-num-input {
        background: transparent;
        border: none;
        color: var(--accent-blue);
        font-weight: 800;
        width: 45px;
        text-align: center;
        font-size: 1rem;
        outline: none;
    }
    .page-total-label {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.85rem;
        margin-left: 6px;
        font-weight: 600;
    }

    .header-right {
        flex: 0 0 auto;
    }

    .btn-download-premium {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white !important;
        padding: 10px 24px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        transition: all 0.3s ease;
    }
    .btn-download-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.4);
        filter: brightness(1.1);
    }

    .viewer-content {
        margin-top: calc(var(--header-height) + 40px);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-bottom: 120px;
    }

    .pdf-page-wrapper {
        position: relative;
        margin-bottom: 40px;
        background: white;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
        width: 850px;
        max-width: 95vw;
        border-radius: 4px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    .pdf-page-wrapper canvas { width: 100%; height: auto; display: block; }

    #loading-screen {
        position: fixed; inset: 0; background: #0f172a; z-index: 10000;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        color: white; transition: opacity 0.5s;
    }
    .progress-bar-bg { width: 240px; height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px; margin-top: 25px; overflow: hidden; }
    .progress-bar-fill { width: 0%; height: 100%; background: var(--accent-blue); transition: width 0.3s; }
</style>
@endsection

@section('content')
<div id="loading-screen">
    <div class="spinner-border text-primary" style="width: 2.5rem; height: 2.5rem;"></div>
    <div class="mt-4 fw-600 opacity-75">Membuka Koleksi Digital...</div>
    <div class="progress-bar-bg"><div class="progress-bar-fill" id="init-progress"></div></div>
</div>

<header class="viewer-header">
    <div class="header-left">
        @php $backUrl = auth()->user()->role === 'admin' ? route('admin.theses.index') : route('dashboard'); @endphp
        <a href="{{ $backUrl }}" data-turbo="false" class="btn-back-circle" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="title-wrapper">
            <h1 class="thesis-title-text">{{ $thesis->title }}</h1>
            <div class="thesis-meta-text">{{ $thesis->user->name }} • {{ $thesis->year }}</div>
        </div>
    </div>

    <div class="header-center">
        <button class="nav-btn" id="prev-page" title="Halaman Sebelumnya"><i class="fas fa-chevron-left"></i></button>
        <div class="page-input-container">
            <input type="number" id="page-input" class="page-num-input" value="1" min="1">
            <span class="page-total-label">/ <span id="total-pages">...</span></span>
        </div>
        <button class="nav-btn" id="next-page" title="Halaman Berikutnya"><i class="fas fa-chevron-right"></i></button>
    </div>

    <div class="header-right">
        <a href="{{ route('theses.download', $thesis->id) }}" data-turbo="false" class="btn-download-premium">
            <i class="fas fa-cloud-download-alt"></i> <span>Unduh PDF</span>
        </a>
    </div>
</header>

<div class="viewer-content" id="viewer-content">
    <!-- PDF pages will be injected here -->
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script>
    const url = "{{ route('theses.stream', $thesis->id, false) }}";
    let pdfDoc = null;
    let isScrollingManual = false;
    let pdfjsLib = null;

    const container = document.getElementById('viewer-content');
    const totalPagesEl = document.getElementById('total-pages');
    const pageInput = document.getElementById('page-input');
    const progressBar = document.getElementById('init-progress');
    const renderedPages = new Set();

    // 1. Navigation Actions
    function jumpToPage(num) {
        if (!pdfDoc || num < 1 || num > pdfDoc.numPages) return;
        isScrollingManual = true;
        const target = container.querySelector(`.pdf-page-wrapper[data-page-number="${num}"]`);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            pageInput.value = num;
            // Unlock scroll sync after smooth scroll finishes
            setTimeout(() => { isScrollingManual = false; }, 1000);
        }
    }

    document.getElementById('prev-page').onclick = () => jumpToPage(parseInt(pageInput.value) - 1);
    document.getElementById('next-page').onclick = () => jumpToPage(parseInt(pageInput.value) + 1);
    
    pageInput.onchange = () => jumpToPage(parseInt(pageInput.value));
    pageInput.onkeydown = (e) => { if(e.key === 'Enter') jumpToPage(parseInt(pageInput.value)); };

    // 2. Rendering Core
    async function renderPage(num, target) {
        if (renderedPages.has(num)) return;
        renderedPages.add(num);

        try {
            const page = await pdfDoc.getPage(num);
            const viewport = page.getViewport({ scale: 1.5 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            target.innerHTML = ''; // Clear spinner
            target.appendChild(canvas);

            await page.render({ canvasContext: context, viewport: viewport }).promise;
        } catch (err) { console.error(err); }
    }

    // 3. Scroll Watcher with Threshold
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const num = parseInt(entry.target.dataset.pageNumber);
                if (!isScrollingManual) pageInput.value = num;
                renderPage(num, entry.target);
            }
        });
    }, { threshold: 0.35 });

    // 4. Lifecycle & Initialization
    async function init() {
        const loadingTask = pdfjsLib.getDocument(url);
        loadingTask.onProgress = (p) => {
            if (p.total > 0) progressBar.style.width = (p.loaded / p.total * 100) + '%';
        };

        try {
            pdfDoc = await loadingTask.promise;
            totalPagesEl.innerText = pdfDoc.numPages;
            pageInput.max = pdfDoc.numPages;
            
            const loader = document.getElementById('loading-screen');
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 500);

            const fragment = document.createDocumentFragment();
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const wrapper = document.createElement('div');
                wrapper.className = 'pdf-page-wrapper';
                wrapper.dataset.pageNumber = i;
                wrapper.innerHTML = '<i class="fas fa-circle-notch fa-spin opacity-20" style="font-size: 2.5rem; color: #3b82f6;"></i>';
                fragment.appendChild(wrapper);
            }
            container.appendChild(fragment);
            container.querySelectorAll('.pdf-page-wrapper').forEach(w => observer.observe(w));
        } catch (err) { console.error(err); }
    }

    function start() {
        pdfjsLib = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
        if (pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
            init();
        } else { setTimeout(start, 500); }
    }

    start();
    document.addEventListener('contextmenu', e => e.preventDefault());
</script>
@endsection
