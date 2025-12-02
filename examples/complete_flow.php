<?php
/**
 * 完整业务流程示例
 * 
 * 展示从用户注册到购买套餐的完整流程
 * 这是一个可直接运行的完整示例
 */

require_once __DIR__ . '/../OpenForAllClient.php';

// 加载配置
$config = require __DIR__ . '/../config.php';
$client = new \OpenForAll\OpenForAllClient($config);

// ==================== 第一步：用户注册 ====================

echo "=== 第一步：用户注册 ===\n\n";

// 1.1 发送短信验证码
$mobile = '13800138000';
echo "1.1 发送短信验证码到 {$mobile}...\n";
$result = $client->sendSms($mobile, 'register');

if ($result) {
    echo "✓ 验证码发送成功\n\n";
} else {
    die("✗ 验证码发送失败: " . $client->getLastError() . "\n");
}

// 1.2 用户注册（实际应用中，验证码由用户输入）
$username = 'testuser_' . time();
$password = 'Test123456';
$email = 'test@example.com';
$code = '123456'; // 实际应用中由用户输入

echo "1.2 用户注册...\n";
echo "用户名: {$username}\n";
echo "邮箱: {$email}\n";
echo "手机: {$mobile}\n";

$result = $client->userRegister($username, $password, $email, $mobile, $code);

if ($result) {
    echo "✓ 注册成功\n";
    echo "用户信息: " . json_encode($result['data'], JSON_UNESCAPED_UNICODE) . "\n\n";
} else {
    die("✗ 注册失败: " . $client->getLastError() . "\n");
}

// ==================== 第二步：用户登录 ====================

echo "=== 第二步：用户登录 ===\n\n";

// 2.1 账号密码登录
echo "2.1 使用账号密码登录...\n";
$result = $client->userLogin($username, $password);

if ($result) {
    echo "✓ 登录成功\n";
    $userInfo = $result['data']['userinfo'];
    echo "用户ID: {$userInfo['id']}\n";
    echo "用户名: {$userInfo['username']}\n";
    echo "昵称: {$userInfo['nickname']}\n\n";
} else {
    die("✗ 登录失败: " . $client->getLastError() . "\n");
}

// 2.2 获取用户信息
echo "2.2 获取用户详细信息...\n";
$result = $client->getUserInfo();

if ($result) {
    echo "✓ 获取成功\n";
    echo json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
} else {
    echo "✗ 获取失败: " . $client->getLastError() . "\n\n";
}

// ==================== 第三步：浏览套餐 ====================

echo "=== 第三步：浏览套餐 ===\n\n";

// 模拟套餐列表（实际应用中从API获取）
$packages = [
    ['id' => 1, 'name' => '基础版', 'price' => 99.00],
    ['id' => 2, 'name' => '专业版', 'price' => 299.00],
    ['id' => 3, 'name' => '企业版', 'price' => 999.00]
];

echo "可用套餐:\n";
foreach ($packages as $package) {
    echo "- {$package['name']}: ¥{$package['price']}/月\n";
}
echo "\n";

// 用户选择套餐
$selectedPackage = $packages[0]; // 选择基础版
echo "用户选择: {$selectedPackage['name']}\n\n";

// ==================== 第四步：创建订单 ====================

echo "=== 第四步：创建订单 ===\n\n";

$orderNo = 'ORD' . time();
echo "订单号: {$orderNo}\n";
echo "套餐: {$selectedPackage['name']}\n";
echo "金额: ¥{$selectedPackage['price']}\n\n";

// 4.1 创建微信支付订单
echo "4.1 创建微信支付订单...\n";
$result = $client->createPayment([
    'type' => 'package',
    'payment_method' => 'wechat',
    'package_id' => $selectedPackage['id'],
    'order_no' => $orderNo,
    'amount' => $selectedPackage['price']
]);

if ($result && isset($result['data']['code_url'])) {
    echo "✓ 订单创建成功\n";
    echo "支付二维码: {$result['data']['code_url']}\n";
    echo "请使用微信扫描二维码完成支付\n\n";
    
    // 生成二维码URL（使用第三方服务）
    $qrcodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($result['data']['code_url']);
    echo "二维码图片: {$qrcodeUrl}\n\n";
} else {
    echo "✗ 订单创建失败: " . $client->getLastError() . "\n\n";
}

// 4.2 或者使用余额支付
echo "4.2 使用余额支付...\n";
$result = $client->balancePayPackage([
    'order_no' => $orderNo,
    'package_id' => $selectedPackage['id'],
    'amount' => $selectedPackage['price']
]);

if ($result) {
    echo "✓ 支付成功\n";
    echo json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
} else {
    echo "✗ 支付失败: " . $client->getLastError() . "\n\n";
}

// ==================== 第五步：查询订单状态 ====================

echo "=== 第五步：查询订单状态 ===\n\n";

$result = $client->queryPayment($orderNo);

if ($result) {
    echo "✓ 查询成功\n";
    echo "订单状态: " . ($result['data']['payment_status'] ?? 'unknown') . "\n";
    echo json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
} else {
    echo "✗ 查询失败: " . $client->getLastError() . "\n\n";
}

// ==================== 完成 ====================

echo "=== 流程完成 ===\n\n";
echo "✓ 用户注册成功\n";
echo "✓ 用户登录成功\n";
echo "✓ 订单创建成功\n";
echo "✓ 支付流程完成\n\n";

echo "提示：这是一个演示示例，实际应用中需要：\n";
echo "1. 处理用户输入的验证码\n";
echo "2. 实现支付状态轮询或回调\n";
echo "3. 添加错误处理和重试机制\n";
echo "4. 保存用户登录状态（Session/Token）\n";
echo "5. 实现支付结果通知\n";
