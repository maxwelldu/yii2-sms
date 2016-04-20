<?php

use maxwelldu\sdk\Chuanglan;

class ChuanglanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Chuanglan
     */
    protected $component;

    public function setUp()
    {
        $this->component = Yii::createObject([
           'class' => Chuanglan::className(),
            'apiAccount' => 'xxx',
            'apiPassword' => 'xxx',
            'apiSendUrl' => 'http://222.73.117.156/msg/HttpBatchSendSM',
            'apiBalanceQueryUrl' => 'http://222.73.117.156/msg/QueryBalance',
        ]);
    }

    public function testSendSms()
    {
        $this->assertTrue($this->component->sendSms('123456789', 'test in chuanglan'));
    }

    public function testError()
    {
        $sms = $this->component;
        if (!$sms->sendSms('123456789', 'test error') && $sms->hasError())
        {
            $error = $sms->getError();
            $this->assertTrue(isset($error));
        }
    }

    /**
     * @expectedException yii\base\InvalidConfigException
     */
    public function testInvalidConfigException()
    {
        Yii::createObject([
            'class' => Chuanglan::className(),
        ]);
    }
}
