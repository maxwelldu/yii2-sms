# yii2-sms
Yii2 对应的短信扩展

[![Build Status](https://travis-ci.org/dcb9/yii2-yunpian.svg?branch=master)](https://travis-ci.org/dcb9/yii2-yunpian)[![Latest Stable Version](https://poser.pugx.org/dcb9/yii2-yunpian/v/stable.svg)](https://packagist.org/packages/dcb9/yii2-yunpian) [![Total Downloads](https://poser.pugx.org/dcb9/yii2-yunpian/downloads.svg)](https://packagist.org/packages/dcb9/yii2-yunpian) [![Latest Unstable Version](https://poser.pugx.org/dcb9/yii2-yunpian/v/unstable.svg)](https://packagist.org/packages/dcb9/yii2-yunpian) [![License](https://poser.pugx.org/dcb9/yii2-yunpian/license.svg)](https://packagist.org/packages/dcb9/yii2-yunpian)

## Install

add `maxwelldu/yii2-sms` to composer.json

```
$ composer update
```

OR

```
$ composer require maxwelldu/yii2-sms
```

## Configurtion

```php
\# file app/config/main.php
<?php

return [
    'components' => [
	   'sms' => [
            'class' => 'maxwelldu\\sdk\\Chuanglan',
            'apiAccount' => 'your chuanglan apiAccount',
            'apiPassword' => 'your chuanglan apiPassword',
            'apiSendUrl' => 'http://222.73.117.156/msg/HttpBatchSendSM',
            'apiBalanceQueryUrl' => 'http://222.73.117.156/msg/QueryBalance',
        ],
    ],
];
```

## Usage

```php
$phone = '18812345678';
// $phone = ['01234567890'];   # 可以为数组
// $phone = '12345678900,01234567890';  # 还可以号码与号码之间用空格隔开
$text ='sms content';
$sms = Yii::$app->sms;
if($sms->sendSms($phone, $text))
{
    //发送成功
} elseif ($sms->hasError()) {
    $error = $sms->getError()
    # "请求参数格式错误"
}
```

## Run phpunit
```
composer install
```

please update apiAccount and apiPassword and mobile in tests/ChuanglanTest.php file

```
./vendor/bin/phpunit
```

