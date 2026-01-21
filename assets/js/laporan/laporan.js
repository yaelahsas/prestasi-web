$(document).ready(function() {
    // Load sekolah info
    loadSekolahInfo();
    
    // Load statistics
    loadStatistics();
    
    // Use global variables defined in HTML
    // currentMonth and currentYear are already defined in the HTML file
    
    $('#bulan').val(currentMonth);
    $('#tahun').val(currentYear);
    $('#bulan_guru').val(currentMonth);
    $('#tahun_guru').val(currentYear);
    $('#bulan_kelas').val(currentMonth);
    $('#tahun_kelas').val(currentYear);
    $('#bulan_rekap').val(currentMonth);
    $('#tahun_rekap').val(currentYear);
    
    // Handle form submissions
    $('#formLaporanBulanan').on('submit', function(e) {
        e.preventDefault();
        
        var bulan = $('#bulan').val();
        var tahun = $('#tahun').val();
        
        // Open PDF in new window
        var url = base_url + 'laporan/cetak_jurnal_bulanan?bulan=' + bulan + '&tahun=' + tahun;
        window.open(url, '_blank');
    });
    
    $('#formLaporanGuru').on('submit', function(e) {
        e.preventDefault();
        
        var id_guru = $('#id_guru').val();
        var bulan = $('#bulan_guru').val();
        var tahun = $('#tahun_guru').val();
        
        if (!id_guru) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih guru terlebih dahulu!'
            });
            return;
        }
        
        // Open PDF in new window
        var url = base_url + 'laporan/cetak_laporan_guru?id_guru=' + id_guru + '&bulan=' + bulan + '&tahun=' + tahun;
        window.open(url, '_blank');
    });
    
    $('#formLaporanKelas').on('submit', function(e) {
        e.preventDefault();
        
        var id_kelas = $('#id_kelas').val();
        var bulan = $('#bulan_kelas').val();
        var tahun = $('#tahun_kelas').val();
        
        if (!id_kelas) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih kelas terlebih dahulu!'
            });
            return;
        }
        
        // Open PDF in new window
        var url = base_url + 'laporan/cetak_laporan_kelas?id_kelas=' + id_kelas + '&bulan=' + bulan + '&tahun=' + tahun;
        window.open(url, '_blank');
    });
    
    $('#formRekapKehadiran').on('submit', function(e) {
        e.preventDefault();
        
        var bulan = $('#bulan_rekap').val();
        var tahun = $('#tahun_rekap').val();
        
        // Open PDF in new window
        var url = base_url + 'laporan/cetak_rekap_kehadiran?bulan=' + bulan + '&tahun=' + tahun;
        window.open(url, '_blank');
    });
    
    // Refresh button
    $('#refreshBtn').on('click', function() {
        loadSekolahInfo();
        loadStatistics();
    });
    
    // Mobile sidebar toggle
    $('#btnSidebar').on('click', function() {
        $('#sidebar').toggleClass('-translate-x-full');
        $('#overlay').toggleClass('hidden');
    });
    
    $('#overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#overlay').addClass('hidden');
    });
});

function loadSekolahInfo() {
    $.ajax({
        url: base_url + 'sekolah/api/get_sekolah',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                $('#infoSekolah').html(`
                    <div class="alert alert-warning bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i> 
                        Data sekolah belum diatur. 
                        <a href="${base_url}sekolah" class="btn btn-sm btn-warning ml-2">
                            <i class="fas fa-cog"></i> Atur Sekarang
                        </a>
                    </div>
                `);
            } else {
                var logoHtml = response.logo_url ? 
                    `<img src="${response.logo_url}" alt="Logo" style="max-height: 60px;" class="mb-2 rounded">` : 
                    `<div class="alert alert-warning bg-yellow-50 border-l-4 border-yellow-400 p-2 rounded text-xs">Logo belum diupload</div>`;
                
                $('#infoSekolah').html(`
                    <div class="text-center">
                        ${logoHtml}
                        <h6 class="font-bold text-sm">${response.nama_sekolah}</h6>
                        <p class="text-xs text-gray-600">${response.alamat}</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#infoSekolah').html(`
                <div class="alert alert-danger bg-red-50 border-l-4 border-red-400 p-3 rounded">
                    <i class="fas fa-exclamation-circle text-red-400"></i> 
                    Gagal memuat data sekolah.
                </div>
            `);
        }
    });
}

function loadStatistics() {
    // Load total jurnal bulan ini
    $.ajax({
        url: base_url + 'api/statistik_jurnal',
        type: 'GET',
        data: {
            bulan: currentMonth,
            tahun: currentYear
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalJurnalBulanIni').text(response.data.total_jurnal || 0);
            }
        },
        error: function() {
            // Silently fail, keep default value
        }
    });
    
    // Load total guru aktif
    $.ajax({
        url: base_url + 'api/total_guru_aktif',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalGuruAktif').text(response.data.total || 0);
            }
        },
        error: function() {
            // Silently fail, keep default value
        }
    });
    
    // Load total kelas aktif
    $.ajax({
        url: base_url + 'api/total_kelas_aktif',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalKelasAktif').text(response.data.total || 0);
            }
        },
        error: function() {
            // Silently fail, keep default value
        }
    });
}