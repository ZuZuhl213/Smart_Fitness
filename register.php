<?php
require_once 'db_config.php';
require_once 'auth.php';

$message = '';
$messageType = '';

// If user is already logged in, redirect to index
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'Vui lòng điền đầy đủ thông tin';
        $messageType = 'danger';
    } elseif ($password !== $confirmPassword) {
        $message = 'Mật khẩu xác nhận không khớp';
        $messageType = 'danger';
    } elseif (strlen($password) < 6) {
        $message = 'Mật khẩu phải có ít nhất 6 ký tự';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Email không hợp lệ';
        $messageType = 'danger';
    } else {
        // Register user
        $result = registerUser($pdo, $username, $email, $password);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            
            // Redirect to login page after 2 seconds
            header("refresh:2;url=login.php");
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký - SmartFit</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-fire me-2" style="font-size: 1.75rem;"></i>
                <span>SmartFit</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card" data-aos="fade-up">
                    <div class="card-header d-flex align-items-center">
                        <i class="bi bi-person-plus text-primary me-2"></i>
                        <h5 class="mb-0">Đăng ký tài khoản</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên người dùng</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                                </div>
                                <div class="invalid-feedback">Vui lòng nhập tên người dùng</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                                <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="invalid-feedback">Mật khẩu xác nhận không khớp</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-3">
                                    <i class="bi bi-person-plus me-2"></i> Đăng ký
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">Đã có tài khoản? <a href="login.php" class="text-primary">Đăng nhập</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                        <i class="bi bi-fire text-primary me-2" style="font-size: 1.5rem;"></i>
                        <span class="fw-bold">SmartFit</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Theo dõi hoạt động thể thao và nhận gợi ý dinh dưỡng phù hợp.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="text-muted small mb-0">&copy; <?= date('Y') ?> SmartFit Dashboard. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animations
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true
            });
            
            // Form validation
            const forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    // Check if passwords match
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('confirm_password');
                    
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        confirmPassword.setCustomValidity('');
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
</body>
</html>
