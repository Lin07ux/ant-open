# ANT OPEN

这是一个用于实现蚂蚁金服开放平台相关服务的 PHP SDK。实现方式参考了蚂蚁金服开放平台的[文档](https://docs.alipay.com/zmxy/271)和官方提供的 [PHP SDK](https://docs.alipay.com/zmxy/54/103419#s1)。

> 目前仅实现了人脸识别认证相关的三个 API：`zhima.customer.certification.initialize`(认证初始化接口)、`zhima.customer.certification.certify`(芝麻认证开始认证接口)、`zhima.customer.certification.query`(芝麻认证查询接口)。

## 安装

```shell
composer require lin07ux/ant-open
```

## 使用

### 初始化客户端

在使用相关接口之前，需要先初始化一个`AlipayClient`对象，并设置相应的`appid`、支付宝公钥、商户私钥。

```php
use AntOpen\AlipayClient;

$client = new AlipayClient('appid');
$client->setAlipayRsaPublicKey('alipay_rsa_public_key_file', true)
    ->setCustomerRsaPrivateKey('customer_rsa_private_key_file', true);
```

### 认证初始化

实例化一个认证请求对象，然后使用初始化的客户端调用该请求，即可完成认证初始化，并获得`biz_no`值：

```php
use AntOpen\Request\ZhimaCustomerCertificationCertifyRequest;

$initRequest = new ZhimaCustomerCertificationInitializeRequest([
    'transaction_id' => 'ZGYD201610252323000001234',
    'product_code' => 'w1010100000000002978',
    'biz_code' => 'FACE',
    'identity_param' => [
        'identity_type' => 'CERT_INFO',
        'cert_type' => 'IDENTITY_CARD',
        'cert_name' => '收委',
        'cert_no' => '260104197909275964',
    ],
]);

$response = $client->request($request)->getResponse();

echo $response['biz_no'];
```

### 获取人脸识别认证链接

需要使用支付宝的人脸识别认证时，需要使用认证初始化过程中获取到的`biz_no`来生成相应的认证 URL，使用支付宝打开该 URL 即可进行人脸识别认证：

```php
use AntOpen\Request\ZhimaCustomerCertificationCertifyRequest;

$request = new ZhimaCustomerCertificationCertifyRequest(['biz_no' => 'ZM201812183000000808000675381813']);
// 设置查询完成后，自动跳转的网址(用于获取查询结果)
$request->setReturnUrl('http://alipay.huameiex.cn/alipay/notify');

$url = $client->requestFromPage($request, 'get');

echo $url;
```

> 生成的认证 URL 由于附带了很多信息，一般会比较长，直接做成二维码的话，扫面经常会出错，建议生成一个短的链接，可以自动跳转到该认证 url，然后用短链接做成二维码，以供支付宝 APP 扫码访问。

### 查询认证结果

每个认证都是通过唯一的`biz_no`来确定的，可以随时使用`biz_no`来查询认证的结果：

```php
use AntOpen\Request\ZhimaCustomerCertificationQueryRequest;

$request = new ZhimaCustomerCertificationCertifyRequest(['biz_no' => 'ZM201812183000000808000675381813']);

$response = $client->request($request)->getResponse();

echo json_encode($response, JSON_UNESCAPED_UNICODE);
```
