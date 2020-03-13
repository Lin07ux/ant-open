支付宝身份认证
-------------------

支付宝身份认证需要如下三个步骤：

### 1、认证初始化

实例化一个认证请求对象，然后使用初始化的客户端调用该请求，即可完成认证初始化，并获得认证请求的唯一标识`certify_id`的值，用于后续的操作：

```php
use AntOpen\Request\AlipayUserCertifyOpenInitializeRequest;

$initRequest = new AlipayUserCertifyOpenInitializeRequest([
    'outer_order_no' => 'ZGYD201610252323000001234', // 自定义的商户订单号
    'biz_code' => 'FACE',
    'identity_param' => [
        'identity_type' => 'CERT_INFO',
        'cert_type' => 'IDENTITY_CARD',
        'cert_name' => '收委',              // 认证人姓名
        'cert_no' => '260104197909275964', // 认证人身份证号
    ],
    'merchant_config' => [
        'return_url' => 'https://example.com/alipay/back', // 认证后返回的页面地址
    ],
]);

$response = $client->request($request)->getResponse();

echo $response['certify_id'];
```

请求成功时，得到的响应数据如下

```php
[
    "code" => "10000",
    "msg": => "Success",
    "certify_id" =>  "2109b5e671aa3ff2eb4851816c65828f"
]
```

### 2、生成认证链接

得到认证唯一标识之后，即可使用该标识生成一个认证链接：

```php
use AntOpen\Request\AlipayUserCertifyOpenCertifyRequest;

$request = new AlipayUserCertifyOpenCertifyRequest(['certify_id' => '2109b5e671aa3ff2eb4851816c65828f']);

$url = $client->requestFromPage($request, 'get');

echo $url;
```

生成认证地址之后，使用支付宝打开该 URL 即可进行人脸识别认证。比如：

```
https://openapi.alipay.com/gateway.do?app_id=2018120462437271&biz_content=%7B%22certify_id%22%3A%2209916bea6d3a2983db962e76c06fb71b%22%7D&charset=utf-8&format=JSON&method=alipay.user.certify.open.certify&sign=dqGnkxJPik1xBH8oA9aHqzTl6LED%2F4w1zfHeh8HU3VExElfNFzfKWit5QepYogS81CDByv6SBvRZdneZpgElNSaSCDodtAkLGRqya7M57r76Rx1zNBlCIzqECnU6AY3CqYxTIIMer%2FyBwAlNDgPiNRI5FrV0f%2FeVC9RWMfKtq1nxZ4%2FZhWbnLGJqqgwy8Me8RO6%2FdAyKgkUrobpThZp2S34K0qsBUFbB3SzcPsgVrWjWBW9WE2FQbypEwISP%2FXjBY834DmIiy5fOM3lnlYM21Vq77hf0qwre%2FoDhGC0FgAzBLVSy9%2FglwkbAeRXJ%2B3KMLYYhl95wpExC0YsqzqqXyg%3D%3D&sign_type=RSA2&timestamp=2019-11-07+15%3A35%3A33&version=1.0
```

> 生成的认证 URL 由于附带了很多信息，一般会比较长，直接做成二维码的话，扫面经常会出错，建议生成一个短的链接，可以自动跳转到该认证 url，然后用短链接做成二维码，以供支付宝 APP 扫码访问。

### 3、查询认证结果

每个认证都是通过唯一的`certify_id`来确定的，可以随时使用`certify_id`来查询认证的结果：

```php
use AntOpen\Request\AlipayUserCertifyOpenQueryRequest;

$request = new AlipayUserCertifyOpenQueryRequest(['certify_id' => '2109b5e671aa3ff2eb4851816c65828f']);

$response = $client->request($request)->getResponse();

echo json_encode($response, JSON_UNESCAPED_UNICODE)['passed'];
```

认证通过时查询得到的结果如下：

```php
[
    "code" => "10000",
    "msg" => "Success",
    "passed" => "T"
]
```

认证失败结果：

```php
[
    "code" => "10000",
    "msg" => "Success",
    "passed" => "F"
]
```