<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (
    !$data ||
    !isset($data['gender'], $data['age'], $data['height'], $data['weight'], $data['duration'], $data['heartRate'], $data['bodyTemp'], $data['goal'])
) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// 1. Gọi Flask /predict
$requestData = [
    'gender' => $data['gender'],
    'age' => (float) $data['age'],
    'height' => (float) $data['height'],
    'weight' => (float) $data['weight'],
    'duration' => (float) $data['duration'],
    'heart_rate' => (float) $data['heartRate'],
    'body_temp' => (float) $data['bodyTemp']
];

$apiUrl = 'http://127.0.0.1:5000/predict';
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode(['success' => false, 'message' => 'ML service error: ' . $response]);
    exit;
}

$result = json_decode($response, true);
if (!$result || !isset($result['calories'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid response from ML service']);
    exit;
}

$calories = $result['calories'];

// 2. Gọi Flask /recommend_menu
$menuRequest = [
    'goal' => $data['goal'],
    'burned_calories' => $calories,
    'tolerance' => 0.1
];
$menuApiUrl = 'http://127.0.0.1:5000/recommend_menu';
$ch2 = curl_init($menuApiUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($menuRequest));
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
$menuResponse = curl_exec($ch2);
$menuHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

$menus = [];
if ($menuHttpCode === 200 && $menuResponse) {
    $menuResult = json_decode($menuResponse, true);
    if (isset($menuResult['menus'])) {
        // Đảm bảo trường tên món ăn là 'name' (nếu trả về là 'menu' thì đổi thành 'name')
        foreach ($menuResult['menus'] as $menu) {
            if (isset($menu['name'])) {
                $menus[] = $menu;
            } elseif (isset($menu['menu'])) {
                // Đổi key 'menu' thành 'name' để frontend dễ dùng
                $menu['name'] = $menu['menu'];
                unset($menu['menu']);
                $menus[] = $menu;
            }
        }
    }
}

// 3. Trả về cả calories và thực đơn
echo json_encode([
    'success' => true,
    'calories' => $calories,
    'menus' => $menus
]);
?>