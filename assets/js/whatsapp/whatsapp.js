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
    }
});
