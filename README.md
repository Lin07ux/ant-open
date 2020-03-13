# ANT OPEN

这是一个用于实现蚂蚁金服开放平台相关服务的 PHP SDK。实现方式参考了蚂蚁金服开放平台的[文档](https://docs.alipay.com/zmxy/271)和官方提供的 [PHP SDK](https://docs.alipay.com/zmxy/54/103419#s1)。

> 人脸识别认证由芝麻认证改为支付宝身份认证，相应的 API 为：`AlipayUserCertifyOpenInitializeRequest`(初始化)、`AlipayUserCertifyOpenCertifyRequest`(开始认证)、`AlipayUserCertifyOpenQueryRequest`(查询结果)，使用示例参见 [支付宝身份认证](./docs/alipay-user-certify.md)。

## 安装

```shell
composer require lin07ux/ant-open
```

## 一、使用

### 1.1 初始化客户端

在使用相关接口之前，需要先初始化一个`AlipayClient`对象，并设置相应的`appid`、支付宝公钥、商户私钥。

```php
use AntOpen\AlipayClient;

$client = new AlipayClient('appid');
$client->setAlipayRsaPublicKey('alipay_rsa_public_key_file', true)
    ->setCustomerRsaPrivateKey('customer_rsa_private_key_file', true);
```

### 1.2 发送请求

得到客户端之后即可构造不同用途的请求，以得到相应的响应结果。

比如，下面实例化一个认证初始化对象，并发送请求得到对应的请求标识：

```php
use AntOpen\Request\AlipayUserCertifyOpenInitializeRequest;

$initRequest = new AlipayUserCertifyOpenInitializeRequest([
    'outer_order_no' => 'ZGYD201610252323000001234',
    'biz_code' => 'FACE',
    'identity_param' => [
        'identity_type' => 'CERT_INFO',
        'cert_type' => 'IDENTITY_CARD',
        'cert_name' => '收委',
        'cert_no' => '260104197909275964',
    ],
    'merchant_config' => [
        'return_url' => 'https://example.com/alipay/back',
    ],
]);

$response = $client->request($request)->getResponse();

echo $response['certify_id'];
```
