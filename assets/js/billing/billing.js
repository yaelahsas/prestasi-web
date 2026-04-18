$(document).ready(function() {
    // Initialize DataTables
    initPeriodTable();
    initBillingTable();
    initTarifTable();
    
    // Load dropdown data
    loadActivePeriods();
    
    // Sidebar toggle
    $('#btnSidebar').click(function() {
        $('#sidebar').toggleClass('-translate-x-full');
        $('#overlay').toggleClass('hidden');
    });
    
    $('#overlay').click(function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#overlay').addClass('hidden');
    });
    
    // Refresh button
    $('#refreshBtn').click(function() {
        refreshPeriodTable();
        refreshBillingTable();
        refreshTarifTable();
    });
});

// ============================================
// TAB FUNCTIONS
// ============================================

function switchTab(tabName) {
    // Hide all tab contents
    $('.tab-content').removeClass('active');
    
    // Remove active class from all tab buttons
    $('.tab-btn').removeClass('active');
    
    // Show selected tab content
    $('#tab-' + tabName).addClass('active');
    
    // Add active class to clicked tab button
    $(event.target).closest('.tab-btn').addClass('active');
}

// ============================================
// PERIODE TABLE FUNCTIONS
// ============================================

function initPeriodTable() {
    $('#periodTable').DataTable({
        ajax: {
            url: base_url + 'billing/get_period_data',
            dataSrc: 'data'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 }
        ],
        order: [[1, 'desc']],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        }
    });
}

function refreshPeriodTable() {
    $('#periodTable').DataTable().ajax.reload(null, false);
}

function openPeriodModal() {
    $('#periodForm')[0].reset();
    $('#id_period').val('');
    $('#periodModalTitle').text('Tambah Periode');
    $('#periodModal').addClass('show');
}

function editPeriod(id) {
    $.ajax({
        url: base_url + 'billing/get_period_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const period = response.data;
                
                $('#id_period').val(period.id_period);
                $('#bulan').val(period.bulan);
                $('#tahun').val(period.tahun);
                $('#tanggal_mulai').val(period.tanggal_mulai);
                $('#tanggal_selesai').val(period.tanggal_selesai);
                $('#status').val(period.status);
                
                $('#periodModalTitle').text('Edit Periode');
                $('#periodModal').addClass('show');
            }
        }
    });
}

function closePeriodModal() {
    $('#periodModal').removeClass('show');
}

function deletePeriod(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data periode yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'billing/delete_period/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire(
                            'Terhapus!',
                            'Data periode berhasil dihapus.',
                            'success'
                        );
                        refreshPeriodTable();
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message,
                            'error'
                        );
                    }
                }
            });
        }
    });
}

$('#periodForm').submit(function(e) {
    e.preventDefault();
    
    const btn = $('#savePeriodBtn');
    btn.addClass('loading');
    
    $.ajax({
        url: base_url + 'billing/save_period',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            btn.removeClass('loading');
            
            if (response.status === 'success') {
                Swal.fire(
                    'Berhasil!',
                    response.message,
                    'success'
                );
                closePeriodModal();
                refreshPeriodTable();
            } else {
                Swal.fire(
                    'Gagal!',
                    response.message,
                    'error'
                );
            }
        },
        error: function() {
            btn.removeClass('loading');
            Swal.fire(
                'Error!',
                'Terjadi kesalahan saat menyimpan data',
                'error'
            );
        }
    });
});

// ============================================
// BILLING TABLE FUNCTIONS
// ============================================

function initBillingTable() {
    $('#billingTable').DataTable({
        ajax: {
            url: base_url + 'billing/get_billing_data',
            dataSrc: 'data'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8 }
        ],
        order: [[1, 'desc']],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        }
    });
}

function refreshBillingTable() {
    $('#billingTable').DataTable().ajax.reload(null, false);
}

function viewBilling(id) {
    $.ajax({
        url: base_url + 'billing/get_billing_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const billing = response.data;
                
                // Get billing detail
                $.ajax({
                    url: base_url + 'billing/get_billing_detail/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(detailResponse) {
                        if (detailResponse.status === 'success') {
                            // Get billing jurnal
                            $.ajax({
                                url: base_url + 'billing/get_billing_jurnal/' + id,
                                type: 'GET',
                                dataType: 'json',
                                success: function(jurnalResponse) {
                                    if (jurnalResponse.status === 'success') {
                                        // Group jurnal by jenis kegiatan
                                        const regulerJurnals = jurnalResponse.data.filter(j => j.jenis_kegiatan === 'reguler');
                                        const olimpiadeJurnals = jurnalResponse.data.filter(j => j.jenis_kegiatan === 'olimpiade');
                                        const luringJurnals = jurnalResponse.data.filter(j => j.jenis_kegiatan === 'luring');
                                        const daringJurnals = jurnalResponse.data.filter(j => j.jenis_kegiatan === 'daring');
                                        
                                        // Get details for totals
                                        const regulerDetails = detailResponse.data.filter(d => d.jenis_kegiatan === 'reguler');
                                        const olimpiadeDetails = detailResponse.data.filter(d => d.jenis_kegiatan === 'olimpiade');
                                        const luringDetails = detailResponse.data.filter(d => d.jenis_kegiatan === 'luring');
                                        const daringDetails = detailResponse.data.filter(d => d.jenis_kegiatan === 'daring');
                                        
                                        // Calculate totals
                                        let totalReguler = 0;
                                        let totalOlimpiade = 0;
                                        let totalLuring = 0;
                                        let totalDaring = 0;
                                        
                                        regulerDetails.forEach(d => totalReguler += parseFloat(d.subtotal_honor));
                                        olimpiadeDetails.forEach(d => totalOlimpiade += parseFloat(d.subtotal_honor));
                                        luringDetails.forEach(d => totalLuring += parseFloat(d.subtotal_honor));
                                        daringDetails.forEach(d => totalDaring += parseFloat(d.subtotal_honor));
                                        
                                        let detailHtml = `
                                            <div class="space-y-4">
                                                <!-- Header Info -->
                                                <div class="bg-gradient-to-r from-blue-50 to-green-50 p-4 rounded-lg">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Kode Billing</label>
                                                            <p class="font-bold text-lg text-blue-600">${billing.kode_billing}</p>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Status</label>
                                                            <p class="font-bold text-lg">${ucfirst(billing.status)}</p>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Guru</label>
                                                            <p class="font-semibold">${billing.nama_guru}</p>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">NIP</label>
                                                            <p class="font-semibold">${billing.nip || '-'}</p>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Periode</label>
                                                            <p class="font-semibold">${billing.nama_period}</p>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-600">Total Jurnal</label>
                                                            <p class="font-semibold">${billing.total_jurnal} Jurnal</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Summary by Jenis Kegiatan -->
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div class="bg-blue-50 p-4 rounded-lg">
                                                        <h4 class="font-semibold text-blue-800 mb-2">
                                                            <i class="fas fa-chalkboard mr-2"></i>Reguler
                                                        </h4>
                                                        <div class="space-y-1">
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Jumlah Jurnal:</span>
                                                                <span class="font-semibold">${regulerJurnals.length}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Total Honor:</span>
                                                                <span class="font-semibold text-blue-600">Rp ${formatNumber(totalReguler)}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="bg-purple-50 p-4 rounded-lg">
                                                        <h4 class="font-semibold text-purple-800 mb-2">
                                                            <i class="fas fa-trophy mr-2"></i>Olimpiade
                                                        </h4>
                                                        <div class="space-y-1">
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Jumlah Jurnal:</span>
                                                                <span class="font-semibold">${olimpiadeJurnals.length}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Total Honor:</span>
                                                                <span class="font-semibold text-purple-600">Rp ${formatNumber(totalOlimpiade)}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-4 mt-4">
                                                    <div class="bg-orange-50 p-4 rounded-lg">
                                                        <h4 class="font-semibold text-orange-800 mb-2">
                                                            <i class="fas fa-users mr-2"></i>Luring
                                                        </h4>
                                                        <div class="space-y-1">
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Jumlah Jurnal:</span>
                                                                <span class="font-semibold">${luringJurnals.length}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Total Honor:</span>
                                                                <span class="font-semibold text-orange-600">Rp ${formatNumber(totalLuring)}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="bg-green-50 p-4 rounded-lg">
                                                        <h4 class="font-semibold text-green-800 mb-2">
                                                            <i class="fas fa-laptop mr-2"></i>Daring
                                                        </h4>
                                                        <div class="space-y-1">
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Jumlah Jurnal:</span>
                                                                <span class="font-semibold">${daringJurnals.length}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-sm text-gray-600">Total Honor:</span>
                                                                <span class="font-semibold text-green-600">Rp ${formatNumber(totalDaring)}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Total Honor -->
                                                <div class="bg-gray-50 p-4 rounded-lg">
                                                    <h4 class="font-semibold mb-3">
                                                        <i class="fas fa-money-bill-wave mr-2"></i>Total Honor
                                                    </h4>
                                                    <div class="text-center">
                                                        <p class="text-3xl font-bold text-blue-600">Rp ${formatNumber(billing.total_honor)}</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Detail Jurnal -->
                                                <div class="border-t pt-4">
                                                    <h4 class="font-semibold mb-3 text-blue-800">
                                                        <i class="fas fa-book mr-2"></i>Detail Jurnal
                                                    </h4>
                                                    ${jurnalResponse.data.length > 0 ? `
                                                        <div class="overflow-x-auto">
                                                            <table class="w-full text-sm">
                                                                <thead>
                                                                    <tr class="bg-blue-100">
                                                                        <th class="p-2 text-left">Tanggal</th>
                                                                        <th class="p-2 text-left">Materi</th>
                                                                        <th class="p-2 text-left">Kelas - Mapel</th>
                                                                        <th class="p-2 text-left">Jenis Kegiatan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    ${jurnalResponse.data.map(jurnal => `
                                                                        <tr class="border-b hover:bg-blue-50">
                                                                            <td class="p-2">${formatDate(jurnal.tanggal)}</td>
                                                                            <td class="p-2">${jurnal.materi}</td>
                                                                            <td class="p-2">${jurnal.nama_kelas} - ${jurnal.nama_mapel}</td>
                                                                            <td class="p-2">
                                                                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${getJenisKegiatanBadgeClass(jurnal.jenis_kegiatan)}">
                                                                                    ${ucfirst(jurnal.jenis_kegiatan)}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    `).join('')}
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    ` : '<p class="text-gray-500 italic">Tidak ada jurnal</p>'}
                                                </div>
                                            </div>
                                        `;
                                        
                                        $('#viewBillingContent').html(detailHtml);
                                        $('#viewBillingModal').addClass('show');
                                    }
                                }
                            });
                        }
                    }
                });
            }
        }
    });
}

function closeViewBillingModal() {
    $('#viewBillingModal').removeClass('show');
}

function deleteBilling(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data billing yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'billing/delete_billing/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire(
                            'Terhapus!',
                            'Data billing berhasil dihapus.',
                            'success'
                        );
                        refreshBillingTable();
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message,
                            'error'
                        );
                    }
                }
            });
        }
    });
}

// ============================================
// GENERATE BILLING FUNCTIONS
// ============================================

function openGenerateModal() {
    $('#generateForm')[0].reset();
    $('#generateModal').addClass('show');
    
    // Load active periods
    loadActivePeriods();
}

function closeGenerateModal() {
    $('#generateModal').removeClass('show');
}

$('#generateForm').submit(function(e) {
    e.preventDefault();
    
    const btn = $('#generateBtn');
    btn.addClass('loading');
    
    $.ajax({
        url: base_url + 'billing/generate_billing_all',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            btn.removeClass('loading');
            
            if (response.status === 'success') {
                Swal.fire(
                    'Berhasil!',
                    response.message,
                    'success'
                );
                closeGenerateModal();
                refreshBillingTable();
            } else {
                Swal.fire(
                    'Gagal!',
                    response.message,
                    'error'
                );
            }
        },
        error: function() {
            btn.removeClass('loading');
            Swal.fire(
                'Error!',
                'Terjadi kesalahan saat generate billing',
                'error'
            );
        }
    });
});

// ============================================
// TARIF TABLE FUNCTIONS
// ============================================

function initTarifTable() {
    $('#tarifTable').DataTable({
        ajax: {
            url: base_url + 'billing/get_tarif_data',
            dataSrc: 'data'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 }
        ],
        order: [[1, 'asc']],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        }
    });
}

function refreshTarifTable() {
    $('#tarifTable').DataTable().ajax.reload(null, false);
}

function openTarifModal() {
    $('#tarifForm')[0].reset();
    $('#id_tarif').val('');
    $('#tarifModalTitle').text('Tambah Tarif');
    $('#tarifModal').addClass('show');
}

function editTarif(id) {
    $.ajax({
        url: base_url + 'billing/get_tarif_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const tarif = response.data;
                
                $('#id_tarif').val(tarif.id_tarif);
                $('#jenis_kegiatan').val(tarif.jenis_kegiatan);
                $('#tarif').val(tarif.tarif);
                $('#status').val(tarif.status);
                $('#keterangan').val(tarif.keterangan);
                
                $('#tarifModalTitle').text('Edit Tarif');
                $('#tarifModal').addClass('show');
            }
        }
    });
}

function closeTarifModal() {
    $('#tarifModal').removeClass('show');
}

function deleteTarif(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data tarif yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'billing/delete_tarif/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire(
                            'Terhapus!',
                            'Data tarif berhasil dihapus.',
                            'success'
                        );
                        refreshTarifTable();
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message,
                            'error'
                        );
                    }
                }
            });
        }
    });
}

$('#tarifForm').submit(function(e) {
    e.preventDefault();
    
    const btn = $('#saveTarifBtn');
    btn.addClass('loading');
    
    $.ajax({
        url: base_url + 'billing/save_tarif',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            btn.removeClass('loading');
            
            if (response.status === 'success') {
                Swal.fire(
                    'Berhasil!',
                    response.message,
                    'success'
                );
                closeTarifModal();
                refreshTarifTable();
            } else {
                Swal.fire(
                    'Gagal!',
                    response.message,
                    'error'
                );
            }
        },
        error: function() {
            btn.removeClass('loading');
            Swal.fire(
                'Error!',
                'Terjadi kesalahan saat menyimpan data tarif',
                'error'
            );
        }
    });
});

// ============================================
// LOAD DATA FUNCTIONS
// ============================================

function loadActivePeriods() {
    $.ajax({
        url: base_url + 'billing/get_active_periods',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let options = '<option value="">-- Pilih Periode --</option>';
                response.data.forEach(function(period) {
                    options += `<option value="${period.id_period}">${period.nama_period}</option>`;
                });
                $('#generate_id_period').html(options);
                
                // Initialize Select2 if not already initialized
                if (!$('#generate_id_period').hasClass('select2-hidden-accessible')) {
                    $('#generate_id_period').select2({
                        placeholder: '-- Pilih Periode --',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#generateModal'),
                        language: {
                            noResults: function() {
                                return 'Tidak ada data ditemukan';
                            },
                            searching: function() {
                                return 'Mencari...';
                            }
                        }
                    });
                } else {
                    // Refresh Select2 if already initialized
                    $('#generate_id_period').trigger('change.select2');
                }
            }
        }
    });
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function getJenisKegiatanBadgeClass(jenisKegiatan) {
    switch(jenisKegiatan) {
        case 'reguler':
            return 'bg-blue-100 text-blue-800';
        case 'olimpiade':
            return 'bg-purple-100 text-purple-800';
        case 'luring':
            return 'bg-orange-100 text-orange-800';
        case 'daring':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// ============================================
// PDF PRINTING FUNCTIONS
// ============================================

function printBillingPdf(id) {
    if (!id) {
        Swal.fire(
            'Error!',
            'Tidak ada billing yang dipilih',
            'error'
        );
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang membuat PDF',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Open PDF in new window
    const pdfUrl = base_url + 'billing/print_billing_pdf/' + id;
    window.open(pdfUrl, '_blank');
    
    // Close loading after a short delay
    setTimeout(() => {
        Swal.close();
    }, 1000);
}
