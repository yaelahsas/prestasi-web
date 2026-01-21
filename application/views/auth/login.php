<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Prestasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom animation for background */
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        /* Custom spinner */
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="animated-gradient min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-graduation-cap text-3xl text-purple-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Sistem Prestasi</h1>
            <p class="text-white/80">Sistem Rekap Jurnal Bimbingan Belajar</p>
        </div>
        
        <!-- Login Card -->
        <div class="glass rounded-2xl shadow-2xl p-8">
            <form id="loginForm" class="space-y-6">
                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition duration-200"
                        placeholder="Masukkan username"
                        required
                        autocomplete="username"
                    >
                </div>
                
                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition duration-200"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm text-purple-600 hover:text-purple-800 transition duration-200">
                        Lupa password?
                    </a>
                </div>
                
                <!-- Login Button -->
                <button 
                    type="submit" 
                    id="loginBtn"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition duration-200 flex items-center justify-center"
                >
                    <span id="btnText">Masuk</span>
                    <div id="btnSpinner" class="spinner ml-2 hidden"></div>
                </button>
            </form>
            
            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    &copy; <?php echo date('Y'); ?> Sistem Prestasi. All rights reserved.
                </p>
            </div>
        </div>
        
        <!-- Demo Account Info -->
        <div class="mt-6 text-center">
            <div class="glass rounded-lg p-4 inline-block">
                <p class="text-sm text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>Akun Demo:
                </p>
                <p class="text-xs text-gray-600">
                    Username: <span class="font-mono bg-gray-200 px-2 py-1 rounded">admin</span> | 
                    Password: <span class="font-mono bg-gray-200 px-2 py-1 rounded">password</span>
                </p>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        var base_url = '<?php echo base_url(); ?>';
    </script>
    <script src="<?php echo base_url('assets/js/auth/login.js'); ?>"></script>
</body>
</html>