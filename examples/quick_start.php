<?php
/**
 * 快速开始示例
 * 
 * 展示 SDK 的基本使用方法
 */

require_once __DIR__ . '/../OpenForAllClient.php';

// 1. 加载配置
$config = require __DIR__ . '/../config.php';

// 2. 创建客户端实例
$client = new \OpenForAll\OpenForAllClient($config);

echo "OpenForAll SDK 快速开始示例\n";
echo "========================================\n\n";

// ==================== 示例1：用户登录 ====================

echo "示例1：用户登录\n";
echo "----------------------------------------\n";

$result = $client->userLogin('username', 'password');

if ($result) {
    echo "✓ 登录成功\n";
    print_r($result['data']);
} else {
    echo "✗ 登录失败: " . $client->getLastError() . "\n";
}

echo "\n";

// ==================== 示例2：获取用户信息 ====================

echo "示例2：获取用户信息\n";
echo "----------------------------------------\n";

$result = $client->getUserInfo();

if ($result) {
    echo "✓ 获取成功\n";
    print_r($result['data']);
} else {
    echo "✗ 获取失败: " . $client->getLastError() . "\n";
}

echo "\n";

// ==================== 示例3：发送短信验证码 ====================

echo "示例3：发送短信验证码\n";
echo "----------------------------------------\n";

$result = $client->sendSms('13800138000', 'register');

if ($result) {
    echo "✓ 发送成功\n";
    print_r($result);
} else {
    echo "✗ 发送失败: " . $client->getLastError() . "\n";
}

echo "\n";

// ==================== 示例4：创建支付订单 ====================

echo "示例4：创建支付订单\n";
echo "----------------------------------------\n";

$result = $client->createPayment([
    'type' => 'package',
    'payment_method' => 'wechat',
    'package_id' => 1,
    'order_no' => 'ORD' . time(),
    'amount' => 99.00
]);

if ($result) {
    echo "✓ 创建成功\n";
    print_r($result['data']);
} else {
    echo "✗ 创建失败: " . $client->getLastError() . "\n";
}

echo "\n";

// ==================== 示例5：查询支付状态 ====================

echo "示例5：查询支付状态\n";
echo "----------------------------------------\n";

$result = $client->queryPayment('ORDER123456');

if ($result) {
    echo "✓ 查询成功\n";
    print_r($result['data']);
} else {
    echo "✗ 查询失败: " . $client->getLastError() . "\n";
}

echo "\n";

// ==================== 示例6：上传文件 ====================

echo "示例6：上传文件\n";
echo "----------------------------------------\n";

// 假设有一个图片文件
$filePath = '/path/to/image.jpg';

if (file_exists($filePath)) {
    $result = $client->uploadFile($filePath);
    
    if ($result) {
        echo "✓ 上传成功\n";
        print_r($result['data']);
    } else {
        echo "✗ 上传失败: " . $client->getLastError() . "\n";
    }
} else {
    echo "文件不存在，跳过上传示例\n";
}

echo "\n";

// ==================== 错误处理示例 ====================

echo "错误处理示例\n";
echo "----------------------------------------\n";

try {
    $result = $client->userLogin('invalid_user', 'wrong_password');
    
    if ($result === false) {
        echo "登录失败: " . $client->getLastError() . "\n";
    } else {
        echo "登录成功\n";
    }
} catch (Exception $e) {
    echo "发生异常: " . $e->getMessage() . "\n";
}

echo "\n";
echo "========================================\n";
echo "快速开始示例完成！\n";
echo "\n";
echo "更多示例请查看：\n";
echo "- examples/complete_flow.php - 完整业务流程\n";
echo "- examples/payment_example.php - 支付功能示例\n";
echo "- examples/user_example.php - 用户管理示例\n";
