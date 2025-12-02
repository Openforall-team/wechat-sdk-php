<?php

namespace OpenForAll;

/**
 * OpenForAll PHP SDK 客户端
 * 
 * 使用示例：
 * $client = new OpenForAllClient([
 *     'api_key' => 'your_api_key',
 *     'api_secret' => 'your_api_secret',
 *     'api_url' => 'https://your-domain.com'
 * ]);
 */
class OpenForAllClient
{
    /**
     * API密钥
     */
    private $apiKey;
    
    /**
     * API密钥
     */
    private $apiSecret;
    
    /**
     * API基础URL
     */
    private $apiUrl;
    
    /**
     * 最后一次错误信息
     */
    private $lastError;
    
    /**
     * 构造函数
     * 
     * @param array $config 配置参数
     */
    public function __construct($config = [])
    {
        $this->apiKey = $config['api_key'] ?? '';
        $this->apiSecret = $config['api_secret'] ?? '';
        $this->apiUrl = rtrim($config['api_url'] ?? '', '/');
        
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            throw new \Exception('API Key 和 API Secret 不能为空');
        }
    }
    
    /**
     * 生成签名
     * 
     * @param int $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @return string
     */
    private function generateSign($timestamp, $nonce)
    {
        return md5($this->apiKey . $timestamp . $nonce . $this->apiSecret);
    }
    
    /**
     * 发送HTTP请求
     * 
     * @param string $endpoint 接口路径
     * @param array $params 请求参数
     * @param string $method 请求方法
     * @return array|false
     */
    private function request($endpoint, $params = [], $method = 'POST')
    {
        $timestamp = time();
        $nonce = $this->generateNonce();
        $sign = $this->generateSign($timestamp, $nonce);
        
        // 添加认证参数
        $params['api_key'] = $this->apiKey;
        $params['timestamp'] = $timestamp;
        $params['nonce'] = $nonce;
        $params['sign'] = $sign;
        
        $url = $this->apiUrl . $endpoint;
        
        $ch = curl_init();
        
        if ($method === 'GET') {
            $url .= '?' . http_build_query($params);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            // 使用表单格式而不是 JSON
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            $this->lastError = 'CURL Error: ' . $error;
            return false;
        }
        
        if ($httpCode !== 200) {
            $this->lastError = 'HTTP Error: ' . $httpCode . ' - Response: ' . substr($response, 0, 200);
            return false;
        }
        
        $result = json_decode($response, true);
        
        if (!$result) {
            $this->lastError = 'Invalid JSON response: ' . substr($response, 0, 200);
            return false;
        }
        
        if (isset($result['code']) && $result['code'] != 1) {
            $this->lastError = $result['msg'] ?? 'Unknown error';
            return false;
        }
        
        return $result;
    }
    
    /**
     * 生成随机字符串
     * 
     * @param int $length 长度
     * @return string
     */
    private function generateNonce($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $nonce = '';
        for ($i = 0; $i < $length; $i++) {
            $nonce .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $nonce;
    }
    
    /**
     * 获取最后一次错误信息
     * 
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }
    
    // ==================== 用户接口 ====================
    
    /**
     * 用户登录
     * 
     * @param string $account 账号
     * @param string $password 密码
     * @return array|false
     */
    public function userLogin($account, $password)
    {
        return $this->request('/api/user/login', [
            'account' => $account,
            'password' => $password
        ]);
    }
    
    /**
     * 手机验证码登录
     * 
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     * @return array|false
     */
    public function userMobileLogin($mobile, $captcha)
    {
        return $this->request('/api/user/mobilelogin', [
            'mobile' => $mobile,
            'captcha' => $captcha
        ]);
    }
    
    /**
     * 用户注册
     * 
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 邮箱
     * @param string $mobile 手机号
     * @param string $code 验证码
     * @return array|false
     */
    public function userRegister($username, $password, $email, $mobile, $code)
    {
        return $this->request('/api/user/register', [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'mobile' => $mobile,
            'code' => $code
        ]);
    }
    
    /**
     * 用户退出登录
     * 
     * @return array|false
     */
    public function userLogout()
    {
        return $this->request('/api/user/logout');
    }
    
    /**
     * 获取用户信息
     * 
     * @return array|false
     */
    public function getUserInfo()
    {
        return $this->request('/api/user/index', [], 'GET');
    }
    
    /**
     * 修改用户资料
     * 
     * @param array $data 用户数据
     * @return array|false
     */
    public function updateProfile($data)
    {
        return $this->request('/api/user/profile', $data);
    }
    
    /**
     * 修改邮箱
     * 
     * @param string $email 邮箱
     * @param string $captcha 验证码
     * @return array|false
     */
    public function changeEmail($email, $captcha)
    {
        return $this->request('/api/user/changeemail', [
            'email' => $email,
            'captcha' => $captcha
        ]);
    }
    
    /**
     * 修改手机号
     * 
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     * @return array|false
     */
    public function changeMobile($mobile, $captcha)
    {
        return $this->request('/api/user/changemobile', [
            'mobile' => $mobile,
            'captcha' => $captcha
        ]);
    }
    
    /**
     * 重置密码
     * 
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     * @return array|false
     */
    public function resetPassword($mobile, $newpassword, $captcha)
    {
        return $this->request('/api/user/resetpwd', [
            'type' => 'mobile',
            'mobile' => $mobile,
            'newpassword' => $newpassword,
            'captcha' => $captcha
        ]);
    }
    
    // ==================== 支付接口 ====================
    
    /**
     * 创建支付订单
     * 
     * @param array $params 订单参数
     * @return array|false
     */
    public function createPayment($params)
    {
        return $this->request('/api/payment/create', $params);
    }
    
    /**
     * 微信支付
     * 
     * @param int $billId 账单ID
     * @param string $tradeType 支付类型
     * @param string $openid 用户openid
     * @return array|false
     */
    public function wechatPay($billId, $tradeType = 'JSAPI', $openid = '')
    {
        return $this->request('/api/payment/wechat', [
            'bill_id' => $billId,
            'trade_type' => $tradeType,
            'openid' => $openid
        ]);
    }
    
    /**
     * 支付宝支付
     * 
     * @param int $billId 账单ID
     * @return array|false
     */
    public function alipay($billId)
    {
        return $this->request('/api/payment/alipay', [
            'bill_id' => $billId
        ]);
    }
    
    /**
     * 余额支付
     * 
     * @param int $billId 账单ID
     * @param string $requestId 请求ID（幂等性）
     * @return array|false
     */
    public function balancePay($billId, $requestId = '')
    {
        if (empty($requestId)) {
            $requestId = uniqid('pay_', true);
        }
        
        return $this->request('/api/payment/balance', [
            'bill_id' => $billId,
            'request_id' => $requestId
        ]);
    }
    
    /**
     * 余额支付套餐
     * 
     * @param array $params 订单参数
     * @return array|false
     */
    public function balancePayPackage($params)
    {
        return $this->request('/api/payment/balancePayPackage', $params);
    }
    
    /**
     * 查询支付状态
     * 
     * @param string $orderNo 订单号
     * @return array|false
     */
    public function queryPayment($orderNo)
    {
        return $this->request('/api/payment/query', [
            'order_no' => $orderNo
        ], 'GET');
    }
    
    /**
     * 取消支付
     * 
     * @param string $orderNo 订单号
     * @return array|false
     */
    public function cancelPayment($orderNo)
    {
        return $this->request('/api/payment/cancel', [
            'order_no' => $orderNo
        ]);
    }
    
    // ==================== 公共接口 ====================
    
    /**
     * 初始化配置
     * 
     * @param string $version 版本号
     * @param string $lng 经度
     * @param string $lat 纬度
     * @return array|false
     */
    public function init($version = '1.0.0', $lng = '', $lat = '')
    {
        return $this->request('/api/common/init', [
            'version' => $version,
            'lng' => $lng,
            'lat' => $lat
        ], 'GET');
    }
    
    /**
     * 上传文件
     * 
     * @param string $filePath 文件路径
     * @return array|false
     */
    public function uploadFile($filePath)
    {
        if (!file_exists($filePath)) {
            $this->lastError = 'File not found';
            return false;
        }
        
        $url = $this->apiUrl . '/api/common/upload';
        
        $timestamp = time();
        $nonce = $this->generateNonce();
        $sign = $this->generateSign($timestamp, $nonce);
        
        $ch = curl_init();
        
        $postData = [
            'file' => new \CURLFile($filePath),
            'api_key' => $this->apiKey,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'sign' => $sign
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            $this->lastError = 'CURL Error: ' . $error;
            return false;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * 获取验证码
     * 
     * @param string $id 验证码标识
     * @return string 验证码图片URL
     */
    public function getCaptcha($id = '')
    {
        return $this->apiUrl . '/api/common/captcha' . ($id ? '?id=' . $id : '');
    }
    
    // ==================== 短信接口 ====================
    
    /**
     * 发送短信验证码
     * 
     * @param string $mobile 手机号
     * @param string $event 事件类型
     * @return array|false
     */
    public function sendSms($mobile, $event = 'register')
    {
        return $this->request('/api/sms/send', [
            'mobile' => $mobile,
            'event' => $event
        ]);
    }
    
    /**
     * 验证短信验证码
     * 
     * @param string $mobile 手机号
     * @param string $code 验证码
     * @param string $event 事件类型
     * @return array|false
     */
    public function verifySms($mobile, $code, $event = 'register')
    {
        return $this->request('/api/sms/check', [
            'mobile' => $mobile,
            'captcha' => $code,
            'event' => $event
        ]);
    }
    
    // ==================== 邮件接口 ====================
    
    /**
     * 发送邮件验证码
     * 
     * @param string $email 邮箱
     * @param string $event 事件类型
     * @return array|false
     */
    public function sendEmail($email, $event = 'register')
    {
        return $this->request('/api/ems/send', [
            'email' => $email,
            'event' => $event
        ]);
    }
    
    /**
     * 验证邮件验证码
     * 
     * @param string $email 邮箱
     * @param string $code 验证码
     * @param string $event 事件类型
     * @return array|false
     */
    public function verifyEmail($email, $code, $event = 'register')
    {
        return $this->request('/api/ems/check', [
            'email' => $email,
            'captcha' => $code,
            'event' => $event
        ]);
    }
}
