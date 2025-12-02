<?php

namespace OpenForAll\SDK;

use OpenForAll\SDK\Exceptions\ApiException;

/**
 * OpenForAll API 客户端
 */
class Client
{
    /**
     * @var string 应用ID
     */
    protected $appId;

    /**
     * @var string 应用密钥
     */
    protected $appSecret;

    /**
     * @var string API基础URL
     */
    protected $baseUrl;

    /**
     * @var int 请求超时时间（秒）
     */
    protected $timeout = 30;

    /**
     * @var int 失败重试次数
     */
    protected $retry = 3;

    /**
     * 构造函数
     *
     * @param array $config 配置参数
     */
    public function __construct(array $config = [])
    {
        $this->appId = $config['app_id'] ?? '';
        $this->appSecret = $config['app_secret'] ?? '';
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->timeout = $config['timeout'] ?? 30;
        $this->retry = $config['retry'] ?? 3;

        if (empty($this->appId) || empty($this->appSecret)) {
            throw new \InvalidArgumentException('app_id and app_secret are required');
        }
    }

    /**
     * GET 请求
     *
     * @param string $uri 请求URI
     * @param array $params 请求参数
     * @return array
     * @throws ApiException
     */
    public function get($uri, array $params = [])
    {
        return $this->request('GET', $uri, $params);
    }

    /**
     * POST 请求
     *
     * @param string $uri 请求URI
     * @param array $data 请求数据
     * @return array
     * @throws ApiException
     */
    public function post($uri, array $data = [])
    {
        return $this->request('POST', $uri, $data);
    }

    /**
     * PUT 请求
     *
     * @param string $uri 请求URI
     * @param array $data 请求数据
     * @return array
     * @throws ApiException
     */
    public function put($uri, array $data = [])
    {
        return $this->request('PUT', $uri, $data);
    }

    /**
     * DELETE 请求
     *
     * @param string $uri 请求URI
     * @param array $params 请求参数
     * @return array
     * @throws ApiException
     */
    public function delete($uri, array $params = [])
    {
        return $this->request('DELETE', $uri, $params);
    }

    /**
     * 发送HTTP请求
     *
     * @param string $method 请求方法
     * @param string $uri 请求URI
     * @param array $data 请求数据
     * @return array
     * @throws ApiException
     */
    protected function request($method, $uri, array $data = [])
    {
        $url = $this->baseUrl . '/' . ltrim($uri, '/');
        $headers = $this->buildHeaders();

        $attempt = 0;
        $lastError = null;

        while ($attempt < $this->retry) {
            try {
                $response = $this->sendRequest($method, $url, $data, $headers);
                return $this->parseResponse($response);
            } catch (ApiException $e) {
                $lastError = $e;
                $attempt++;
                
                if ($attempt < $this->retry) {
                    usleep(100000 * $attempt); // 递增延迟
                }
            }
        }

        throw $lastError;
    }

    /**
     * 发送实际的HTTP请求
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return string
     * @throws ApiException
     */
    protected function sendRequest($method, $url, $data, $headers)
    {
        $ch = curl_init();

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if (in_array($method, ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new ApiException('Request failed: ' . $error, 0);
        }

        if ($httpCode >= 400) {
            throw new ApiException('HTTP Error: ' . $httpCode, $httpCode);
        }

        return $response;
    }

    /**
     * 解析响应
     *
     * @param string $response
     * @return array
     * @throws ApiException
     */
    protected function parseResponse($response)
    {
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Invalid JSON response: ' . json_last_error_msg());
        }

        if (isset($data['code']) && $data['code'] != 1) {
            throw new ApiException(
                $data['msg'] ?? 'Unknown error',
                $data['code'] ?? 0
            );
        }

        return $data['data'] ?? $data;
    }

    /**
     * 构建请求头
     *
     * @return array
     */
    protected function buildHeaders()
    {
        $timestamp = time();
        $nonce = md5(uniqid());
        $signature = $this->generateSignature($timestamp, $nonce);

        return [
            'Content-Type: application/json',
            'X-App-Id: ' . $this->appId,
            'X-Timestamp: ' . $timestamp,
            'X-Nonce: ' . $nonce,
            'X-Signature: ' . $signature,
        ];
    }

    /**
     * 生成签名
     *
     * @param int $timestamp
     * @param string $nonce
     * @return string
     */
    protected function generateSignature($timestamp, $nonce)
    {
        $data = [
            'app_id' => $this->appId,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
        ];

        ksort($data);
        $string = http_build_query($data) . '&key=' . $this->appSecret;

        return md5($string);
    }
}
