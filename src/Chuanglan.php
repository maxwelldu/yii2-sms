<?php

namespace maxwelldu\sdk;

use yii\base\InvalidConfigException;
use yii\base\Component;
use Yii;

/**
 * Class Chuanglan
 *
 * 创蓝短信接口
 *
 * @package maxwelldu\sdk
 */
class Chuanglan extends Component
{
    const LOG_CATEGORY = 'sms.chuanglan';
    /* 发送短信接口URL */
    public $apiSendUrl = 'http://222.73.117.158/msg/HttpBatchSendSM';
    /* 余额查询接口URL */
    public $apiBalanceQueryUrl = 'http://222.73.117.158/msg/QueryBalance';
    /* 帐号 */
    public $apiAccount;
    /* 密码 */
    public $apiPassword;

    /* 错误内容 */
    private $error;

    public function getError()
    {
        $error = $this->error;
        $this->error = NULL;

        return $error;
    }

    public function hasError()
    {
        return $this->error != NULL;
    }

    protected function setError($message)
    {
        $this->error = $message;
        Yii::error($message, self::LOG_CATEGORY);
    }

    public function init()
    {
        if ($this->apiAccount === NULL)
        {
            throw new InvalidConfigException("The apiAccount property must be set.");
        }
        if ($this->apiPassword === NULL)
        {
            throw new InvalidConfigException("The apiPassword property must be set.");
        }
    }
    /**
     * 发送短信
     * @param string $mobile 手机号码,多个号码使用"," 分割,最多可以一次提交50000个手机号码
     * @param string $msg 短信内容
     * @param string $needstatus 是否需要状态报告,需要true, 不需要false
     * @param string $product 产品ID
     * @param string $extno 扩展码,最多可以扩展6位
     * @return mixed
     */
    public function sendSms($mobile, $msg, $needstatus = 'false', $product = '', $extno = '')
    {
        if (!is_array($mobile)) {
            $mobile = explode(',', $mobile);
        }

        if (empty($mobile)) {
            $this->setError('The mobile is must be set');

            return FALSE;
        }

        $mobile = array_filter($mobile, function($val) {
            $isPhoneNumber = 0 < preg_match('/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{4,12}$/', $val);
            if ($isPhoneNumber) {
                return TRUE;
            } else {
                $this->setError("The phone number is error: " . $val);
                return FALSE;
            }
        });

        $mobile = implode(',', $mobile);

        //创蓝接口参数
        $postArr = array (
            'account' => $this->apiAccount,
            'pswd' => $this->apiPassword,
            'msg' => $msg,
            'mobile' => $mobile,
            'needstatus' => $needstatus,
            'product' => $product,
            'extno' => $extno
        );

        $result = $this->curlPost( $this->apiSendUrl , $postArr);

        $state = $this->execResult($result)[1];
        $messages = [
            '0' => '提交成功',
            '101' => '无此用户',
            '102' => '密码错',
            '103' => '提交过快（提交速度超过流速限制）',
            '104' => '系统忙（因平台侧原因，暂时无法处理提交的短信）',
            '105' => '敏感短信（短信内容包含敏感词)）',
            '106' => '消息长度错（>536或<=0）',
            '107' => '包含错误的手机号码',
            '108' => '手机号码个数错（群发>50000或<=0;单发>200或<=0）',
            '109' => '无发送额度（该用户可用短信已使用完）',
            '110' => '不在发送时间内',
            '111' => '超出该帐户当月发送额度限制',
            '112' => '无此产品，用户没有订购该产品',
            '113' => 'extno格式错（非数字或者长度不对）',
            '115' => '自动审核驳回',
            '116' => '签名不合法，未带签名（用户必须带签名的前提下）',
            '117' => 'IP地址认证错,请求调用的IP地址不是系统登记的IP地址',
            '118' => '用户没有相应的发送权限',
            '119' => '用户已过期',
            '120' => '测试帐号限定验证码内容,非验证码内容不可提交',
        ];
        var_dump($messages[$state]);
        $this->setError($messages[$state] );

        $success = $state == 0;
        return $success;
    }
    /**
     * 查询额度
     * @return mixed
     */
    public function queryBalance() {
        //查询参数
        $postArr = array (
            'account' => $this->apiAccount,
            'pswd' => $this->apiPassword,
        );
        $result = $this->curlPost($this->apiBalanceQueryUrl, $postArr);
        return $result;
    }

    /**
     * 处理返回值
     * @param $result
     * @return array
     */
    public function execResult($result){
        $result=preg_split("/[,\r\n]/",$result);
        return $result;
    }

    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     */
    private function curlPost($url,$postFields){
        $postFields = http_build_query($postFields);
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }
}
