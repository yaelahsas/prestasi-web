$(document).ready(function() {
    // Initialize DataTable
    let table = $('#usersTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "users/get_users_data",
            "type": "POST"
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
        "responsive": true,
        "columnDefs": [
            { "targets": "_all", "orderable": false }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    });

    // Load statistics
    loadStatistics();

    // Form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        saveUser();
    });

    // Search functionality
    $('#searchBtn').on('click', function() {
        const keyword = $('#searchInput').val();
        searchUsers(keyword);
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            const keyword = $(this).val();
            searchUsers(keyword);
        }
    });

    // Refresh button
    $('#refreshBtn').on('click', function() {
        refreshTable();
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
        $('#modalTitle').text('Edit Pengguna');
        $('#passwordRequired').hide();
        $('#password').removeAttr('required');
        
        // Load user data
        $.ajax({
            url: base_url + 'users/get_user_by_id/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#id_user').val(response.data.id_user);
                    $('#nama').val(response.data.nama);
                    $('#username').val(response.data.username);
                    $('#role').val(response.data.role);
                    $('#status').val(response.data.status);
                    $('#userModal').addClass('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data user'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memuat data'
                });
            }
        });
    } else {
        // Add mode
        $('#modalTitle').text('Tambah Pengguna');
        $('#passwordRequired').show();
        $('#password').attr('required', 'required');
        $('#userModal').addClass('show');
    }
}

// Close modal
function closeModal() {
    $('#userModal').removeClass('show');
    resetForm();
}

// Reset form
function resetForm() {
    $('#userForm')[0].reset();
    $('#id_user').val('');
    $('.error-message').hide();
    $('.form-control').removeClass('border-red-500');
}

// Save user (add/edit)
function saveUser() {
    const formData = new FormData($('#userForm')[0]);
    
    // Show loading
    $('#saveBtn').addClass('loading');
    
    $.ajax({
        url: base_url + 'users/save_user',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
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
                }).then(function() {
                    closeModal();
                    table.ajax.reload();
                    loadStatistics();
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

// Edit user
function editUser(id) {
    openModal(id);
}

// Delete user
function deleteUser(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data user akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'users/delete_user/' + id,
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
                        }).then(function() {
                            table.ajax.reload();
                            loadStatistics();
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

// Toggle status
function toggleStatus(id, currentStatus) {
    const newStatus = currentStatus === 'aktif' ? 'nonaktif' : 'aktif';
    const statusText = newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `User akan di${statusText}!`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'users/toggle_status/' + id,
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
                        }).then(function() {
                            table.ajax.reload();
                            loadStatistics();
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
                        text: 'Terjadi kesalahan saat mengubah status'
                    });
                }
            });
        }
    });
}

// Search users
function searchUsers(keyword) {
    if (keyword.trim() === '') {
        table.ajax.reload();
        return;
    }
    
    $.ajax({
        url: base_url + 'users/search',
        type: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Clear and reload table with search results
                table.clear();
                
                if (response.data.length > 0) {
                    response.data.forEach(function(user) {
                        const row = [
                            user.id_user,
                            user.nama,
                            user.username,
                            '<span class="px-3 py-1 rounded-full text-xs font-medium ' + 
                            (user.role == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') + 
                            '">' + user.role.charAt(0).toUpperCase() + user.role.slice(1) + '</span>',
                            '<span class="px-3 py-1 rounded-full text-xs font-medium ' + 
                            (user.status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + 
                            '">' + user.status.charAt(0).toUpperCase() + user.status.slice(1) + '</span>',
                            new Date(user.created_at).toLocaleDateString('id-ID'),
                            '<div class="flex gap-1">' +
                            '<button onclick="editUser(' + user.id_user + ')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">' +
                            '<i class="fas fa-edit"></i>' +
                            '</button>' +
                            '<button onclick="deleteUser(' + user.id_user + ')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">' +
                            '<i class="fas fa-trash"></i>' +
                            '</button>' +
                            '<button onclick="toggleStatus(' + user.id_user + ', \'' + user.status + '\')" class="px-3 py-1 ' + 
                            (user.status == 'aktif' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600') + 
                            ' text-white rounded transition-colors">' +
                            '<i class="fas ' + (user.status == 'aktif' ? 'fa-eye-slash' : 'fa-eye') + '"></i>' +
                            '</button>' +
                            '</div>'
                        ];
                        table.row.add(row);
                    });
                }
                
                table.draw();
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
    table.ajax.reload();
    loadStatistics();
    
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Data berhasil diperbarui',
        timer: 1000,
        showConfirmButton: false
    });
}

// Load statistics
function loadStatistics() {
    // Load total users aktif
    $.ajax({
        url: base_url + 'users/get_total_users_aktif',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#usersAktif').text(response.data.total);
            }
        }
    });
    
    // Load total users nonaktif
    $.ajax({
        url: base_url + 'users/get_total_users_nonaktif',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#usersNonaktif').text(response.data.total);
            }
        }
    });
}