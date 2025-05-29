<?php
require_once 'controller.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartFit Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Toast Notification -->
    <?php if ($formSubmitted): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <?php if ($modelError): ?>
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                <strong class="me-auto">Lỗi</strong>
                <?php else: ?>
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong class="me-auto">Thành công</strong>
                <?php endif; ?>
                <small>Vừa xong</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php if ($modelError): ?>
                <?= $modelErrorMessage ?>
                <?php else: ?>
                Đã tính toán thành công <?= $caloriesBurnt ?> calories và lưu vào lịch sử.
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="?tab=home">
                <i class="bi bi-fire me-2" style="font-size: 1.75rem;"></i>
                <span>SmartFit</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'home' ? 'active' : '' ?>" href="?tab=home">
                            <i class="bi bi-house-door me-1"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'calculator' ? 'active' : '' ?>" href="?tab=calculator">
                            <i class="bi bi-calculator me-1"></i> Tính toán
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'history' ? 'active' : '' ?>" href="?tab=history">
                            <i class="bi bi-clock-history me-1"></i> Lịch sử
                        </a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="bi bi-person-plus me-1"></i> Đăng ký
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4 flex-grow-1">
        <!-- Navigation Pills (Mobile Friendly) -->
        <div class="row justify-content-center mb-4 d-md-none">
            <div class="col-12">
                <ul class="nav nav-pills nav-justified">
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'home' ? 'active' : '' ?>" href="?tab=home">
                            <i class="bi bi-house-door me-md-2"></i>
                            <span>Trang chủ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'calculator' ? 'active' : '' ?>" href="?tab=calculator">
                            <i class="bi bi-calculator me-md-2"></i>
                            <span>Tính toán</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'history' ? 'active' : '' ?>" href="?tab=history">
                            <i class="bi bi-clock-history me-md-2"></i>
                            <span>Lịch sử</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <div class="tab-pane fade <?= $activeTab === 'home' ? 'show active' : '' ?>">
                <!-- Hero Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card" data-aos="fade-up">
                            <div class="card-body p-4 p-md-5 text-center">
                                <h1 class="display-5 fw-bold mb-4">Chào mừng đến với SmartFit</h1>
                                <p class="lead mb-4">Theo dõi hoạt động thể thao và nhận gợi ý dinh dưỡng phù hợp để đạt được mục tiêu sức khỏe của bạn.</p>
                                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                    <a href="?tab=calculator" class="btn btn-primary btn-lg px-4 gap-3">
                                        <i class="bi bi-calculator me-2"></i> Bắt đầu tính toán
                                    </a>
                                    <a href="?tab=history" class="btn btn-outline-primary btn-lg px-4">
                                        <i class="bi bi-clock-history me-2"></i> Xem lịch sử
                                    </a>
                                </div>
                                <?php if (!$isLoggedIn): ?>
                                <div class="alert alert-info mt-4" role="alert">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Mẹo:</strong> <a href="register.php" class="alert-link">Đăng ký</a> hoặc <a href="login.php" class="alert-link">đăng nhập</a> để lưu lịch sử tập luyện của bạn.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Section -->
                <?php if ($totalWorkouts > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="mb-3">Thống kê của bạn</h4>
                    </div>
                    <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-card bg-white text-primary">
                            <div class="stat-icon">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div class="stat-value"><?= $totalWorkouts ?></div>
                            <div class="stat-label">Tổng số buổi tập</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card bg-white text-success">
                            <div class="stat-icon">
                                <i class="bi bi-fire"></i>
                            </div>
                            <div class="stat-value"><?= $totalCaloriesBurnt ?></div>
                            <div class="stat-label">Tổng calories đã đốt</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-card bg-white text-info">
                            <div class="stat-icon">
                                <i class="bi bi-lightning-charge"></i>
                            </div>
                            <div class="stat-value"><?= $avgCaloriesPerWorkout ?></div>
                            <div class="stat-label">Calories trung bình/buổi tập</div>
                        </div>
                    </div>
                </div>
                
                <!-- Chart Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card" data-aos="fade-up" data-aos-delay="400">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Lịch sử calories</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="caloriesChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Additional Info Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card" data-aos="fade-up" data-aos-delay="100">
                            <div class="card-body p-4">
                                <h4 class="mb-3 text-center">Về SmartFit</h4>
                                <p class="mb-0">SmartFit sử dụng công nghệ AI tiên tiến để giúp bạn theo dõi hoạt động thể thao và nhận gợi ý dinh dưỡng phù hợp. Hệ thống của chúng tôi phân tích dữ liệu từ các thông số cá nhân và hoạt động của bạn để đưa ra kết quả chính xác nhất.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calculator Tab -->
            <div class="tab-pane fade <?= $activeTab === 'calculator' ? 'show active' : '' ?>">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card" data-aos="fade-up">
                            <div class="card-header d-flex align-items-center">
                                <i class="bi bi-calculator text-primary me-2"></i>
                                <h5 class="mb-0">Công cụ tính Calories & Gợi ý bữa ăn</h5>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST" class="mb-4">
                                    <input type="hidden" name="submit_calorie_form" value="1">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <select class="form-select" id="gender" name="gender" required>
                                                    <option value="" disabled <?= !isset($_POST['gender']) ? 'selected' : '' ?>>Chọn giới tính</option>
                                                    <option value="male" <?= (($_POST['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Nam</option>
                                                    <option value="female" <?= (($_POST['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Nữ</option>
                                                </select>
                                                <label for="gender">Giới tính</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <select class="form-select" id="goal" name="goal" required>
                                                    <option value="" disabled <?= !isset($_POST['goal']) ? 'selected' : '' ?>>Chọn mục tiêu</option>
                                                    <option value="giảm_mỡ" <?= (($_POST['goal'] ?? '') === 'giảm_mỡ') ? 'selected' : '' ?>>Giảm mỡ</option>
                                                    <option value="giảm_cân" <?= (($_POST['goal'] ?? '') === 'giảm_cân') ? 'selected' : '' ?>>Giảm cân</option>
                                                    <option value="giữ_cân" <?= (($_POST['goal'] ?? '') === 'giữ_cân') ? 'selected' : '' ?>>Giữ cân</option>
                                                    <option value="tăng_cơ" <?= (($_POST['goal'] ?? '') === 'tăng_cơ') ? 'selected' : '' ?>>Tăng cơ</option>
                                                    <option value="tăng_cân" <?= (($_POST['goal'] ?? '') === 'tăng_cân') ? 'selected' : '' ?>>Tăng cân</option>
                                                </select>
                                                <label for="goal">Mục tiêu cá nhân</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="number" class="form-control" id="age" name="age" placeholder="Tuổi" required value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
                                                <label for="age">Tuổi</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="number" step="0.1" class="form-control" id="height" name="height" placeholder="Chiều cao (cm)" required value="<?= htmlspecialchars($_POST['height'] ?? '') ?>">
                                                <label for="height">Chiều cao (cm)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="number" step="0.1" class="form-control" id="weight" name="weight" placeholder="Cân nặng (kg)" required value="<?= htmlspecialchars($_POST['weight'] ?? '') ?>">
                                                <label for="weight">Cân nặng (kg)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="number" step="0.1" class="form-control" id="duration" name="duration" placeholder="Thời gian tập (phút)" required value="<?= htmlspecialchars($_POST['duration'] ?? '') ?>">
                                                <label for="duration">Thời gian tập (phút)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="number" step="0.1" class="form-control" id="heartRate" name="heartRate" placeholder="Nhịp tim (nhịp/phút)" required value="<?= htmlspecialchars($_POST['heartRate'] ?? '') ?>">
                                                <label for="heartRate">Nhịp tim (nhịp/phút)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="number" step="0.1" class="form-control" id="bodyTemp" name="bodyTemp" placeholder="Nhiệt độ cơ thể (°C)" required value="<?= htmlspecialchars($_POST['bodyTemp'] ?? '') ?>">
                                                <label for="bodyTemp">Nhiệt độ cơ thể (°C)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary py-3">
                                            <i class="bi bi-robot me-2"></i> Tính toán với AI
                                        </button>
                                    </div>
                                </form>

                                <?php if ($formSubmitted && !$modelError && $caloriesBurnt !== null): ?>
                                <div class="result-box fade-in" data-aos="fade-up">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="mb-3">Kết quả</h4>
                                            <div class="d-flex align-items-center mb-4">
                                                <i class="bi bi-fire text-primary me-3" style="font-size: 2.5rem;"></i>
                                                <div>
                                                    <div class="calories-display"><?= $caloriesBurnt ?></div>
                                                    <div class="text-muted">calories đã đốt cháy</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="mb-3">
                                                <i class="bi bi-apple text-primary me-2"></i>
                                                Gợi ý bữa ăn
                                            </h4>
                                            <?php if (!empty($mealSuggestion)): ?>
                                            <ul class="meal-list">
                                                <?php foreach($mealSuggestion as $meal): ?>
                                                    <li>
                                                        <strong><?= htmlspecialchars($meal['name']) ?></strong>
                                                        <ul class="mb-1">
                                                            <li>Protein: <?= $meal['protein'] ?>g</li>
                                                            <li>Carbs: <?= $meal['carbs'] ?>g</li>
                                                            <li>Fat: <?= $meal['fat'] ?>g</li>
                                                            <li>Calories: <?= $meal['calories'] ?> kcal</li>
                                                        </ul>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php else: ?>
                                            <div class="alert alert-warning">Không tìm thấy thực đơn phù hợp.</div>
                                            <?php endif; ?>
                                            <div class="alert alert-info mt-3" role="alert">
                                                <i class="bi bi-info-circle me-2"></i>
                                                Gợi ý bữa ăn được tính toán dựa trên lượng calories đã đốt cháy và mục tiêu cá nhân.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php elseif ($formSubmitted && $modelError): ?>
                                <div class="alert alert-danger mt-4" role="alert">
                                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i> Lỗi kết nối AI</h5>
                                    <p><?= $modelErrorMessage ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- ... giữ nguyên phần tips ... -->
                    </div>
                </div>
            </div>

            <!-- History Tab -->
            <div class="tab-pane fade <?= $activeTab === 'history' ? 'show active' : '' ?>">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card" data-aos="fade-up">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock-history text-primary me-2"></i>
                                    <h5 class="mb-0">Lịch sử hoạt động</h5>
                                </div>
                                <?php if (count($_SESSION['history'] ?? []) > 0): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="exportBtn">
                                    <i class="bi bi-download me-1"></i> Xuất dữ liệu
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!$isLoggedIn): ?>
                                <div class="alert alert-warning mb-4" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Lưu ý:</strong> Bạn chưa đăng nhập. Lịch sử của bạn sẽ bị mất khi đóng trình duyệt. 
                                    <a href="login.php" class="alert-link">Đăng nhập</a> hoặc 
                                    <a href="register.php" class="alert-link">đăng ký</a> để lưu lịch sử vĩnh viễn.
                                </div>
                                <?php endif; ?>
                                
                                <?php if (count($_SESSION['history'] ?? []) === 0): ?>
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <h4>Chưa có dữ liệu lịch sử</h4>
                                        <p class="text-muted mb-4">Bạn chưa có hoạt động nào được ghi lại. Hãy bắt đầu tính toán để theo dõi tiến độ của bạn.</p>
                                        <a href="?tab=calculator" class="btn btn-primary">
                                            <i class="bi bi-calculator me-2"></i> Bắt đầu tính toán
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <th>Tuổi</th>
                                                    <th>Chiều cao (cm)</th>
                                                    <th>Cân nặng (kg)</th>
                                                    <th>Thời gian tập (phút)</th>
                                                    <th>Nhịp tim</th>
                                                    <th>Nhiệt độ (°C)</th>
                                                    <th>Calories</th>
                                                    <th>Mục tiêu</th>
                                                    <th>Chi tiết</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_reverse($_SESSION['history']) as $index => $entry): ?>
                                                <tr>
                                                    <td><?= isset($entry['time']) ? htmlspecialchars($entry['time']) : 'N/A' ?></td>
                                                    <td><?= isset($entry['age']) ? htmlspecialchars($entry['age']) : 'N/A' ?></td>
                                                    <td><?= isset($entry['height']) ? htmlspecialchars($entry['height']) : 'N/A' ?></td>
                                                    <td><?= isset($entry['weight']) ? htmlspecialchars($entry['weight']) : 'N/A' ?></td>
                                                    <td><?= isset($entry['duration']) ? htmlspecialchars($entry['duration']) : 'N/A' ?></td>
                                                    <td><?= isset($entry['heartRate']) ? htmlspecialchars($entry['heartRate']) : 'N/A' ?></td>
                                                    <td><?= isset($entry['bodyTemp']) ? htmlspecialchars($entry['bodyTemp']) : 'N/A' ?></td>
                                                    <td class="fw-bold text-primary"><?= isset($entry['calories']) ? htmlspecialchars($entry['calories']) : 'N/A' ?></td>
                                                    <td><?= isset($entry['goal']) ? htmlspecialchars($entry['goal']) : 'N/A' ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal<?= $index ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Detail Modals -->
                                    <?php foreach (array_reverse($_SESSION['history']) as $index => $entry): ?>
                                    <div class="modal fade" id="detailModal<?= $index ?>" tabindex="-1" aria-labelledby="detailModalLabel<?= $index ?>" aria-hidden="true" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailModalLabel<?= $index ?>">Chi tiết hoạt động</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="d-flex align-items-center mb-4">
                                                        <i class="bi bi-fire text-primary me-3" style="font-size: 2.5rem;"></i>
                                                        <div>
                                                            <div class="calories-display"><?= $entry['calories'] ?></div>
                                                            <div class="text-muted">calories đã đốt cháy</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-4">
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Thời gian</div>
                                                                <div><?= htmlspecialchars($entry['time']) ?></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Tuổi</div>
                                                                <div><?= htmlspecialchars($entry['age']) ?> tuổi</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Chiều cao</div>
                                                                <div><?= htmlspecialchars($entry['height']) ?> cm</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Cân nặng</div>
                                                                <div><?= htmlspecialchars($entry['weight']) ?> kg</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Thời gian tập</div>
                                                                <div><?= htmlspecialchars($entry['duration']) ?> phút</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Nhịp tim</div>
                                                                <div><?= htmlspecialchars($entry['heartRate']) ?> nhịp/phút</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Nhiệt độ cơ thể</div>
                                                                <div><?= htmlspecialchars($entry['bodyTemp']) ?> °C</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="text-muted small">Mục tiêu</div>
                                                                <div><?= htmlspecialchars($entry['goal']) ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <h5 class="mb-3">Gợi ý bữa ăn</h5>
                                                    <?php if (!empty($entry['meal'])): ?>
                                                    <ul class="meal-list">
                                                        <?php foreach($entry['meal'] as $meal): ?>
                                                            <li>
                                                                <strong><?= htmlspecialchars($meal['name']) ?></strong>
                                                                <ul class="mb-1">
                                                                    <li>Protein: <?= $meal['protein'] ?>g</li>
                                                                    <li>Carbs: <?= $meal['carbs'] ?>g</li>
                                                                    <li>Fat: <?= $meal['fat'] ?>g</li>
                                                                    <li>Calories: <?= $meal['calories'] ?> kcal</li>
                                                                </ul>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <?php else: ?>
                                                    <div class="alert alert-warning">Không có thực đơn gợi ý.</div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 SmartFit. All rights reserved.</p>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>