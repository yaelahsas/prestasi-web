$(document).ready(function() {
    // Initialize DataTable
    window.table = $('#sekolahTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "sekolah/get_sekolah_data",
            "type": "GET",
            "dataSrc": function(json) {
                console.log("DataTables response:", json);
                if (json.status === 'success') {
                    return json.data;
                } else {
                    console.error("DataTables error:", json.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data sekolah: ' + (json.message || 'Unknown error')
                    });
                    return [];
                }
            },
            "error": function(xhr, error, code) {
                console.log("DataTables error: ", error, code);
                console.log("Response:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data sekolah'
                });
                return [];
            }
        },
        "order": [[0, "desc"]],
        "columns": [
            { "data": "0" },
            { "data": "1" },
            { "data": "2" },
            { "data": "3" },
            { "data": "4" },
            { "data": "5" },
            { "data": "6" }
        ],
        "columnDefs": [
            { "targets": "_all", "orderable": false }
        ],
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        },
        "initComplete": function() {
            console.log("DataTable initialized successfully");
        }
    });

    // Load statistics
    loadStatistics();

    // Form submission
    $('#sekolahForm').on('submit', function(e) {
        e.preventDefault();
        saveSekolah();
    });

    // Search functionality
    $('#searchBtn').on('click', function() {
        const keyword = $('#searchInput').val();
        searchSekolah(keyword);
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            const keyword = $(this).val();
            searchSekolah(keyword);
        }
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        refreshTable();
    });

    // File upload preview
    $('#logo').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#fileLabel').text('File terpilih: ' + fileName);
        } else {
            $('#fileLabel').text('Pilih file logo (jpg, png, gif - max 2MB)');
        }
    });

    // Sidebar toggle for mobile
    $('#btnSidebar').on('click', function() {
        $('#sidebar').toggleClass('-translate-x-full');
        $('#overlay').toggleClass('hidden');
    });

    $('#overlay').on('click', function() {
        $('#sidebar').addClass('-translate-x-full');
        $('#overlay').addClass('hidden');
    });


});

// Open modal for add/edit
function openModal(id = null) {
    resetForm();
    
    if (id) {
        // Edit mode
        $('#modalTitle').text('Edit Sekolah');
        
        // Load sekolah data
        $.ajax({
            url: base_url + 'sekolah/get_sekolah_by_id/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#id_sekolah').val(response.data.id_sekolah);
                    $('#nama_sekolah').val(response.data.nama_sekolah);
                    $('#alamat').val(response.data.alamat);
                    $('#kepala_sekolah').val(response.data.kepala_sekolah);
                    $('#nip_kepsek').val(response.data.nip_kepsek);
                    
                    // Show current logo if exists
                    if (response.data.logo) {
                        $('#currentLogo').html('<img src="' + base_url + 'assets/uploads/logo/' + response.data.logo + '" alt="Current Logo" class="max-w-full h-auto rounded">');
                    }
                    
                    $('#sekolahModal').addClass('show');
                    console.log('Modal opened for edit');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data sekolah: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                console.log('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data: ' + error
                });
            }
        });
    } else {
        // Add mode
        $('#modalTitle').text('Tambah Sekolah');
        $('#sekolahModal').addClass('show');
        console.log('Modal opened for add');
    }
}

// Close modal
function closeModal() {
    console.log('closeModal function called');
    $('#sekolahModal').removeClass('show');
    resetForm();
}

// Reset form
function resetForm() {
    console.log('resetForm function called');
    $('#sekolahForm')[0].reset();
    $('#id_sekolah').val('');
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
    $('#currentLogo').html('');
    $('#fileLabel').text('Pilih file logo (jpg, png, gif - max 2MB)');
}

// Save sekolah (add/edit)
function saveSekolah() {
    const formData = new FormData($('#sekolahForm')[0]);
    
    // Show loading
    $('#saveBtn').addClass('loading');
    
    $.ajax({
        url: base_url + 'sekolah/save_sekolah',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            $('#saveBtn').removeClass('loading');
            console.log('Save response:', response);
            
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(function() {
                    closeModal();
                    // Force refresh table with null parameter to clear cache
                    if (typeof window.table !== 'undefined') {
                        window.table.ajax.reload(null, false);
                        loadStatistics();
                    }
                });
            } else {
                if (response.errors) {
                    // Show validation errors
                    $('.error-message').hide();
                    $('.form-control').removeClass('border-red-500');
                    
                    $.each(response.errors, function(key, value) {
                        $('#' + key + '_error').text(value).show();
                        $('#' + key).addClass('border-red-500');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            }
        },
        error: function() {
            $('#saveBtn').removeClass('loading');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengirim data'
            });
        }
    });
}

// Edit sekolah
function editSekolah(id) {
    console.log('Edit sekolah called with ID:', id);
    openModal(id);
}

// Delete sekolah
function deleteSekolah(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data sekolah akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'sekolah/delete_sekolah/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Delete response:', response);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            // Force refresh table with null parameter to clear cache
                            if (typeof window.table !== 'undefined') {
                                window.table.ajax.reload(null, false);
                                loadStatistics();
                            }
                        });
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

// Search sekolah
function searchSekolah(keyword) {
    if (keyword.trim() === '') {
        window.table.ajax.reload();
        return;
    }
    
    $.ajax({
        url: base_url + 'sekolah/search',
        type: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Clear and reload table with search results
                window.table.clear();
                
                if (response.data.length > 0) {
                    response.data.forEach(function(sekolah) {
                        const logoHtml = sekolah.logo ?
                            '<img src="' + base_url + 'assets/uploads/logo/' + sekolah.logo + '" alt="Logo" class="w-12 h-12 object-cover rounded">' : '-';
                        
                        const row = [
                            sekolah.id_sekolah,
                            sekolah.nama_sekolah,
                            sekolah.alamat ? sekolah.alamat.substring(0, 50) + '...' : '-',
                            sekolah.kepala_sekolah ? sekolah.kepala_sekolah : '-',
                            sekolah.nip_kepsek ? sekolah.nip_kepsek : '-',
                            logoHtml,
                            '<div class="flex gap-1">' +
                            '<button onclick="editSekolah(' + sekolah.id_sekolah + ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">' +
                            '<i class="fas fa-edit"></i>' +
                            '</button>' +
                            '<button onclick="deleteSekolah(' + sekolah.id_sekolah + ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">' +
                            '<i class="fas fa-trash"></i>' +
                            '</button>' +
                            '</div>'
                        ];
                        window.table.row.add(row);
                    });
                }
                
                window.table.draw();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mencari data'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mencari data'
            });
        }
    });
}

// Refresh table
function refreshTable() {
    console.log('refreshTable called');
    // Force refresh table with null parameter to clear cache
    if (typeof window.table !== 'undefined') {
        window.table.ajax.reload(null, false);
        loadStatistics();
        
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Data berhasil diperbarui',
            timer: 1000,
            showConfirmButton: false
        });
    } else {
        console.error('Table is not defined');
    }
}

// Load statistics
function loadStatistics() {
    // Load total sekolah
    $.ajax({
        url: base_url + 'sekolah/get_total_sekolah',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#totalSekolah').text(response.data.total);
            }
        },
        error: function(xhr, status, error) {
            console.log("Statistics load error: ", error);
        }
    });
}