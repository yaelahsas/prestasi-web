/**
 * JavaScript untuk halaman login
 * Menggunakan jQuery dan SweetAlert untuk notifikasi
 */

$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const eyeIcon = $('#eyeIcon');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Handle login form submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        const username = $('#username').val().trim();
        const password = $('#password').val();
        
        // Validasi form
        if (!username || !password) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Username dan password harus diisi',
                confirmButtonColor: '#8b5cf6'
            });
            return;
        }
        
        // Tampilkan loading state
        showLoading();
        
        // Kirim data login via AJAX
        $.ajax({
            url: base_url + 'auth/login',
            type: 'POST',
            data: {
                username: username,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                hideLoading();
                
                if (response.status) {
                    // Login berhasil
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        confirmButtonColor: '#8b5cf6'
                    }).then(function() {
                        // Redirect ke URL yang dikirim dari server
                        window.location.href = response.redirect_url || base_url + 'dashboard';
                    });
                } else {
                    // Login gagal
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                        confirmButtonColor: '#8b5cf6'
                    });
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 0) {
                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan pada server. Silakan hubungi administrator.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonColor: '#8b5cf6'
                });
            }
        });
    });
    
    // Fungsi untuk menampilkan loading state
    function showLoading() {
        $('#loginBtn').prop('disabled', true);
        $('#btnText').text('Memproses...');
        $('#btnSpinner').removeClass('hidden');
    }
    
    // Fungsi untuk menyembunyikan loading state
    function hideLoading() {
        $('#loginBtn').prop('disabled', false);
        $('#btnText').text('Masuk');
        $('#btnSpinner').addClass('hidden');
    }
    
    // Auto-focus ke field username saat halaman dimuat
    $('#username').focus();
    
    // Handle Enter key pada field password
    $('#password').keypress(function(e) {
        if (e.which === 13) {
            $('#loginForm').submit();
        }
    });
    
    // Handle forgot password link (placeholder)
    $('a[href="#"]').click(function(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Lupa Password',
            text: 'Silakan hubungi administrator untuk reset password Anda.',
            confirmButtonColor: '#8b5cf6'
        });
    });
    
    // Check for session timeout or logout message
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    
    if (message) {
        let title = 'Informasi';
        let icon = 'info';
        
        switch(message) {
            case 'logout':
                title = 'Logout Berhasil';
                icon = 'success';
                break;
            case 'session_expired':
                title = 'Sesi Berakhir';
                icon = 'warning';
                break;
            case 'access_denied':
                title = 'Akses Ditolak';
                icon = 'error';
                break;
        }
        
        Swal.fire({
            icon: icon,
            title: title,
            text: getMessageText(message),
            confirmButtonColor: '#8b5cf6'
        });
    }
    
    // Fungsi untuk mendapatkan pesan berdasarkan parameter
    function getMessageText(message) {
        const messages = {
            'logout': 'Anda telah berhasil keluar dari sistem.',
            'session_expired': 'Sesi Anda telah berakhir. Silakan login kembali.',
            'access_denied': 'Anda tidak memiliki akses ke halaman tersebut.'
        };
        
        return messages[message] || 'Terjadi kesalahan.';
    }
});