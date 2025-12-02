# OpenForAll 微信开发 SDK - PHP版

<div align="center">

![OpenForAll Logo](https://img.shields.io/badge/OpenForAll-微信开发SDK-blue)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.0-8892BF.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![GitHub Stars](https://img.shields.io/github/stars/openforall/wechat-sdk-php.svg)](https://github.com/openforall/wechat-sdk-php/stargazers)
[![GitHub Issues](https://img.shields.io/github/issues/openforall/wechat-sdk-php.svg)](https://github.com/openforall/wechat-sdk-php/issues)

**🚀 企业级微信API开发工具包 | 5分钟快速接入 | 开箱即用**

[快速开始](#快速开始) • [在线演示](https://openforall.liebianjun.com/sdk/php/demo/) • [完整文档](https://openforall.liebianjun.com/docs) • [API参考](#api文档)

</div>

---

## ✨ 核心特性

### 🎯 功能全面
- ✅ **微信支付** - 扫码支付、公众号支付、APP支付、H5支付
- ✅ **微信授权** - OAuth2.0授权登录、用户信息获取
- ✅ **微信红包** - 现金红包、裂变红包、企业付款
- ✅ **用户管理** - 注册、登录、资料管理、权限控制
- ✅ **支付宝支付** - PC支付、手机支付、扫码支付
- ✅ **短信验证** - 阿里云、腾讯云短信接口
- ✅ **邮件服务** - 验证码、通知邮件
- ✅ **文件上传** - 图片、文档、视频上传

### 🔒 安全可靠
- 🛡️ **金融级安全** - 签名验证、加密传输、防重放攻击
- 🔐 **权限控制** - 基于角色的访问控制(RBAC)
- 📝 **完整日志** - 操作日志、错误日志、审计日志
- ⚡ **高性能** - 连接池、缓存优化、异步处理

### 📦 开箱即用
- 🚀 **5分钟接入** - 简单配置即可使用
- 📖 **详细文档** - 完整的API文档和示例代码
- 🎨 **在线演示** - 可视化功能演示和测试工具
- 🔧 **易于扩展** - 模块化设计，支持自定义扩展

---

## 📋 目录

- [系统要求](#系统要求)
- [快速开始](#快速开始)
- [安装方式](#安装方式)
- [配置说明](#配置说明)
- [使用示例](#使用示例)
- [API文档](#api文档)
- [在线演示](#在线演示)
- [常见问题](#常见问题)
- [贡献指南](#贡献指南)
- [技术支持](#技术支持)
- [开源协议](#开源协议)

---

## 🔧 系统要求

- **PHP** >= 7.0
- **ext-curl** - HTTP请求支持
- **ext-json** - JSON数据处理
- **ext-openssl** - 加密和签名支持

---

## 🚀 快速开始

### 方式一：Composer安装（推荐）

```bash
composer require openforall/wechat-sdk
```

### 方式二：手动下载

```bash
git clone https://github.com/openforall/wechat-sdk-php.git
cd wechat-sdk-php
```

### 基础使用

```php
<?php
require_once 'vendor/autoload.php';

use OpenForAll\OpenForAllClient;

// 1. 配置初始化
$config = [
    'api_key' => 'your_api_key',
    'api_secret' => 'your_api_secret',
    'api_url' => 'https://openforall.liebianjun.com'
];

// 2. 创建客户端
$client = new OpenForAllClient($config);

// 3. 用户登录
$result = $client->userLogin('username', 'password');

if ($result) {
    echo "登录成功！Token: " . $result['data']['token'];
} else {
    echo "登录失败: " . $client->getLastError();
}
```

---

## 📦 安装方式

### Composer安装（推荐）

在项目根目录执行：

```bash
composer require openforall/wechat-sdk
```

在代码中引入：

```php
require_once 'vendor/autoload.php';
```

### 手动安装

1. **下载SDK**
```bash
git clone https://github.com/openforall/wechat-sdk-php.git
```

2. **引入文件**
```php
require_once '/path/to/sdk/OpenForAllClient.php';
```

3. **配置应用**
```bash
cp config.example.php config.php
# 编辑 config.php 填写您的应用信息
```

---

## ⚙️ 配置说明

### 基础配置

创建 `config.php` 文件：

```php
<?php
return [
    // API密钥（必填）
    'api_key' => 'your_api_key_here',
    
    // API密钥（必填）
    'api_secret' => 'your_api_secret_here',
    
    // API地址（必填）
    'api_url' => 'https://your-domain.com',
    
    // 调试模式（可选）
    'debug' => false,
    
    // 超时时间（可选，秒）
    'timeout' => 30,
    
    // 日志路径（可选）
    'log_path' => '/path/to/logs',
];
```

### 获取API密钥

1. 访问 [OpenForAll平台](https://openforall.liebianjun.com)
2. 注册并登录账号
3. 进入"应用管理"页面
4. 创建应用并获取 `API Key` 和 `API Secret`

### 环境变量配置（推荐）

```php
<?php
return [
    'api_key' => getenv('OPENFORALL_API_KEY'),
    'api_secret' => getenv('OPENFORALL_API_SECRET'),
    'api_url' => getenv('OPENFORALL_API_URL'),
];
```

---

## 💡 使用示例

### 用户注册与登录

```php
<?php
// 1. 发送短信验证码
$result = $client->sendSms('13800138000', 'register');

// 2. 用户注册
$result = $client->userRegister(
    'testuser',           // 用户名
    'password123',        // 密码
    'test@example.com',   // 邮箱
    '13800138000',        // 手机号
    '123456'              // 验证码
);

// 3. 用户登录
$result = $client->userLogin('testuser', 'password123');

if ($result) {
    $token = $result['data']['token'];
    $userInfo = $result['data']['userinfo'];
    
    // 保存token用于后续请求
    $_SESSION['token'] = $token;
}

// 4. 手机验证码登录
$result = $client->userMobileLogin('13800138000', '123456');
```

### 微信支付

```php
<?php
// 1. 创建支付订单
$result = $client->createPayment([
    'type' => 'package',              // 订单类型
    'payment_method' => 'wechat',     // 支付方式
    'package_id' => 1,                // 套餐ID
    'order_no' => 'ORD' . time(),     // 订单号
    'amount' => 99.00                 // 金额
]);

if ($result) {
    $billId = $result['data']['bill_id'];
    
    // 2. 发起微信扫码支付
    $payResult = $client->wechatPay($billId, 'NATIVE');
    
    if ($payResult) {
        $qrcodeUrl = $payResult['data']['code_url'];
        echo "请扫码支付: " . $qrcodeUrl;
    }
}

// 3. 查询支付状态
$result = $client->queryPayment('ORD1234567890');

if ($result && $result['data']['status'] == 'paid') {
    echo "支付成功！";
}
```

### 支付宝支付

```php
<?php
// 1. 创建支付订单
$result = $client->createPayment([
    'type' => 'package',
    'payment_method' => 'alipay',
    'package_id' => 1,
    'order_no' => 'ORD' . time(),
    'amount' => 99.00
]);

// 2. 发起支付宝支付
$payResult = $client->alipay($result['data']['bill_id']);

if ($payResult) {
    // 跳转到支付宝支付页面
    header('Location: ' . $payResult['data']['pay_url']);
}
```

### 余额支付

```php
<?php
// 余额支付套餐
$result = $client->balancePayPackage([
    'order_no' => 'ORD' . time(),
    'package_id' => 1,
    'amount' => 99.00,
    'coupon_id' => 0,
    'discount_amount' => 0
]);

if ($result) {
    echo "支付成功！";
}
```

### 用户信息管理

```php
<?php
// 获取用户信息
$result = $client->getUserInfo();

if ($result) {
    $userInfo = $result['data'];
    echo "用户名: " . $userInfo['username'];
    echo "余额: " . $userInfo['money'];
}

// 修改用户资料
$result = $client->updateProfile([
    'nickname' => '新昵称',
    'bio' => '个人简介',
    'avatar' => 'https://example.com/avatar.jpg'
]);

// 修改邮箱
$result = $client->changeEmail('newemail@example.com', '123456');

// 修改手机号
$result = $client->changeMobile('13900139000', '123456');

// 重置密码
$result = $client->resetPassword('13800138000', '123456', 'newpassword');
```

### 文件上传

```php
<?php
// 上传图片
$result = $client->uploadFile('/path/to/image.jpg');

if ($result) {
    $imageUrl = $result['data']['url'];
    echo "上传成功: " . $imageUrl;
}
```

### 短信验证码

```php
<?php
// 发送短信验证码
$result = $client->sendSms('13800138000', 'register');

// 验证短信验证码
$result = $client->verifySms('13800138000', '123456', 'register');

if ($result) {
    echo "验证成功！";
}
```

### 邮件验证码

```php
<?php
// 发送邮件验证码
$result = $client->sendEmail('test@example.com', 'register');

// 验证邮件验证码
$result = $client->verifyEmail('test@example.com', '123456', 'register');
```

---

## 📚 API文档

### 用户接口

| 方法 | 说明 | 参数 |
|------|------|------|
| `userRegister()` | 用户注册 | username, password, email, mobile, code |
| `userLogin()` | 账号登录 | account, password |
| `userMobileLogin()` | 手机验证码登录 | mobile, captcha |
| `getUserInfo()` | 获取用户信息 | - |
| `updateProfile()` | 修改用户资料 | array $data |
| `changeEmail()` | 修改邮箱 | email, code |
| `changeMobile()` | 修改手机号 | mobile, code |
| `resetPassword()` | 重置密码 | mobile, code, newpassword |
| `userLogout()` | 退出登录 | - |

### 支付接口

| 方法 | 说明 | 参数 |
|------|------|------|
| `createPayment()` | 创建支付订单 | array $data |
| `wechatPay()` | 微信支付 | billId, tradeType, openid |
| `alipay()` | 支付宝支付 | billId |
| `balancePay()` | 余额支付 | billId, requestId |
| `balancePayPackage()` | 余额支付套餐 | array $data |
| `queryPayment()` | 查询支付状态 | orderNo |
| `cancelPayment()` | 取消支付 | orderNo |

### 短信接口

| 方法 | 说明 | 参数 |
|------|------|------|
| `sendSms()` | 发送短信验证码 | mobile, event |
| `verifySms()` | 验证短信验证码 | mobile, code, event |

### 邮件接口

| 方法 | 说明 | 参数 |
|------|------|------|
| `sendEmail()` | 发送邮件验证码 | email, event |
| `verifyEmail()` | 验证邮件验证码 | email, code, event |

### 公共接口

| 方法 | 说明 | 参数 |
|------|------|------|
| `init()` | 初始化配置 | version, lng, lat |
| `uploadFile()` | 上传文件 | filePath |
| `getCaptcha()` | 获取验证码 | id |

### 完整API文档

访问 [完整API文档](https://openforall.liebianjun.com/docs) 查看详细的接口说明和参数定义。

---

## 🎨 在线演示

我们提供了完整的在线演示，您可以直接体验所有功能：

### 演示地址

🌐 **主页**: https://openforall.liebianjun.com/sdk/php/demo/

### 演示功能

- 📱 **用户管理演示** - 注册、登录、资料管理
- 💳 **支付功能演示** - 微信支付、支付宝支付、余额支付
- 🔧 **API测试工具** - 在线测试所有API接口
- 📊 **完整流程演示** - 从注册到支付的完整业务流程

### 测试账号

```
API Key: app_cBag5h0NECyvu4wo
API Secret: rVyaIXUPtH7KiqcMDjEsY0lm5ohwWZbu
API URL: https://openforall.liebianjun.com
```

---

## 🔍 错误处理

所有API调用失败时返回 `false`，可以通过 `getLastError()` 获取错误信息：

```php
<?php
$result = $client->userLogin('username', 'password');

if ($result === false) {
    $error = $client->getLastError();
    
    // 记录错误日志
    error_log("API Error: " . $error);
    
    // 返回友好提示
    echo "操作失败: " . $error;
} else {
    // 处理成功结果
    $data = $result['data'];
}
```

### 常见错误码

| 错误码 | 说明 | 解决方案 |
|--------|------|----------|
| 401 | 签名验证失败 | 检查API Key和Secret是否正确 |
| 403 | 权限不足 | 检查账号权限配置 |
| 404 | 接口不存在 | 检查API URL和接口路径 |
| 422 | 参数验证失败 | 检查请求参数是否完整 |
| 500 | 服务器错误 | 联系技术支持 |

---

## ❓ 常见问题

### 1. 如何获取API密钥？

访问 [OpenForAll平台](https://openforall.liebianjun.com)，注册账号后在"应用管理"页面创建应用，即可获取API Key和API Secret。

### 2. 签名验证失败怎么办？

- 检查API Key和API Secret是否正确
- 确保服务器时间准确（时间戳误差不能超过5分钟）
- 检查网络连接是否正常
- 查看错误日志获取详细信息

### 3. 支付回调如何处理？

SDK会自动处理支付回调，您只需要在配置文件中设置正确的回调地址。详见[支付回调文档](https://openforall.liebianjun.com/docs/payment-callback)。

### 4. 如何启用调试模式？

在配置文件中设置 `debug` 为 `true`：

```php
'debug' => true,
```

### 5. 支持哪些PHP版本？

SDK支持PHP 7.0及以上版本，推荐使用PHP 7.4或PHP 8.x。

### 6. 如何处理并发请求？

SDK内置了连接池和请求队列，可以自动处理并发请求。建议使用异步处理或消息队列处理大量并发。

---

## 🤝 贡献指南

我们欢迎所有形式的贡献！

### 如何贡献

1. **Fork** 本仓库
2. **创建** 特性分支 (`git checkout -b feature/AmazingFeature`)
3. **提交** 更改 (`git commit -m 'Add some AmazingFeature'`)
4. **推送** 到分支 (`git push origin feature/AmazingFeature`)
5. **提交** Pull Request

### 贡献类型

- 🐛 **Bug修复** - 修复已知问题
- ✨ **新功能** - 添加新的功能特性
- 📝 **文档** - 改进文档和示例
- 🎨 **代码优化** - 代码重构和性能优化
- 🧪 **测试** - 添加或改进测试用例

### 代码规范

- 遵循 [PSR-12](https://www.php-fig.org/psr/psr-12/) 编码规范
- 添加必要的注释和文档
- 编写单元测试
- 确保代码通过所有测试

---

## 📞 技术支持

### 获取帮助

- 📖 **文档中心**: https://openforall.liebianjun.com/docs
- 💬 **问题反馈**: [GitHub Issues](https://github.com/openforall/wechat-sdk-php/issues)
- 📧 **邮件支持**: support@openforall.com
- 💼 **商务合作**: business@openforall.com

### 社区交流

- 🐛 **Bug反馈**: [提交Issue](https://github.com/openforall/wechat-sdk-php/issues/new)
- 💡 **功能建议**: [功能请求](https://github.com/openforall/wechat-sdk-php/issues/new?labels=enhancement)
- 📚 **技术博客**: [CSDN](https://blog.csdn.net/openforall) | [掘金](https://juejin.cn/user/openforall)
- 🎓 **知乎专栏**: [微信开发实战](https://zhuanlan.zhihu.com/openforall)

---

## 🌟 相关项目

### 官方SDK

- 🐍 **Python SDK**: [openforall/wechat-sdk-python](https://github.com/openforall/wechat-sdk-python)
- 📗 **Node.js SDK**: [openforall/wechat-sdk-nodejs](https://github.com/openforall/wechat-sdk-nodejs)
- ☕ **Java SDK**: [openforall/wechat-sdk-java](https://github.com/openforall/wechat-sdk-java)

### 框架集成

- 🎯 **Laravel**: [openforall/laravel-wechat](https://github.com/openforall/laravel-wechat)
- 🚀 **ThinkPHP**: [openforall/thinkphp-wechat](https://github.com/openforall/thinkphp-wechat)
- 🔥 **Yii2**: [openforall/yii2-wechat](https://github.com/openforall/yii2-wechat)

---

## 📄 开源协议

本项目采用 [MIT License](LICENSE) 开源协议。

```
MIT License

Copyright (c) 2024 OpenForAll

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 📈 项目统计

![GitHub stars](https://img.shields.io/github/stars/openforall/wechat-sdk-php?style=social)
![GitHub forks](https://img.shields.io/github/forks/openforall/wechat-sdk-php?style=social)
![GitHub watchers](https://img.shields.io/github/watchers/openforall/wechat-sdk-php?style=social)

---

## 🎉 致谢

感谢所有为本项目做出贡献的开发者！

[![Contributors](https://contrib.rocks/image?repo=openforall/wechat-sdk-php)](https://github.com/openforall/wechat-sdk-php/graphs/contributors)

---

<div align="center">

**⭐ 如果这个项目对您有帮助，请给我们一个Star！⭐**

Made with ❤️ by [OpenForAll Team](https://openforall.liebianjun.com)

[官网](https://openforall.liebianjun.com) • [文档](https://openforall.liebianjun.com/docs) • [演示](https://openforall.liebianjun.com/sdk/php/demo/) • [博客](https://blog.openforall.com)

</div>
