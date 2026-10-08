@php
    $currentUser = auth()->user();
    $currentRole = $currentUser->role ?? 'guest';
@endphp

<!-- AI Command Center / Command Palette Modal -->
<div class="modal fade" id="aiAgentModal" tabindex="-1" aria-labelledby="aiCommandPaletteLabel" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog spp-command-palette-dialog modal-dialog-centered">
        <div class="modal-content spp-command-palette-content border-0">
            
            <!-- Command Palette Topbar: ✦ SPP Assistant × -->
            <div class="spp-command-palette-topbar d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="topbar-sparkle">✦</span>
                    <span class="topbar-title">SPP Assistant</span>
                </div>
                <button type="button" class="btn-close text-muted" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
            </div>

            <!-- Command Palette Input Bar -->
            <div class="spp-command-input-container">
                <label for="commandPaletteInput" class="form-label text-muted small mb-2 fw-medium">Cari data atau tulis perintah...</label>
                <div class="spp-command-input-wrapper">
                    <input type="text" id="commandPaletteInput" class="spp-command-palette-input"
                           placeholder="siapa yang belum bayar bulan ini?"
                           autocomplete="off" maxlength="500">
                    <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                        <button type="button" id="commandPaletteClearBtn" class="btn btn-sm btn-link text-muted p-0 d-none text-decoration-none" title="Bersihkan">
                            <i class="mdi mdi-close fs-6"></i>
                        </button>
                        <kbd class="command-kbd-badge" style="font-size: 0.75rem; background: #ffffff; border: 1px solid var(--border); color: var(--navy-primary);" title="Jalankan Perintah">↵</kbd>
                    </div>
                </div>
            </div>

            <!-- Command Palette Body -->
            <div class="spp-command-palette-body" id="commandPaletteBody">
                
                <!-- 1. Initial State: Quick Suggestions -->
                <div id="commandInitialState" class="p-3">
                    <div class="text-muted fw-semibold mb-2 px-1" style="font-size: 0.8rem; letter-spacing: 0.3px;">Coba:</div>

                    <div class="d-flex flex-column gap-1" id="commandSuggestionsList">
                        @if ($currentRole === 'admin')
                            <div class="command-item" data-prompt="Siswa yang belum bayar">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Siswa yang belum bayar</span>
                                </div>
                                <span class="badge bg-light text-muted border">Tunggakan</span>
                            </div>
                            <div class="command-item" data-prompt="Total pembayaran bulan ini">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Total pembayaran bulan ini</span>
                                </div>
                                <span class="badge bg-light text-muted border">Statistik</span>
                            </div>
                            <div class="command-item" data-prompt="Tunggakan kelas XII">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Tunggakan kelas XII</span>
                                </div>
                                <span class="badge bg-light text-muted border">Kelas</span>
                            </div>
                            <div class="command-item" data-prompt="Cari siswa berdasarkan NIS">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Cari siswa berdasarkan NIS</span>
                                </div>
                                <span class="badge bg-light text-muted border">Pencarian</span>
                            </div>

                        @elseif ($currentRole === 'petugas')
                            <div class="command-item" data-prompt="Siswa yang belum bayar">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Siswa yang belum bayar</span>
                                </div>
                                <span class="badge bg-light text-muted border">Tunggakan</span>
                            </div>
                            <div class="command-item" data-prompt="Total pembayaran hari ini">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Total pembayaran hari ini</span>
                                </div>
                                <span class="badge bg-light text-muted border">Statistik</span>
                            </div>
                            <div class="command-item" data-prompt="Pembayaran yang butuh verifikasi">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Pembayaran yang butuh verifikasi</span>
                                </div>
                                <span class="badge bg-light text-muted border">Verifikasi</span>
                            </div>
                            <div class="command-item" data-prompt="Cari siswa berdasarkan NIS">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Cari siswa berdasarkan NIS</span>
                                </div>
                                <span class="badge bg-light text-muted border">Pencarian</span>
                            </div>

                        @elseif ($currentRole === 'siswa')
                            <div class="command-item" data-prompt="Berapa tunggakan saya?">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Berapa tunggakan saya?</span>
                                </div>
                                <span class="badge bg-light text-muted border">Tagihan</span>
                            </div>
                            <div class="command-item" data-prompt="Bagaimana status pembayaran saya?">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Status pembayaran saya</span>
                                </div>
                                <span class="badge bg-light text-muted border">Status</span>
                            </div>
                            <div class="command-item" data-prompt="buka riwayat pembayaran">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Riwayat pembayaran</span>
                                </div>
                                <span class="badge bg-light text-muted border">Riwayat</span>
                            </div>
                            <div class="command-item" data-prompt="bayar spp sekarang">
                                <div class="d-flex align-items-center">
                                    <span class="command-bullet">•</span>
                                    <span>Bayar SPP sekarang</span>
                                </div>
                                <span class="badge bg-light text-muted border">Pembayaran</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Loading State: Minimalist & Operational -->
                <div id="commandLoadingState" class="d-none py-5 text-center">
                    <div class="spinner-border spinner-border-sm text-secondary me-2" role="status"></div>
                    <span class="text-secondary fw-medium" style="font-size: 0.88rem;">Mencari data...</span>
                </div>

                <!-- 3. Result State: Structured Native UI -->
                <div id="commandResultState" class="d-none p-3">
                    <div id="commandResultContent"></div>
                </div>

            </div>

            <!-- Command Palette Footer -->
            <div class="spp-command-palette-footer d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span><kbd class="command-kbd-badge">↑</kbd> <kbd class="command-kbd-badge">↓</kbd> Navigasi</span>
                    <span><kbd class="command-kbd-badge">↵</kbd> Jalankan</span>
                    <span><kbd class="command-kbd-badge">ESC</kbd> Tutup</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted" style="font-size: 0.72rem;">
                        <i class="mdi mdi-shield-check text-success me-1"></i> Role Protected
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const commandModalEl = document.getElementById('aiAgentModal');
    const commandInput = document.getElementById('commandPaletteInput');
    const commandClearBtn = document.getElementById('commandPaletteClearBtn');
    const commandInitialState = document.getElementById('commandInitialState');
    const commandLoadingState = document.getElementById('commandLoadingState');
    const commandResultState = document.getElementById('commandResultState');
    const commandResultContent = document.getElementById('commandResultContent');
    const commandSuggestionsList = document.getElementById('commandSuggestionsList');

    let selectedSuggestionIndex = -1;

    // Detect OS for shortcut display (⌘K on Mac, Ctrl K on Windows/Linux)
    const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
    const kbdShortcutText = isMac ? '⌘K' : 'Ctrl K';

    document.querySelectorAll('.command-bar-kbd, .sidebar-kbd-badge, #navKbdBadge, #commandKbdShortcut').forEach(el => {
        el.textContent = kbdShortcutText;
    });

    // Global Keyboard Shortcut: Ctrl + K or Cmd + K
    window.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            openCommandPalette();
        }
    });

    function openCommandPalette() {
        if (!commandModalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(commandModalEl);
        modal.show();
    }

    // Auto-focus input when modal opens
    if (commandModalEl) {
        commandModalEl.addEventListener('shown.bs.modal', function () {
            if (commandInput) {
                commandInput.focus();
                commandInput.select();
            }
        });

        // Reset state when closed
        commandModalEl.addEventListener('hidden.bs.modal', function () {
            selectedSuggestionIndex = -1;
            highlightSuggestion(-1);
        });
    }

    // Clear Button
    if (commandClearBtn) {
        commandClearBtn.addEventListener('click', function () {
            commandInput.value = '';
            commandClearBtn.classList.add('d-none');
            showInitialState();
            commandInput.focus();
        });
    }

    // Input Keydown Handling (Arrow keys & Enter)
    if (commandInput) {
        commandInput.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                commandClearBtn.classList.remove('d-none');
            } else {
                commandClearBtn.classList.add('d-none');
                showInitialState();
            }
        });

        commandInput.addEventListener('keydown', function (e) {
            const items = commandSuggestionsList ? commandSuggestionsList.querySelectorAll('.command-item') : [];

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length === 0 || !commandResultState.classList.contains('d-none')) return;
                selectedSuggestionIndex = (selectedSuggestionIndex + 1) % items.length;
                highlightSuggestion(selectedSuggestionIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length === 0 || !commandResultState.classList.contains('d-none')) return;
                selectedSuggestionIndex = (selectedSuggestionIndex - 1 + items.length) % items.length;
                highlightSuggestion(selectedSuggestionIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedSuggestionIndex >= 0 && items[selectedSuggestionIndex] && commandResultState.classList.contains('d-none')) {
                    const prompt = items[selectedSuggestionIndex].getAttribute('data-prompt');
                    executeCommand(prompt);
                } else {
                    const prompt = this.value.trim();
                    if (prompt) {
                        executeCommand(prompt);
                    }
                }
            }
        });
    }

    function highlightSuggestion(index) {
        const items = commandSuggestionsList ? commandSuggestionsList.querySelectorAll('.command-item') : [];
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Click on suggestion items
    if (commandSuggestionsList) {
        commandSuggestionsList.querySelectorAll('.command-item').forEach(item => {
            item.addEventListener('click', function () {
                const prompt = this.getAttribute('data-prompt');
                if (prompt) {
                    executeCommand(prompt);
                }
            });
        });
    }

    function showInitialState() {
        commandInitialState.classList.remove('d-none');
        commandLoadingState.classList.add('d-none');
        commandResultState.classList.add('d-none');
        commandResultContent.innerHTML = '';
    }

    function showLoadingState() {
        commandInitialState.classList.add('d-none');
        commandLoadingState.classList.remove('d-none');
        commandResultState.classList.add('d-none');
    }

    function showResultState() {
        commandInitialState.classList.add('d-none');
        commandLoadingState.classList.add('d-none');
        commandResultState.classList.remove('d-none');
    }

    // Execute Natural Language Command via API
    function executeCommand(prompt) {
        if (!prompt) return;
        commandInput.value = prompt;
        commandClearBtn.classList.remove('d-none');

        showLoadingState();

        fetch("{{ route('ai.query') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ prompt: prompt })
        })
        .then(res => res.json())
        .then(data => {
            renderStructuredResponse(data);
        })
        .catch(err => {
            renderStructuredResponse({
                status: 'error',
                response_type: 'error',
                title: 'Kendala Koneksi',
                message: 'Tidak dapat terhubung ke server: ' + err.message,
                data: [],
                actions: []
            });
        });
    }

    // Render Native Structured Response (No Chat Bubbles / AI Slop)
    function renderStructuredResponse(res) {
        showResultState();

        const status = res.status || 'success';
        const responseType = res.response_type || res.type || 'text';
        const title = res.title || 'Hasil Permintaan';
        const message = res.message || '';
        const data = res.data || [];
        const actions = Array.isArray(res.actions) ? res.actions : [];

        let html = '';

        // Unauthorized State
        if (status === 'unauthorized') {
            html = `
                <div class="p-3 rounded-2 border" style="background: #fffafa; border-color: #fecaca !important;">
                    <div class="d-flex align-items-center mb-1 text-danger">
                        <i class="mdi mdi-shield-alert-outline fs-5 me-2"></i>
                        <span class="fw-bold">${escapeHtml(title)}</span>
                    </div>
                    <p class="mb-0 text-secondary small">${escapeHtml(message)}</p>
                </div>
            `;
        }
        // Error State
        else if (status === 'error') {
            html = `
                <div class="p-3 rounded-2 border bg-light" style="border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-center mb-1 text-dark">
                        <i class="mdi mdi-alert-circle-outline fs-5 me-2 text-warning"></i>
                        <span class="fw-bold">${escapeHtml(title)}</span>
                    </div>
                    <div class="text-secondary small" style="white-space: pre-line;">${escapeHtml(message)}</div>
                </div>
            `;
        }
        // Empty State
        else if (status === 'empty' || responseType === 'empty') {
            html = `
                <div class="py-4 text-center">
                    <div class="text-muted mb-2 fs-4"><i class="mdi mdi-file-search-outline"></i></div>
                    <h6 class="fw-bold text-dark mb-1">${escapeHtml(title)}</h6>
                    <p class="text-muted small mb-3">${escapeHtml(message)}</p>
                    ${renderActionButtons(actions)}
                </div>
            `;
        }
        // 1. Student Found (Single Student Card)
        else if (responseType === 'student' && data && !Array.isArray(data)) {
            const student = data;
            html = `
                <div class="border rounded-2 p-3 bg-white" style="border-color: #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-light text-muted border mb-1" style="font-size: 0.68rem;">Siswa Ditemukan</span>
                            <h6 class="fw-bold text-dark mb-0 fs-6">${escapeHtml(student.name || student.student_name)}</h6>
                            <div class="text-muted small mt-1">
                                NIS: <strong>${escapeHtml(student.nis || '-')}</strong> &bull; 
                                Kelas: <strong>${escapeHtml(student.class || '-')}</strong>
                                ${student.status ? `&bull; Status: <span class="badge ${student.status === 'Aktif' ? 'bg-success' : 'bg-secondary'}">${student.status}</span>` : ''}
                            </div>
                        </div>
                        ${student.total_arrears ? `<span class="badge bg-danger fs-6 px-3 py-2">${escapeHtml(student.total_arrears)}</span>` : ''}
                    </div>
                    ${student.unpaid_months_count ? `
                        <div class="mt-2 mb-2">
                            <small class="text-muted d-block mb-1">Tunggakan (${student.unpaid_months_count} bulan):</small>
                            <div>${(student.months || []).map(m => `<span class="badge bg-danger text-white me-1 mb-1">${escapeHtml(m)}</span>`).join('')}</div>
                        </div>
                    ` : ''}
                    <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                        ${student.detail_url ? `<a href="${student.detail_url}" class="btn btn-sm btn-outline-secondary px-3 py-1">Detail Siswa</a>` : ''}
                        ${student.billing_url ? `<a href="${student.billing_url}" class="btn btn-sm btn-navy px-3 py-1" style="background: var(--navy-primary); color: #fff;">Lihat Tagihan</a>` : ''}
                        ${renderActionButtons(actions)}
                    </div>
                </div>
            `;
        }
        // 2. Student List (Table of multiple students)
        else if (responseType === 'student_list' && Array.isArray(data)) {
            const rows = data.map(s => `
                <tr>
                    <td class="fw-bold">${escapeHtml(s.nis)}</td>
                    <td>${escapeHtml(s.name)}</td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(s.class)}</span></td>
                    <td><span class="badge ${s.status === 'Aktif' ? 'bg-success' : 'bg-secondary'}">${escapeHtml(s.status)}</span></td>
                    <td class="text-end">
                        <a href="${s.detail_url}" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.72rem;">Detail</a>
                        <a href="${s.billing_url}" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.72rem;">Tagihan</a>
                    </td>
                </tr>
            `).join('');

            html = `
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">${escapeHtml(title)}</h6>
                            <small class="text-muted">${escapeHtml(message)}</small>
                        </div>
                    </div>
                    <div class="table-responsive rounded-2 border bg-white" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        ${renderActionButtons(actions)}
                    </div>
                </div>
            `;
        }
        // 3. Unpaid Students Table (Tunggakan Siswa)
        else if ((responseType === 'table' || responseType === 'unpaid_list') && data && data.students) {
            const students = data.students;
            const rows = students.map(u => `
                <tr>
                    <td class="fw-bold">${escapeHtml(u.nis)}</td>
                    <td>${escapeHtml(u.name)}</td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(u.class)}</span></td>
                    <td class="text-danger fw-bold">${escapeHtml(u.amount)}</td>
                    <td class="text-end">
                        <a href="${u.billing_url}" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.72rem;">Detail</a>
                    </td>
                </tr>
            `).join('');

            html = `
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">${escapeHtml(title)}</h6>
                            <small class="text-muted">${escapeHtml(message)}</small>
                        </div>
                        ${renderActionButtons(actions)}
                    </div>
                    <div class="table-responsive rounded-2 border bg-white" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tarif</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div>
            `;
        }
        // 4. Pending Payments Table
        else if ((responseType === 'table' || responseType === 'pending_list') && data && data.items) {
            const items = data.items;
            const rows = items.map(p => `
                <tr>
                    <td class="fw-bold">${escapeHtml(p.student)}</td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(p.class)}</span></td>
                    <td>${escapeHtml(p.date)}</td>
                    <td class="fw-bold text-success">${escapeHtml(p.amount)}</td>
                    <td class="text-end">
                        <a href="${p.show_url}" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.72rem;">Periksa</a>
                    </td>
                </tr>
            `).join('');

            html = `
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">${escapeHtml(title)}</h6>
                            <small class="text-muted">${escapeHtml(message)}</small>
                        </div>
                        ${renderActionButtons(actions)}
                    </div>
                    <div class="table-responsive rounded-2 border bg-white" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tanggal</th>
                                    <th>Nominal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div>
            `;
        }
        // 5. Statistic Card (Pemasukan / Ringkasan)
        else if (responseType === 'stat' || responseType === 'summary_card') {
            html = `
                <div class="border rounded-2 p-4 bg-white" style="border-color: #e2e8f0;">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">${escapeHtml(title)}</div>
                    <div class="fs-2 fw-bold text-dark mb-2" style="color: var(--navy-primary) !important;">
                        ${escapeHtml(data.total_amount || '-')}
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-light text-dark border">
                            ${escapeHtml(data.total_transactions || data.unpaid_bills_count || 0)} transaksi disetujui
                        </span>
                        ${data.pending_verification > 0 ? `
                            <span class="badge bg-warning text-dark">${data.pending_verification} butuh verifikasi</span>
                        ` : ''}
                    </div>
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        ${renderActionButtons(actions)}
                    </div>
                </div>
            `;
        }
        // 6. Monthly Report Card (Rekap Laporan Bulanan)
        else if (responseType === 'report' || responseType === 'monthly_report') {
            html = `
                <div class="border rounded-2 p-3 bg-white" style="border-color: #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">${escapeHtml(title)}</h6>
                            <small class="text-muted">${escapeHtml(message)}</small>
                        </div>
                    </div>
                    <div class="row g-2 mb-3 text-center">
                        <div class="col-6 col-sm-3">
                            <div class="p-2 rounded-2 border bg-light">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">TOTAL TRANSAKSI</small>
                                <span class="fw-bold fs-6 text-dark">${escapeHtml(data.total_transactions || '0')}</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-2 rounded-2 border bg-light">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">TOTAL PEMBAYARAN</small>
                                <span class="fw-bold fs-6" style="color: var(--navy-primary);">${escapeHtml(data.total_payment || '0')}</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-2 rounded-2 border bg-light">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">SISWA LUNAS</small>
                                <span class="fw-bold fs-6 text-success">${escapeHtml(data.students_paid || '0')}</span>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-2 rounded-2 border bg-light">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">SISWA MENUNGGAK</small>
                                <span class="fw-bold fs-6 text-danger">${escapeHtml(data.students_unpaid || '0')}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        ${renderActionButtons(actions)}
                    </div>
                </div>
            `;
        }
        // 7. Navigation Card
        else if (responseType === 'nav' || responseType === 'navigation') {
            html = `
                <div class="border rounded-2 p-3 bg-white" style="border-color: #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-muted border mb-1" style="font-size: 0.68rem;">Pintasan Navigasi</span>
                            <h6 class="fw-bold text-dark mb-1 fs-6">${escapeHtml(data.label || title)}</h6>
                            <small class="text-muted">${escapeHtml(data.description || message)}</small>
                        </div>
                        <a href="${data.url}" class="btn btn-sm btn-navy px-3" style="background: var(--navy-primary); color: #fff;">
                            Buka Sekarang <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            `;
        }
        // 8. Student Self Status
        else if (responseType === 'student_self_status' && data) {
            const monthBadges = Array.isArray(data.unpaid_months) && data.unpaid_months.length > 0
                ? data.unpaid_months.map(m => `<span class="badge bg-danger text-white me-1 mb-1">${escapeHtml(m)}</span>`).join('')
                : '<span class="badge bg-success text-white">Tidak ada tunggakan 🎉</span>';

            html = `
                <div class="border rounded-2 p-3 bg-white" style="border-color: #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">${escapeHtml(data.name)} (${escapeHtml(data.nis)})</h6>
                            <small class="text-muted">Kelas: ${escapeHtml(data.class)}</small>
                        </div>
                        <span class="badge bg-light text-dark border fs-6 px-3 py-2">${escapeHtml(data.total_arrears)}</span>
                    </div>
                    <div class="row g-2 mb-2 text-center">
                        <div class="col-6">
                            <div class="p-2 rounded bg-light border">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">LUNAS</small>
                                <span class="fw-bold text-success">${data.paid_count} Bulan</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-light border">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">BELUM BAYAR</small>
                                <span class="fw-bold text-danger">${data.unpaid_count} Bulan</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 mb-3">
                        <small class="text-muted d-block mb-1">Rincian Bulan Belum Lunas:</small>
                        <div>${monthBadges}</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        ${renderActionButtons(actions)}
                    </div>
                </div>
            `;
        }
        // Fallback Text Display
        else {
            html = `
                <div class="border rounded-2 p-3 bg-white" style="border-color: #e2e8f0;">
                    <h6 class="fw-bold text-dark mb-1 fs-6">${escapeHtml(title)}</h6>
                    <p class="text-secondary small mb-2" style="white-space: pre-line;">${escapeHtml(message)}</p>
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        ${renderActionButtons(actions)}
                    </div>
                </div>
            `;
        }

        commandResultContent.innerHTML = html;
    }

    function renderActionButtons(actions) {
        if (!Array.isArray(actions) || actions.length === 0) return '';

        return actions.map(act => {
            if (!act || !act.url) return '';
            const isPrimary = act.variant === 'primary';
            const btnStyle = isPrimary 
                ? 'background: var(--navy-primary); color: #fff; border: 1px solid var(--navy-primary);'
                : 'background: #ffffff; color: var(--navy-primary); border: 1px solid var(--border);';
            const iconHtml = act.icon ? `<i class="mdi ${escapeHtml(act.icon)} me-1"></i>` : '';

            return `
                <a href="${act.url}" class="btn btn-sm px-3 py-1 fw-semibold" style="${btnStyle}">
                    ${iconHtml}${escapeHtml(act.label)}
                </a>
            `;
        }).join('');
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
});
</script>
