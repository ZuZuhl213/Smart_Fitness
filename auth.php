<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Register a new user
function registerUser($pdo, $username, $email, $password) {
    try {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if ($user['username'] === $username) {
                return ['success' => false, 'message' => 'Tên người dùng đã tồn tại'];
            } else {
                return ['success' => false, 'message' => 'Email đã tồn tại'];
            }
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        
        return ['success' => true, 'message' => 'Đăng ký thành công'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Lỗi đăng ký: ' . $e->getMessage()];
    }
}

// Login user
function loginUser($pdo, $username, $password) {
    try {
        // Find user by username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Tên người dùng không tồn tại'];
        }
        
        $user = $stmt->fetch();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Initialize history array if not exists
            if (!isset($_SESSION['history'])) {
                $_SESSION['history'] = [];
            }
            
            return ['success' => true, 'message' => 'Đăng nhập thành công'];
        } else {
            return ['success' => false, 'message' => 'Mật khẩu không chính xác'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Lỗi đăng nhập: ' . $e->getMessage()];
    }
}

// Logout user
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}

// Save workout to database
function saveWorkout($pdo, $userId, $workoutData) {
    try {
        $stmt = $pdo->prepare("INSERT INTO workout_history 
            (user_id, age, height, weight, duration, heart_rate, body_temp, calories, meal_suggestion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $userId,
            $workoutData['age'],
            $workoutData['height'],
            $workoutData['weight'],
            $workoutData['duration'],
            $workoutData['heartRate'],
            $workoutData['bodyTemp'],
            $workoutData['calories'],
            json_encode($workoutData['meal'])
        ]);
        
        return ['success' => true, 'message' => 'Lưu dữ liệu thành công'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Lỗi lưu dữ liệu: ' . $e->getMessage()];
    }
}

// Get user workout history
function getUserWorkouts($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM workout_history WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        
        $workouts = $stmt->fetchAll();
        
        // Convert meal_suggestion from JSON to array
        foreach ($workouts as &$workout) {
            $workout['meal'] = json_decode($workout['meal_suggestion'], true);
            $workout['time'] = $workout['created_at'];
            $workout['heartRate'] = $workout['heart_rate'];
            $workout['bodyTemp'] = $workout['body_temp'];
        }
        
        return $workouts;
    } catch (PDOException $e) {
        return [];
    }
}

// Get user stats
function getUserStats($pdo, $userId) {
    try {
        // Total workouts
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM workout_history WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalWorkouts = $stmt->fetch()['total'];
        
        // Total calories
        $stmt = $pdo->prepare("SELECT SUM(calories) as total FROM workout_history WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalCaloriesBurnt = $stmt->fetch()['total'] ?: 0;
        
        // Average calories
        $avgCaloriesPerWorkout = $totalWorkouts > 0 ? round($totalCaloriesBurnt / $totalWorkouts) : 0;
        
        // Last workout
        $stmt = $pdo->prepare("SELECT * FROM workout_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$userId]);
        $lastWorkout = $stmt->fetch();
        
        if ($lastWorkout) {
            $lastWorkout['meal'] = json_decode($lastWorkout['meal_suggestion'], true);
            $lastWorkout['time'] = $lastWorkout['created_at'];
            $lastWorkout['heartRate'] = $lastWorkout['heart_rate'];
            $lastWorkout['bodyTemp'] = $lastWorkout['body_temp'];
        }
        
        return [
            'totalWorkouts' => $totalWorkouts,
            'totalCaloriesBurnt' => $totalCaloriesBurnt,
            'avgCaloriesPerWorkout' => $avgCaloriesPerWorkout,
            'lastWorkout' => $lastWorkout
        ];
    } catch (PDOException $e) {
        return [
            'totalWorkouts' => 0,
            'totalCaloriesBurnt' => 0,
            'avgCaloriesPerWorkout' => 0,
            'lastWorkout' => null
        ];
    }
}
?>
