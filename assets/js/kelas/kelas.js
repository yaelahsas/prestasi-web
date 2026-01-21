$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#kelasTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "kelas/get_data",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "0" },
            { "data": "1" },
            { "data": "2" },
            { "data": "3" },
            { "data": "4" }
        ],
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    });
    
    // Update statistik kelas
    updateStats();

    // Event handlers
    $('#kelasForm').on('submit', function(e) {
        e.preventDefault();
        saveKelas();
    });

    $('#refreshBtn').on('click', function() {
        refreshTable();
    });

    $('#searchBtn').on('click', function() {
        searchKelas();
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            searchKelas();
        }
    });

    // Sidebar toggle untuk mobile
    $('#btnSidebar').on('click', function() {
        $('#sidebar').toggleClass('-translate-x-full');
        $('#overlay').toggleClass('hidden');
    });

    $('#overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#overlay').addClass('hidden');
    });

    // Close modal saat klik di luar modal
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('modal')) {
            closeModal();
        }
    });
});

// Fungsi untuk membuka modal
function openModal() {
    resetForm();
    $('#modalTitle').text('Tambah Kelas');
    $('#kelasModal').addClass('show');
}

// Fungsi untuk menutup modal
function closeModal() {
    $('#kelasModal').removeClass('show');
    resetForm();
}

// Fungsi untuk mereset form
function resetForm() {
    $('#kelasForm')[0].reset();
    $('#id_kelas').val('');
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
}

// Fungsi untuk menyimpan data kelas
function saveKelas() {
    // Show loading
    $('#saveBtn').addClass('loading');
    
    // Clear previous errors
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
    
    $.ajax({
        url: base_url + 'kelas/save',
        type: 'POST',
        data: $('#kelasForm').serialize(),
        dataType: 'json',
        success: function(response) {
            $('#saveBtn').removeClass('loading');
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                closeModal();
                refreshTable();
                updateStats();
            } else {
                // Show validation errors
                if (response.errors) {
                    $.each(response.errors, function(key, value) {
                        $('#' + key + '_error').text(value).show();
                        $('#' + key).addClass('border-red-500');
                    });
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            $('#saveBtn').removeClass('loading');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data'
            });
        }
    });
}

// Fungsi untuk edit kelas
function editKelas(id) {
    $.ajax({
        url: base_url + 'kelas/get_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let kelas = response.data;
                
                $('#modalTitle').text('Edit Kelas');
                $('#id_kelas').val(kelas.id_kelas);
                $('#nama_kelas').val(kelas.nama_kelas);
                $('#tingkat').val(kelas.tingkat);
                $('#status').val(kelas.status);
                
                $('#kelasModal').addClass('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data kelas'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat data kelas'
            });
        }
    });
}

// Fungsi untuk hapus kelas
function deleteKelas(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data kelas yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'kelas/delete/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        refreshTable();
                        updateStats();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus data'
                    });
                }
            });
        }
    });
}

// Fungsi untuk toggle status kelas
function toggleStatus(id, currentStatus) {
    let newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
    let actionText = newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Anda akan ' + actionText + ' kelas ini!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, ' + actionText + '!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'kelas/toggle_status/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        refreshTable();
                        updateStats();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengubah status'
                    });
                }
            });
        }
    });
}

// Fungsi untuk refresh tabel
function refreshTable() {
    $('#kelasTable').DataTable().ajax.reload();
}

// Fungsi untuk search kelas
function searchKelas() {
    let keyword = $('#searchInput').val();
    
    if (keyword.trim() === '') {
        refreshTable();
        return;
    }
    
    $.ajax({
        url: base_url + 'kelas/search',
        type: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Clear current table data
                $('#kelasTable').DataTable().clear();
                
                // Add new data
                let data = [];
                $.each(response.data, function(index, kelas) {
                    let statusBadge = '<span class="px-3 py-1 rounded-full text-xs font-medium ' + 
                                     (kelas.status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + 
                                     '">' + kelas.status.charAt(0).toUpperCase() + kelas.status.slice(1) + '</span>';
                    
                    let actionButtons = '<div class="flex gap-1">' +
                                       '<button onclick="editKelas(' + kelas.id_kelas + ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">' +
                                       '<i class="fas fa-edit"></i>' +
                                       '</button>' +
                                       '<button onclick="deleteKelas(' + kelas.id_kelas + ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">' +
                                       '<i class="fas fa-trash"></i>' +
                                       '</button>' +
                                       '<button onclick="toggleStatus(' + kelas.id_kelas + ', \'' + kelas.status + '\')" class="px-3 py-1 ' + 
                                       (kelas.status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') + 
                                       ' text-white rounded transition-colors">' +
                                       '<i class="fas ' + (kelas.status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') + '"></i>' +
                                       '</button>' +
                                       '</div>';
                    
                    data.push([
                        kelas.id_kelas,
                        kelas.nama_kelas,
                        kelas.tingkat,
                        statusBadge,
                        actionButtons
                    ]);
                });
                
                $('#kelasTable').DataTable().rows.add(data).draw();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal melakukan pencarian'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat melakukan pencarian'
            });
        }
    });
}

// Fungsi untuk update statistik kelas
function updateStats() {
    $.ajax({
        url: base_url + 'kelas/get_data',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' || response.data) {
                let data = response.data || [];
                let totalKelas = data.length;
                let kelasAktif = 0;
                let kelasNonaktif = 0;
                
                $.each(data, function(index, kelas) {
                    if (kelas[3].includes('Aktif')) {
                        kelasAktif++;
                    } else {
                        kelasNonaktif++;
                    }
                });
                
                $('#totalKelas').text(totalKelas);
                $('#kelasAktif').text(kelasAktif);
                $('#kelasNonaktif').text(kelasNonaktif);
            }
        },
        error: function() {
            console.log('Error updating stats');
        }
    });
}