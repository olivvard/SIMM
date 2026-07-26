@extends('layouts.app')
@section('title', 'Backup & Recovery')
@section('page-title', 'Backup & Recovery')

@section('content')
@php
    use App\Http\Controllers\BackupController;
@endphp

<div class="mt-3">

{{-- ── Flash Alerts ───────────────────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3" role="alert"
         style="border-left: 5px solid #10b981; border-radius: 12px;">
        <i class="fa fa-check-circle fa-lg text-success flex-shrink-0"></i>
        <div>{!! session('success') !!}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3" role="alert"
         style="border-left: 5px solid #ef4444; border-radius: 12px;">
        <i class="fa fa-times-circle fa-lg text-danger flex-shrink-0"></i>
        <div>{!! session('error') !!}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Warning Banner ──────────────────────────────────────────────────────── --}}
<div class="alert d-flex align-items-start gap-3 mb-4"
     style="background: #fefce8; border: 1px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 12px;">
    <i class="fa fa-exclamation-triangle fa-lg mt-1 flex-shrink-0" style="color: #d97706;"></i>
    <div>
        <p class="mb-0 fw-semibold" style="color: #92400e; font-size: 13px;">⚠️ Penting — Baca Sebelum Melanjutkan</p>
        <p class="mb-0 mt-1" style="color: #92400e; font-size: 12px;">
            Operasi restore akan <strong>menimpa seluruh database</strong> dengan data dari file backup.
            Tindakan ini <strong>tidak dapat dibatalkan</strong>. Pastikan Anda sudah memiliki backup terbaru sebelum melakukan restore.
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ROW 1: Create Backup Cards                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Database Backup --}}
    <div class="col-xl-6">
        <div class="card h-100" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%); box-shadow: 0 8px 30px rgba(29,78,216,0.25);">
            <div class="card-body" style="padding: 28px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa fa-database" style="font-size: 1.6rem; color: #fff;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: #fff;">Database Backup</h5>
                        <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 12px;">Export semua tabel sebagai file .sql</p>
                    </div>
                </div>
                <ul style="color: rgba(255,255,255,0.8); font-size: 12px; padding-left: 18px; margin-bottom: 20px;">
                    <li>Menyimpan seluruh struktur & data database</li>
                    <li>Format SQL — dapat di-restore ke MySQL/MariaDB</li>
                    <li>Disimpan di <code style="background:rgba(0,0,0,0.3); padding: 1px 5px; border-radius: 4px;">storage/app/backups/</code></li>
                </ul>
                <form method="POST" action="{{ route('backup.database') }}"
                      onsubmit="return confirm('Buat backup database sekarang?')">
                    @csrf
                    <button type="submit" class="btn w-100 fw-semibold"
                            style="background: rgba(255,255,255,0.2); color:#fff; border: 1px solid rgba(255,255,255,0.3); border-radius: 10px; padding: 10px; backdrop-filter: blur(4px);">
                        <i class="fa fa-cloud-download me-2"></i> Buat Database Backup Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- File Storage Backup --}}
    <div class="col-xl-6">
        <div class="card h-100" style="border-radius: 16px; border: none; background: linear-gradient(135deg, #064e3b 0%, #059669 100%); box-shadow: 0 8px 30px rgba(5,150,105,0.25);">
            <div class="card-body" style="padding: 28px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa fa-folder-open" style="font-size: 1.6rem; color: #fff;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: #fff;">File Storage Backup</h5>
                        <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 12px;">Zip semua file di storage/app/public</p>
                    </div>
                </div>
                <ul style="color: rgba(255,255,255,0.8); font-size: 12px; padding-left: 18px; margin-bottom: 20px;">
                    <li>Menyimpan foto profil & lampiran maintenance</li>
                    <li>Format ZIP — dapat diekstrak manual</li>
                    <li>Disimpan di <code style="background:rgba(0,0,0,0.3); padding: 1px 5px; border-radius: 4px;">storage/app/backups/</code></li>
                </ul>
                <form method="POST" action="{{ route('backup.files') }}"
                      onsubmit="return confirm('Buat backup file storage sekarang?')">
                    @csrf
                    <button type="submit" class="btn w-100 fw-semibold"
                            style="background: rgba(255,255,255,0.2); color:#fff; border: 1px solid rgba(255,255,255,0.3); border-radius: 10px; padding: 10px; backdrop-filter: blur(4px);">
                        <i class="fa fa-file-zip-o me-2"></i> Buat File Storage Backup Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ROW 2: Restore + Upload                                                     --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-xl-12">
        <div class="card" style="border-radius: 16px; border: 2px dashed #fca5a5; background: #fff5f5;">
            <div class="card-body" style="padding: 28px;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: #fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fa fa-upload" style="font-size: 1.3rem; color: #ef4444;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="color: #7f1d1d;">Restore Database dari File</h6>
                        <p class="mb-0 text-muted" style="font-size: 12px;">Upload file <strong>.sql</strong> hasil backup sebelumnya untuk restore database</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('backup.restore') }}"
                      enctype="multipart/form-data"
                      onsubmit="return confirmRestore()">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small" style="color: #7f1d1d;">
                                Pilih File Backup (.sql)
                            </label>
                            <input type="file" name="sql_file" id="sql_file"
                                   class="form-control @error('sql_file') is-invalid @enderror"
                                   accept=".sql,.txt"
                                   style="border-radius: 10px; border-color: #fca5a5;">
                            @error('sql_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-danger w-100"
                                    style="border-radius: 10px; font-weight: 600; padding: 10px;">
                                <i class="fa fa-refresh me-2"></i> Restore Database
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
{{-- ROW 3: Backup Files List                                                    --}}
{{-- ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="card" style="border-radius: 16px; border: none;">
    <div class="card-header d-flex align-items-center justify-content-between"
         style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
        <div>
            <h6 class="mb-0 fw-bold" style="font-size: 14px;">
                <i class="fa fa-list me-2 text-primary"></i>
                Daftar Backup Tersimpan
            </h6>
            <p class="text-muted mb-0" style="font-size: 12px; margin-top: 2px;">
                {{ $backups->count() }} file backup tersimpan di server
            </p>
        </div>
        <span class="badge" style="background: #eff6ff; color: #3b82f6; border-radius: 8px; font-size: 11px; padding: 6px 12px;">
            {{ $backups->count() }} file
        </span>
    </div>
    <div class="card-body p-0">
        @if($backups->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fa fa-inbox" style="font-size: 3rem; opacity: 0.15; display: block; margin-bottom: 12px;"></i>
                <p class="fw-semibold mb-1">Belum ada backup tersimpan</p>
                <small>Buat backup database atau file storage menggunakan tombol di atas.</small>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size: 12px; padding: 12px 20px;">#</th>
                            <th style="font-size: 12px;">Nama File</th>
                            <th style="font-size: 12px;">Tipe</th>
                            <th style="font-size: 12px;">Ukuran</th>
                            <th style="font-size: 12px;">Dibuat</th>
                            <th style="font-size: 12px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $i => $backup)
                            <tr>
                                <td style="padding: 14px 20px; color: #94a3b8; font-size: 12px; font-weight: 600;">
                                    {{ $i + 1 }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; display:flex; align-items:center; justify-content:center;
                                                    background: {{ $backup['type'] === 'database' ? '#eff6ff' : '#f0fdf4' }};">
                                            <i class="fa {{ $backup['type'] === 'database' ? 'fa-database' : 'fa-file-archive-o' }}"
                                               style="font-size: 1rem; color: {{ $backup['type'] === 'database' ? '#3b82f6' : '#22c55e' }};"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size: 13px; font-family: monospace;">{{ $backup['name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($backup['type'] === 'database')
                                        <span class="badge" style="background: #eff6ff; color: #3b82f6; border-radius: 6px; font-size: 11px;">
                                            <i class="fa fa-database me-1"></i> Database
                                        </span>
                                    @else
                                        <span class="badge" style="background: #f0fdf4; color: #16a34a; border-radius: 6px; font-size: 11px;">
                                            <i class="fa fa-file-archive-o me-1"></i> File Storage
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 12px; font-weight: 600; color: #475569;">
                                        {{ BackupController::formatBytes($backup['size']) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 12px; font-weight: 600;">{{ $backup['modified']->format('d M Y, H:i') }}</div>
                                    <small class="text-muted">{{ $backup['modified']->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Download --}}
                                        <a href="{{ route('backup.download', $backup['name']) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           style="border-radius: 8px; font-size: 12px; padding: 5px 12px;"
                                           title="Download">
                                            <i class="fa fa-download"></i>
                                        </a>

                                        {{-- Restore (only .sql) --}}
                                        @if($backup['type'] === 'database')
                                        <form method="POST"
                                              action="{{ route('backup.restore.storage', $backup['name']) }}"
                                              onsubmit="return confirmRestore('{{ $backup['name'] }}')">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-warning"
                                                    style="border-radius: 8px; font-size: 12px; padding: 5px 12px;"
                                                    title="Restore database ini">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('backup.delete', $backup['name']) }}"
                                              onsubmit="return confirm('Hapus backup {{ $backup['name'] }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    style="border-radius: 8px; font-size: 12px; padding: 5px 12px;"
                                                    title="Hapus backup">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
function confirmRestore(filename) {
    const name = filename ? `"${filename}"` : 'yang dipilih';
    return confirm(
        `⚠️ PERINGATAN!\n\n` +
        `Anda akan me-restore database dari backup ${name}.\n\n` +
        `Seluruh data saat ini akan DITIMPA dan TIDAK DAPAT DIKEMBALIKAN.\n\n` +
        `Apakah Anda yakin ingin melanjutkan?`
    );
}
</script>
@endpush
