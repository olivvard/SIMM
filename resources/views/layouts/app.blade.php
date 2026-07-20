<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.head-css')
</head>

<body>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('components.navbar')
        <div class="page-body-wrapper sidebar-icon">
            @include('components.sidebar')
            <div class="page-body">

                {{-- ============================================================ --}}
                {{-- TAMPERING ALERT BANNER                                        --}}
                {{-- Disembunyikan secara default.                                 --}}
                {{-- JS polling akan menampilkannya jika ada data yang dimanipulasi --}}
                {{-- Banner hanya muncul kembali jika ada log BARU yang belum      --}}
                {{-- pernah di-dismiss sebelumnya (disimpan di sessionStorage).    --}}
                {{-- ============================================================ --}}
                <div id="tamper-alert-banner"
                     style="display:none; position:sticky; top:0; z-index:9999;
                            background:#7f1d1d; border-bottom:3px solid #ef4444;">
                    <div style="padding:14px 24px; display:flex; align-items:flex-start; gap:14px;">
                        {{-- Icon --}}
                        <div style="flex-shrink:0; padding-top:2px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                 fill="#ef4444" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091
                                         1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982
                                         1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0
                                         1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0
                                         2 1 1 0 0 1 0-2z"/>
                            </svg>
                        </div>
                        {{-- Content --}}
                        <div style="flex:1;">
                            <p style="color:#fca5a5; font-weight:700; font-size:14px; margin:0 0 6px;">
                                ⚠️ DATA INTEGRITY VIOLATION DETECTED
                            </p>
                            <p style="color:#fecaca; font-size:13px; margin:0 0 10px;">
                                Maintenance log berikut terdeteksi telah <strong>dimodifikasi langsung di database</strong>
                                (bypass aplikasi / via phpMyAdmin). Digital signature tidak cocok.
                            </p>
                            <ul id="tamper-alert-list"
                                style="color:#fee2e2; font-size:13px; margin:0; padding-left:18px; line-height:2;">
                                {{-- Diisi oleh JavaScript --}}
                            </ul>
                            <div class="mt-2">
                                <a href="{{ route('activity-logs.index', ['status' => 'danger']) }}"
                                   style="color:#fbbf24; font-size:12px; font-weight:600;
                                          text-decoration:underline;">
                                    Lihat semua di Activity Log →
                                </a>
                            </div>
                        </div>
                        {{-- Dismiss Button --}}
                        <button id="tamper-dismiss-btn"
                                style="background:none; border:none; color:#fca5a5; font-size:22px;
                                       cursor:pointer; flex-shrink:0; line-height:1; padding:0;"
                                title="Dismiss — tidak akan muncul lagi untuk log ini">
                            &times;
                        </button>
                    </div>
                </div>

                <div class="container-fluid dashboard-default-sec">
                    @yield('content')
                </div>
            </div>
            @include('components.footer')
        </div>
    </div>
    @include('components.vendor')
    @stack('scripts')

    {{-- ================================================================== --}}
    {{-- INTEGRITY POLLING SCRIPT                                            --}}
    {{--                                                                     --}}
    {{-- Setiap 30 detik, fetch /integrity-check.                           --}}
    {{-- Jika ada tampering terdeteksi:                                      --}}
    {{--   - Tampilkan SEMUA log yang bermasalah                             --}}
    {{--   - Gunakan sessionStorage untuk ingat log yang sudah di-dismiss    --}}
    {{--   - Banner hanya muncul kembali untuk log yang BELUM di-dismiss     --}}
    {{-- ================================================================== --}}
    <script>
    (function () {
        const CHECK_INTERVAL  = 30000;          // 30 detik
        const ENDPOINT        = "{{ route('integrity.check') }}";
        const STORAGE_KEY     = 'simm_dismissed_tamper_ids'; // key di sessionStorage

        // Ambil array ID yang sudah pernah di-dismiss dari sessionStorage
        function getDismissedIds() {
            try {
                return JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]');
            } catch { return []; }
        }

        // Simpan array ID yang di-dismiss ke sessionStorage
        function saveDismissedIds(ids) {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
        }

        // Simpan referensi ke log yang sedang ditampilkan (untuk dismiss)
        let currentVisibleIds = [];

        // Render atau sembunyikan banner berdasarkan data dari polling
        function renderBanner(data) {
            const banner  = document.getElementById('tamper-alert-banner');
            const list    = document.getElementById('tamper-alert-list');

            if (!data || data.status !== 'tampered' || data.count === 0) {
                banner.style.display = 'none';
                return;
            }

            // Filter: hanya tampilkan log yang BELUM di-dismiss
            const dismissed = getDismissedIds();
            const newLogs   = data.logs.filter(log => !dismissed.includes(log.id));

            if (newLogs.length === 0) {
                // Semua log yang tampere sudah pernah di-dismiss → sembunyikan banner
                banner.style.display = 'none';
                return;
            }

            // Simpan ID yang sedang ditampilkan (untuk keperluan dismiss)
            currentVisibleIds = newLogs.map(l => l.id);

            // Render semua log yang bermasalah + before/after diff
            list.innerHTML = newLogs.map(log => {
                // Bangun tabel diff jika snapshot tersedia
                let diffHtml = '';
                if (log.has_snapshot && log.diff && log.diff.length > 0) {
                    const rows = log.diff.map(d =>
                        `<tr>
                            <td style="padding:2px 10px 2px 0; color:#fcd34d; font-weight:600; white-space:nowrap;">
                                ${d.field}
                            </td>
                            <td style="padding:2px 10px; color:#fca5a5; text-decoration:line-through; word-break:break-all;">
                                ${d.before}
                            </td>
                            <td style="padding:2px 0 2px 4px; color:#6ee7b7; word-break:break-all;">
                                → ${d.after}
                            </td>
                        </tr>`
                    ).join('');
                    diffHtml = `
                        <table style="margin:6px 0 0 0; font-size:12px; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="padding:0 10px 4px 0; color:#94a3b8; font-weight:500; text-align:left;">Field</th>
                                    <th style="padding:0 10px 4px 0; color:#94a3b8; font-weight:500; text-align:left;">Sebelum (Asli)</th>
                                    <th style="padding:0 0 4px 0; color:#94a3b8; font-weight:500; text-align:left;">Sesudah (Diubah)</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>`;
                } else if (!log.has_snapshot) {
                    diffHtml = `<p style="font-size:11px; color:#94a3b8; margin:4px 0 0;">
                        ⚠️ Snapshot tidak tersedia untuk log lama ini (dibuat sebelum fitur aktif).
                        Jalankan: <code style="background:#1e293b; padding:1px 4px; border-radius:3px;">php artisan maintenance:backfill-signatures</code>
                    </p>`;
                }

                return `<li style="margin-bottom:12px; list-style:disc;">
                    <span>
                        Log <strong>#${log.id}</strong> —
                        Motor: <strong>${log.motor_code}</strong> |
                        Tanggal Inspeksi: <strong>${log.inspection_date}</strong> |
                        Dientri oleh: ${log.admin_name}
                        &nbsp;<a href="${log.url}"
                                 style="color:#fbbf24; font-weight:600; text-decoration:underline;">
                            Lihat Detail →
                        </a>
                    </span>
                    ${diffHtml}
                </li>`;
            }).join('');

            banner.style.display = 'block';
        }

        // Tombol dismiss: simpan ID ke sessionStorage → banner tidak muncul lagi
        // untuk log yang sama, sampai tab/browser ditutup
        document.getElementById('tamper-dismiss-btn').addEventListener('click', function () {
            const dismissed = getDismissedIds();
            // Gabungkan ID yang sudah ada dengan yang baru di-dismiss
            const updated   = [...new Set([...dismissed, ...currentVisibleIds])];
            saveDismissedIds(updated);
            document.getElementById('tamper-alert-banner').style.display = 'none';
        });

        // Fungsi polling utama
        function runIntegrityCheck() {
            fetch(ENDPOINT, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept'           : 'application/json',
                }
            })
            .then(res => {
                if (!res.ok || res.redirected) return null;
                return res.json();
            })
            .then(data => {
                if (data) renderBanner(data);
            })
            .catch(() => { /* Abaikan error network */ });
        }

        // Jalankan sekali saat halaman pertama dibuka, lalu setiap 30 detik
        runIntegrityCheck();
        setInterval(runIntegrityCheck, CHECK_INTERVAL);
    })();
    </script>

</body>

</html>