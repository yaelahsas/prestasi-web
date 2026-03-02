/**
 * WhatsApp Bot Management - JavaScript
 * Mengelola sesi WhatsApp Bot menggunakan Baileys API
 */

let currentQRSession = null;
let qrRefreshInterval = null;
let statusRefreshInterval = null;

// ===== TAB MANAGEMENT =====

function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    // Remove active from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('tab-active');
        el.classList.add('text-gray-500');
    });

    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('tab-active');
    activeTab.classList.remove('text-gray-500');

    // Load data for specific tabs
    if (tabName === 'logs') {
        loadLogs();
    }
    if (tabName === 'info') {
        checkServerStatus();
    }
    if (tabName === 'settings') {
        loadBotSettings();
    }
}

// ===== SESSION MANAGEMENT =====

function openAddSessionModal() {
    document.getElementById('modalAddSession').classList.remove('hidden');
    document.getElementById('newSessionId').focus();
}

function closeAddSessionModal() {
    document.getElementById('modalAddSession').classList.add('hidden');
    document.getElementById('newSessionId').value = '';
    document.getElementById('newSessionName').value = '';
    document.getElementById('newSessionDesc').value = '';
}

function saveNewSession() {
    const sessionId   = document.getElementById('newSessionId').value.trim();
    const sessionName = document.getElementById('newSessionName').value.trim();
    const description = document.getElementById('newSessionDesc').value.trim();

    if (!sessionId || !sessionName) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Tidak Lengkap',
            text: 'Session ID dan Nama Sesi wajib diisi',
            confirmButtonColor: '#22c55e',
        });
        return;
    }

    // Validate session ID format
    if (!/^[a-zA-Z0-9_\-]+$/.test(sessionId)) {
        Swal.fire({
            icon: 'warning',
            title: 'Format Tidak Valid',
            text: 'Session ID hanya boleh berisi huruf, angka, underscore (_), dan dash (-)',
            confirmButtonColor: '#22c55e',
        });
        return;
    }

    Swal.fire({
        title: 'Membuat Sesi...',
        text: 'Menghubungkan ke Baileys API',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.ajax({
        url: base_url + 'whatsapp/add_session',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ session_id: sessionId, session_name: sessionName, description: description }),
        success: function(res) {
            Swal.close();
            if (res.status === 'success') {
                closeAddSessionModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Sesi Berhasil Dibuat!',
                    text: 'Sesi "' + sessionName + '" telah dibuat. Scan QR Code untuk menghubungkan.',
                    confirmButtonColor: '#22c55e',
                    confirmButtonText: 'Scan QR Code',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    } else {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: res.message || 'Terjadi kesalahan',
                    confirmButtonColor: '#22c55e',
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            let msg = 'Terjadi kesalahan server';
            try {
                const r = JSON.parse(xhr.responseText);
                msg = r.message || msg;
            } catch(e) {}
            Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#22c55e' });
        }
    });
}

function deleteSession(sessionId, sessionName) {
    Swal.fire({
        icon: 'warning',
        title: 'Hapus Sesi?',
        html: `Apakah Anda yakin ingin menghapus sesi <strong>"${sessionName}"</strong>?<br><small class="text-gray-500">Sesi akan dihapus dari database dan Baileys API.</small>`,
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            $.ajax({
                url: base_url + 'whatsapp/delete_session/' + sessionId,
                method: 'DELETE',
                success: function(res) {
                    if (res.status === 'success') {
                        // Remove card from DOM
                        const card = document.getElementById('card-' + sessionId);
                        if (card) {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                card.remove();
                                updateSessionCount();
                            }, 300);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dihapus',
                            text: 'Sesi "' + sessionName + '" telah dihapus',
                            confirmButtonColor: '#22c55e',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message, confirmButtonColor: '#22c55e' });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghapus sesi', confirmButtonColor: '#22c55e' });
                }
            });
        }
    });
}

function logoutSession(sessionId) {
    Swal.fire({
        icon: 'warning',
        title: 'Logout Sesi?',
        text: 'Bot akan terputus dari WhatsApp. Anda perlu scan QR Code lagi untuk menghubungkan.',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Logout...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            $.ajax({
                url: base_url + 'whatsapp/logout_session/' + sessionId,
                method: 'DELETE',
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Logout',
                        text: 'Sesi telah diputus',
                        confirmButtonColor: '#22c55e',
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal logout sesi', confirmButtonColor: '#22c55e' });
                }
            });
        }
    });
}

function checkStatus(sessionId) {
    const card = document.getElementById('card-' + sessionId);
    const btn = card ? card.querySelector('button[onclick*="checkStatus"]') : null;
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }

    $.ajax({
        url: base_url + 'whatsapp/get_status/' + sessionId,
        method: 'GET',
        success: function(res) {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                btn.disabled = false;
            }

            if (res && res.status) {
                const status = res.data ? (res.data.status || 'disconnected') : 'disconnected';
                updateCardStatus(sessionId, status);

                Swal.fire({
                    icon: status === 'connected' ? 'success' : 'info',
                    title: 'Status Sesi',
                    text: 'Status: ' + ucfirst(status),
                    confirmButtonColor: '#22c55e',
                    timer: 2000,
                    showConfirmButton: false,
                });
            }
        },
        error: function() {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                btn.disabled = false;
            }
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Dapat Terhubung',
                text: 'Tidak dapat memeriksa status. Pastikan server Baileys berjalan.',
                confirmButtonColor: '#22c55e',
            });
        }
    });
}

function updateCardStatus(sessionId, status) {
    const card = document.getElementById('card-' + sessionId);
    if (!card) return;

    const dot = card.querySelector('.pulse-dot');
    const badge = card.querySelector('[class*="status-"]');

    if (dot) {
        dot.className = 'pulse-dot ' + status;
    }
    if (badge) {
        badge.className = 'text-xs px-2 py-0.5 rounded-full font-medium status-' + status;
        badge.textContent = ucfirst(status);
    }
}

function updateSessionCount() {
    const cards = document.querySelectorAll('[id^="card-"]');
    document.getElementById('totalSessions').textContent = cards.length;

    let active = 0;
    cards.forEach(card => {
        const dot = card.querySelector('.pulse-dot');
        if (dot && dot.classList.contains('connected')) active++;
    });
    document.getElementById('activeSessions').textContent = active;
}

function refreshSessions() {
    const icon = document.getElementById('refreshIcon');
    icon.classList.add('fa-spin');

    $.ajax({
        url: base_url + 'whatsapp/get_sessions',
        method: 'GET',
        success: function(res) {
            icon.classList.remove('fa-spin');
            if (res.status === 'success') {
                // Refresh status for each session
                res.data.forEach(session => {
                    updateCardStatus(session.session_id, session.status);
                });
                updateSessionCount();
            }
        },
        error: function() {
            icon.classList.remove('fa-spin');
        }
    });
}

// ===== QR CODE =====

function showQR(sessionId) {
    currentQRSession = sessionId;
    document.getElementById('qrSessionLabel').textContent = 'Session: ' + sessionId;
    document.getElementById('qrStatus').classList.add('hidden');
    document.getElementById('modalQR').classList.remove('hidden');

    loadQRCode(sessionId);

    // Auto refresh QR every 30 seconds
    clearInterval(qrRefreshInterval);
    qrRefreshInterval = setInterval(() => {
        if (currentQRSession) loadQRCode(currentQRSession);
    }, 30000);
}

function loadQRCode(sessionId) {
    document.getElementById('qrContent').innerHTML = `
        <div class="text-center text-gray-400">
            <i class="fas fa-spinner fa-spin text-3xl mb-3 block text-school-green"></i>
            <p class="text-sm">Memuat QR Code...</p>
        </div>
    `;

    $.ajax({
        url: base_url + 'whatsapp/get_qr/' + sessionId,
        method: 'GET',
        success: function(res) {
            if (res && res.qr) {
                document.getElementById('qrContent').innerHTML = `
                    <div class="qr-container shadow-md">
                        <img src="${res.qr}" alt="QR Code" class="w-48 h-48 object-contain">
                    </div>
                `;
            } else if (res && res.status === 'connected') {
                document.getElementById('qrContent').innerHTML = `
                    <div class="text-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-4xl text-school-green"></i>
                        </div>
                        <p class="text-green-600 font-medium">Sudah Terhubung!</p>
                    </div>
                `;
                document.getElementById('qrStatus').classList.remove('hidden');
                clearInterval(qrRefreshInterval);
                updateCardStatus(sessionId, 'connected');
                updateSessionCount();
            } else {
                document.getElementById('qrContent').innerHTML = `
                    <div class="text-center text-gray-400">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-exclamation-triangle text-2xl text-yellow-400"></i>
                        </div>
                        <p class="text-sm">QR Code tidak tersedia</p>
                        <p class="text-xs mt-1">Pastikan server Baileys berjalan</p>
                    </div>
                `;
            }
        },
        error: function() {
            document.getElementById('qrContent').innerHTML = `
                <div class="text-center text-gray-400">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-times-circle text-2xl text-red-400"></i>
                    </div>
                    <p class="text-sm">Tidak dapat terhubung ke server</p>
                    <p class="text-xs mt-1 text-red-400">Pastikan Baileys API berjalan</p>
                </div>
            `;
        }
    });
}

function refreshQR() {
    if (currentQRSession) {
        loadQRCode(currentQRSession);
    }
}

function closeQRModal() {
    document.getElementById('modalQR').classList.add('hidden');
    clearInterval(qrRefreshInterval);
    currentQRSession = null;
}

// ===== PAIRING CODE =====

let currentPairingSession = null;

/**
 * Tampilkan modal pairing code (step 1: input nomor telepon)
 */
function showPairingCode(sessionId) {
    currentPairingSession = sessionId;
    document.getElementById('pairingSessionLabel').textContent = 'Session: ' + sessionId;
    document.getElementById('pairingStep2SessionLabel').textContent = 'Session: ' + sessionId;
    document.getElementById('pairingPhone').value = '';
    document.getElementById('pairingStatus').classList.add('hidden');

    // Show step 1, hide step 2
    document.getElementById('pairingStep1').classList.remove('hidden');
    document.getElementById('pairingStep2').classList.add('hidden');

    document.getElementById('modalPairingCode').classList.remove('hidden');
    setTimeout(() => document.getElementById('pairingPhone').focus(), 100);
}

/**
 * Minta kode pairing dari Baileys API
 */
function requestPairingCode() {
    const phone = document.getElementById('pairingPhone').value.trim().replace(/[^0-9]/g, '');

    if (!phone) {
        Swal.fire({
            icon: 'warning',
            title: 'Nomor Kosong',
            text: 'Masukkan nomor WhatsApp terlebih dahulu',
            confirmButtonColor: '#a855f7',
        });
        return;
    }

    if (!/^62\d{8,14}$/.test(phone)) {
        Swal.fire({
            icon: 'warning',
            title: 'Format Nomor Salah',
            text: 'Gunakan format: 628xxxxxxxxxx (tanpa + atau 0 di depan)',
            confirmButtonColor: '#a855f7',
        });
        return;
    }

    // Switch to step 2 and show loading
    document.getElementById('pairingStep1').classList.add('hidden');
    document.getElementById('pairingStep2').classList.remove('hidden');
    document.getElementById('pairingCodeDisplay').innerHTML = `
        <div class="text-center text-gray-400">
            <i class="fas fa-spinner fa-spin text-3xl mb-3 block text-purple-500"></i>
            <p class="text-sm">Meminta kode pairing...</p>
        </div>
    `;

    $.ajax({
        url: base_url + 'whatsapp/get_pairing_code/' + currentPairingSession + '?phone=' + encodeURIComponent(phone),
        method: 'GET',
        timeout: 20000,
        success: function(res) {
            if (res && (res.code || res.pairingCode || res.pairing_code)) {
                const code = res.code || res.pairingCode || res.pairing_code;
                // Format kode menjadi XXXX-XXXX jika 8 karakter
                const formatted = code.length === 8
                    ? code.substring(0, 4) + '-' + code.substring(4)
                    : code;

                document.getElementById('pairingCodeDisplay').innerHTML = `
                    <div class="text-center">
                        <div class="bg-purple-50 border-2 border-purple-200 rounded-2xl px-8 py-5 inline-block mb-3">
                            <p class="text-4xl font-bold font-mono tracking-widest text-purple-700 select-all">${escHtml(formatted)}</p>
                        </div>
                        <p class="text-xs text-gray-400">Ketuk kode untuk menyalin</p>
                    </div>
                `;

                // Click to copy
                const codeEl = document.querySelector('#pairingCodeDisplay .text-4xl');
                if (codeEl) {
                    codeEl.style.cursor = 'pointer';
                    codeEl.title = 'Klik untuk menyalin';
                    codeEl.addEventListener('click', function() {
                        navigator.clipboard.writeText(code).then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Disalin!',
                                text: 'Kode pairing disalin ke clipboard',
                                confirmButtonColor: '#a855f7',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        }).catch(() => {});
                    });
                }

                // Poll for connection status
                pollPairingStatus(currentPairingSession);

            } else if (res && res.status === 'connected') {
                document.getElementById('pairingCodeDisplay').innerHTML = `
                    <div class="text-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-4xl text-school-green"></i>
                        </div>
                        <p class="text-green-600 font-medium">Sudah Terhubung!</p>
                    </div>
                `;
                document.getElementById('pairingStatus').classList.remove('hidden');
                updateCardStatus(currentPairingSession, 'connected');
                updateSessionCount();
            } else {
                const errMsg = res.message || 'Tidak dapat mendapatkan kode pairing. Pastikan server Baileys berjalan.';
                document.getElementById('pairingCodeDisplay').innerHTML = `
                    <div class="text-center text-gray-400">
                        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-times-circle text-2xl text-red-400"></i>
                        </div>
                        <p class="text-sm text-red-500">${escHtml(errMsg)}</p>
                        <p class="text-xs mt-1 text-gray-400">Pastikan server Baileys mendukung pairing code</p>
                    </div>
                `;
            }
        },
        error: function(xhr) {
            let msg = 'Tidak dapat terhubung ke server';
            try {
                const r = JSON.parse(xhr.responseText);
                msg = r.message || msg;
            } catch(e) {}
            document.getElementById('pairingCodeDisplay').innerHTML = `
                <div class="text-center text-gray-400">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-times-circle text-2xl text-red-400"></i>
                    </div>
                    <p class="text-sm text-red-500">${escHtml(msg)}</p>
                </div>
            `;
        }
    });
}

/**
 * Poll status koneksi setelah pairing code ditampilkan
 */
let pairingPollInterval = null;

function pollPairingStatus(sessionId) {
    clearInterval(pairingPollInterval);
    let attempts = 0;
    const maxAttempts = 24; // 2 menit (24 x 5 detik)

    pairingPollInterval = setInterval(function() {
        attempts++;
        if (attempts > maxAttempts) {
            clearInterval(pairingPollInterval);
            return;
        }

        $.ajax({
            url: base_url + 'whatsapp/get_status/' + sessionId,
            method: 'GET',
            success: function(res) {
                const status = res.data ? (res.data.status || 'disconnected') : 'disconnected';
                if (status === 'connected') {
                    clearInterval(pairingPollInterval);
                    document.getElementById('pairingStatus').classList.remove('hidden');
                    document.getElementById('pairingCodeDisplay').innerHTML = `
                        <div class="text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-check-circle text-4xl text-school-green"></i>
                            </div>
                            <p class="text-green-600 font-medium">Berhasil Terhubung!</p>
                        </div>
                    `;
                    updateCardStatus(sessionId, 'connected');
                    updateSessionCount();
                }
            }
        });
    }, 5000);
}

/**
 * Kembali ke step 1 (input nomor)
 */
function backToPairingStep1() {
    clearInterval(pairingPollInterval);
    document.getElementById('pairingStep1').classList.remove('hidden');
    document.getElementById('pairingStep2').classList.add('hidden');
    document.getElementById('pairingStatus').classList.add('hidden');
}

/**
 * Tutup modal pairing code
 */
function closePairingModal() {
    clearInterval(pairingPollInterval);
    document.getElementById('modalPairingCode').classList.add('hidden');
    document.getElementById('pairingStep1').classList.remove('hidden');
    document.getElementById('pairingStep2').classList.add('hidden');
    document.getElementById('pairingStatus').classList.add('hidden');
    document.getElementById('pairingPhone').value = '';
    currentPairingSession = null;
}

// ===== SEND MESSAGE =====

function sendMessage() {
    const sessionId = document.getElementById('sendSessionId').value;
    const receiver  = document.getElementById('sendReceiver').value.trim();
    const message   = document.getElementById('sendMessage').value.trim();

    if (!sessionId) {
        Swal.fire({ icon: 'warning', title: 'Pilih Sesi', text: 'Pilih sesi bot terlebih dahulu', confirmButtonColor: '#22c55e' });
        return;
    }
    if (!receiver) {
        Swal.fire({ icon: 'warning', title: 'Nomor Kosong', text: 'Masukkan nomor penerima', confirmButtonColor: '#22c55e' });
        return;
    }
    if (!message) {
        Swal.fire({ icon: 'warning', title: 'Pesan Kosong', text: 'Masukkan pesan yang akan dikirim', confirmButtonColor: '#22c55e' });
        return;
    }

    // Validate phone number format
    if (!/^62\d{8,14}$/.test(receiver)) {
        Swal.fire({
            icon: 'warning',
            title: 'Format Nomor Salah',
            text: 'Gunakan format: 628xxxxxxxxxx (tanpa + atau 0 di depan)',
            confirmButtonColor: '#22c55e',
        });
        return;
    }

    Swal.fire({
        title: 'Mengirim Pesan...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.ajax({
        url: base_url + 'whatsapp/send_message',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ session_id: sessionId, receiver: receiver, message: message }),
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pesan Terkirim!',
                    text: 'Pesan berhasil dikirim ke ' + receiver,
                    confirmButtonColor: '#22c55e',
                    timer: 2500,
                    showConfirmButton: false,
                });
                document.getElementById('sendMessage').value = '';
                document.getElementById('charCount').textContent = '0';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim',
                    text: res.message || 'Terjadi kesalahan saat mengirim pesan',
                    confirmButtonColor: '#22c55e',
                });
            }
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengirim pesan', confirmButtonColor: '#22c55e' });
        }
    });
}

// ===== MESSAGE LOGS =====

function loadLogs() {
    const sessionFilter = document.getElementById('logSessionFilter').value;
    const tbody = document.getElementById('logsTableBody');

    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-10 text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 block"></i>
                Memuat log...
            </td>
        </tr>
    `;

    let url = base_url + 'whatsapp/get_message_logs?limit=100';
    if (sessionFilter) url += '&session_id=' + encodeURIComponent(sessionFilter);

    $.ajax({
        url: url,
        method: 'GET',
        success: function(res) {
            if (res.status === 'success' && res.data && res.data.length > 0) {
                let html = '';
                res.data.forEach((log, i) => {
                    const statusClass = log.status === 'sent'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700';
                    const statusIcon = log.status === 'sent' ? 'fa-check' : 'fa-times';

                    html += `
                        <tr class="log-row border-b border-gray-50 hover:bg-green-50 transition-colors">
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                ${formatDateTime(log.sent_at)}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded">${escHtml(log.session_id)}</span>
                            </td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-700">${escHtml(log.receiver)}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">
                                <p class="truncate" title="${escHtml(log.message)}">${escHtml(log.message)}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium ${statusClass}">
                                    <i class="fas ${statusIcon} text-[10px]"></i>
                                    ${ucfirst(log.status)}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">${escHtml(log.sent_by_name || '-')}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-3 block text-gray-200"></i>
                            <p class="text-sm">Belum ada log pesan</p>
                        </td>
                    </tr>
                `;
            }
        },
        error: function() {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-10 text-red-400">
                        <i class="fas fa-exclamation-circle text-2xl mb-2 block"></i>
                        Gagal memuat log
                    </td>
                </tr>
            `;
        }
    });
}

// ===== SERVER STATUS =====

function checkServerStatus() {
    const statusEl = document.getElementById('serverStatus');
    const apiUrlEl = document.getElementById('apiUrl');

    if (!statusEl) return;

    statusEl.textContent = 'Memeriksa...';
    statusEl.className = 'font-medium text-yellow-600';

    $.ajax({
        url: base_url + 'whatsapp/get_sessions',
        method: 'GET',
        timeout: 5000,
        success: function(res) {
            // If we can reach our own server, show basic info
            statusEl.textContent = 'Server Aktif';
            statusEl.className = 'font-medium text-green-600';
            if (apiUrlEl) apiUrlEl.textContent = window.location.origin;
        },
        error: function() {
            statusEl.textContent = 'Tidak Dapat Terhubung';
            statusEl.className = 'font-medium text-red-600';
        }
    });
}

// ===== UTILITY FUNCTIONS =====

function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatDateTime(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const pad = n => String(n).padStart(2, '0');
    return `${pad(d.getDate())} ${getMonthName(d.getMonth())} ${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

function getMonthName(month) {
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return months[month] || '';
}

// Close modals on outside click
document.addEventListener('click', function(e) {
    const modalQR = document.getElementById('modalQR');
    const modalAdd = document.getElementById('modalAddSession');

    if (e.target === modalQR) closeQRModal();
    if (e.target === modalAdd) closeAddSessionModal();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQRModal();
        closeAddSessionModal();
        closePairingModal();
    }
});

// ===== BOT SETTINGS =====

/**
 * Muat pengaturan bot dari server
 */
function loadBotSettings() {
    $.ajax({
        url: base_url + 'whatsapp/get_bot_settings',
        method: 'GET',
        success: function(res) {
            if (res.status === 'success' && res.data) {
                const d = res.data;
                // Isi form dengan data dari server
                setFieldValue('env_BAILEYS_API_URL',            d.BAILEYS_API_URL            || 'http://localhost:3000');
                setFieldValue('env_APP_PORT',                   d.APP_PORT                   || '3000');
                setFieldValue('env_MAX_RETRIES',                d.MAX_RETRIES                || '0');
                setFieldValue('env_RECONNECT_INTERVAL',         d.RECONNECT_INTERVAL         || '5000');
                setFieldValue('env_AUTO_READ_MESSAGES',         d.AUTO_READ_MESSAGES         || 'false');
                setFieldValue('env_BOT_AUTHORIZED_NUMBERS',     d.BOT_AUTHORIZED_NUMBERS     || '');
                setFieldValue('env_APP_WEBHOOK_URL',            d.APP_WEBHOOK_URL            || '');
                setFieldValue('env_APP_WEBHOOK_ALLOWED_EVENTS', d.APP_WEBHOOK_ALLOWED_EVENTS || 'ALL');
                setFieldValue('env_APP_WEBHOOK_FILE_IN_BASE64', d.APP_WEBHOOK_FILE_IN_BASE64 || 'false');
                setFieldValue('env_BOT_API_URL',                d.BOT_API_URL                || 'http://localhost:9998/api');
                setFieldValue('env_BOT_API_KEY',                d.BOT_API_KEY                || '');

                updateEnvPreview();
            }
        },
        error: function() {
            // Jika gagal load, isi dengan default
            updateEnvPreview();
        }
    });
}

function setFieldValue(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    el.value = value;
}

/**
 * Simpan pengaturan bot ke server
 */
function saveBotSettings() {
    const data = {
        BAILEYS_API_URL:            (document.getElementById('env_BAILEYS_API_URL')?.value || '').trim(),
        APP_PORT:                   (document.getElementById('env_APP_PORT')?.value || '').trim(),
        MAX_RETRIES:                (document.getElementById('env_MAX_RETRIES')?.value || '').trim(),
        RECONNECT_INTERVAL:         (document.getElementById('env_RECONNECT_INTERVAL')?.value || '').trim(),
        AUTO_READ_MESSAGES:         (document.getElementById('env_AUTO_READ_MESSAGES')?.value || 'false'),
        BOT_AUTHORIZED_NUMBERS:     (document.getElementById('env_BOT_AUTHORIZED_NUMBERS')?.value || '').trim(),
        APP_WEBHOOK_URL:            (document.getElementById('env_APP_WEBHOOK_URL')?.value || '').trim(),
        APP_WEBHOOK_ALLOWED_EVENTS: (document.getElementById('env_APP_WEBHOOK_ALLOWED_EVENTS')?.value || 'ALL').trim(),
        APP_WEBHOOK_FILE_IN_BASE64: (document.getElementById('env_APP_WEBHOOK_FILE_IN_BASE64')?.value || 'false'),
        BOT_API_URL:                (document.getElementById('env_BOT_API_URL')?.value || '').trim(),
        BOT_API_KEY:                (document.getElementById('env_BOT_API_KEY')?.value || '').trim(),
    };

    if (!data.BAILEYS_API_URL) {
        Swal.fire({
            icon: 'warning',
            title: 'URL Wajib Diisi',
            text: 'URL Baileys API tidak boleh kosong',
            confirmButtonColor: '#22c55e',
        });
        return;
    }

    Swal.fire({
        title: 'Menyimpan Pengaturan...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.ajax({
        url: base_url + 'whatsapp/save_bot_settings',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(res) {
            Swal.close();
            if (res.status === 'success' || res.status === 'warning') {
                Swal.fire({
                    icon: res.status === 'success' ? 'success' : 'warning',
                    title: res.status === 'success' ? 'Pengaturan Disimpan!' : 'Sebagian Tersimpan',
                    text: res.message,
                    confirmButtonColor: '#22c55e',
                    timer: 2500,
                    showConfirmButton: false,
                });
                updateEnvPreview();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: res.message || 'Terjadi kesalahan',
                    confirmButtonColor: '#22c55e',
                });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal menyimpan pengaturan',
                confirmButtonColor: '#22c55e',
            });
        }
    });
}

/**
 * Test koneksi ke Baileys API
 */
function testBaileysConnection() {
    const btn = document.getElementById('btnTestConn');
    const banner = document.getElementById('connStatusBanner');

    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Menguji...';
        btn.disabled = true;
    }

    if (banner) {
        banner.className = 'mb-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 bg-yellow-50 text-yellow-700';
        banner.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menguji koneksi ke Baileys API...';
        banner.classList.remove('hidden');
    }

    $.ajax({
        url: base_url + 'whatsapp/test_connection',
        method: 'GET',
        timeout: 10000,
        success: function(res) {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-plug text-xs"></i> Test Koneksi';
                btn.disabled = false;
            }

            if (res.status === 'success') {
                if (banner) {
                    banner.className = 'mb-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 bg-green-50 text-green-700 border border-green-200';
                    banner.innerHTML = `<i class="fas fa-check-circle"></i> <strong>Berhasil terhubung!</strong> Baileys API aktif di <code class="font-mono bg-green-100 px-1 rounded">${escHtml(res.url)}</code>`;
                }
            } else {
                if (banner) {
                    banner.className = 'mb-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 bg-red-50 text-red-700 border border-red-200';
                    banner.innerHTML = `<i class="fas fa-times-circle"></i> <strong>Gagal terhubung!</strong> ${escHtml(res.message || 'Pastikan server Baileys berjalan')}`;
                }
            }
        },
        error: function() {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-plug text-xs"></i> Test Koneksi';
                btn.disabled = false;
            }
            if (banner) {
                banner.className = 'mb-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 bg-red-50 text-red-700 border border-red-200';
                banner.innerHTML = '<i class="fas fa-times-circle"></i> <strong>Tidak dapat terhubung!</strong> Pastikan server Baileys berjalan dan URL sudah benar.';
            }
        }
    });
}

/**
 * Update preview file .env
 */
function updateEnvPreview() {
    const fields = {
        BAILEYS_API_URL:            document.getElementById('env_BAILEYS_API_URL')?.value            || 'http://localhost:3000',
        APP_PORT:                   document.getElementById('env_APP_PORT')?.value                   || '3000',
        MAX_RETRIES:                document.getElementById('env_MAX_RETRIES')?.value                || '0',
        RECONNECT_INTERVAL:         document.getElementById('env_RECONNECT_INTERVAL')?.value         || '5000',
        AUTO_READ_MESSAGES:         document.getElementById('env_AUTO_READ_MESSAGES')?.value         || 'false',
        BOT_AUTHORIZED_NUMBERS:     document.getElementById('env_BOT_AUTHORIZED_NUMBERS')?.value     || '',
        APP_WEBHOOK_URL:            document.getElementById('env_APP_WEBHOOK_URL')?.value            || '',
        APP_WEBHOOK_ALLOWED_EVENTS: document.getElementById('env_APP_WEBHOOK_ALLOWED_EVENTS')?.value || 'ALL',
        APP_WEBHOOK_FILE_IN_BASE64: document.getElementById('env_APP_WEBHOOK_FILE_IN_BASE64')?.value || 'false',
        BOT_API_URL:                document.getElementById('env_BOT_API_URL')?.value                || 'http://localhost:9998/api',
        BOT_API_KEY:                document.getElementById('env_BOT_API_KEY')?.value                || '',
    };

    const envContent =
`# ============================================================
# Konfigurasi WhatsApp Bot (Baileys)
# Generated: ${new Date().toLocaleString('id-ID')}
# ============================================================

# ===== SERVER =====
APP_PORT=${fields.APP_PORT}

# ===== KONEKSI BAILEYS =====
BAILEYS_API_URL=${fields.BAILEYS_API_URL}
MAX_RETRIES=${fields.MAX_RETRIES}
RECONNECT_INTERVAL=${fields.RECONNECT_INTERVAL}

# ===== PERILAKU BOT =====
AUTO_READ_MESSAGES=${fields.AUTO_READ_MESSAGES}
BOT_AUTHORIZED_NUMBERS=${fields.BOT_AUTHORIZED_NUMBERS}

# ===== WEBHOOK =====
APP_WEBHOOK_URL=${fields.APP_WEBHOOK_URL}
APP_WEBHOOK_ALLOWED_EVENTS=${fields.APP_WEBHOOK_ALLOWED_EVENTS}
APP_WEBHOOK_FILE_IN_BASE64=${fields.APP_WEBHOOK_FILE_IN_BASE64}

# ===== API BACKEND =====
BOT_API_URL=${fields.BOT_API_URL}
BOT_API_KEY=${fields.BOT_API_KEY}`;

    const preview = document.getElementById('envPreview');
    if (preview) preview.textContent = envContent;
}

/**
 * Salin konten .env ke clipboard
 */
function copyEnvContent() {
    const preview = document.getElementById('envPreview');
    if (!preview) return;

    const text = preview.textContent;
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Disalin!',
            text: 'Konten .env berhasil disalin ke clipboard',
            confirmButtonColor: '#22c55e',
            timer: 1500,
            showConfirmButton: false,
        });
    }).catch(() => {
        // Fallback untuk browser lama
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        Swal.fire({
            icon: 'success',
            title: 'Disalin!',
            text: 'Konten .env berhasil disalin',
            confirmButtonColor: '#22c55e',
            timer: 1500,
            showConfirmButton: false,
        });
    });
}

/**
 * Toggle visibilitas API Key
 */
function toggleApiKeyVisibility() {
    const input = document.getElementById('env_BOT_API_KEY');
    const icon  = document.getElementById('apiKeyEyeIcon');
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash text-xs';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye text-xs';
    }
}

// Auto-update preview saat ada perubahan di form settings
document.addEventListener('DOMContentLoaded', function() {
    const settingsFields = [
        'env_BAILEYS_API_URL', 'env_APP_PORT', 'env_MAX_RETRIES', 'env_RECONNECT_INTERVAL',
        'env_AUTO_READ_MESSAGES', 'env_BOT_AUTHORIZED_NUMBERS', 'env_APP_WEBHOOK_URL',
        'env_APP_WEBHOOK_ALLOWED_EVENTS', 'env_APP_WEBHOOK_FILE_IN_BASE64',
        'env_BOT_API_URL', 'env_BOT_API_KEY',
    ];
    settingsFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateEnvPreview);
            el.addEventListener('change', updateEnvPreview);
        }
    });
});
