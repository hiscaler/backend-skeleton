<?php

namespace app\models;

use app\business\SmsBusinessAbstract;
use app\helpers\Config;
use Overtrue\EasySms\Contracts\GatewayInterface;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\Exception;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use yadjet\validators\MobilePhoneNumberValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * 短信
 *
 * @package app\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class Sms extends Model
{

    const CACHE_PREFIX = '_SMS_MODEL_';

    /**
     * @var string 发送短信类型
     */
    public $type;

    /**
     * @var string 短信接收号码
     */
    public $mobile_phone;

    /**
     * @var string 发送内容
     */
    public $content;

    public function rules()
    {
        return [
            [['type', 'mobile_phone'], 'required'],
            ['type', 'string', 'min' => 1],
            ['type', 'match', 'pattern' => '/^[a-z]{1,19}[a-z]$/'], // 20 位长度字符串
            ['type', function ($attribute, $params) {
                $class = ArrayHelper::getValue(Config::get('sms', []), "private.business.{$this->type}");
                if (!$class || !class_exists($class)) {
                    $this->addError($attribute, '请在 sms.private.business 项中设置发送业务处理类。');
                }
            }],
            [['mobile_phone', 'content'], 'trim'],
            ['mobile_phone', MobilePhoneNumberValidator::class],
            ['mobile_phone', function ($attribute, $params) {
                if (($cache = Yii::$app->getCache()->get(self::CACHE_PREFIX . $this->mobile_phone)) !== false) {
                    if (($seconds = $cache['expired_datetime'] - time()) > 0) {
                        $msg = '';
                        if ($minutes = floor($seconds / 60)) {
                            $msg = "$minutes 分";
                        }
                        if ($s = $seconds % 60) {
                            $msg && $msg .= ' ';
                            $msg .= "$s 秒";
                        }
                        $this->addError($attribute, "短信内容尚未到期，{$msg}后方可重新发送。");
                    }
                }
            }],
            ['content', 'string', 'min' => 1, 'max' => 140],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 发送
     *
     * @return bool
     */
    public function send()
    {
        $smsConfig = Config::get('sms', []);
        $class = ArrayHelper::getValue($smsConfig, "private.business.{$this->type}");
        try {
            /* @var $business SmsBusinessAbstract */
            $business = call_user_func([new $class(), 'build']);
            $payload = $business->getPayload();
            $this->content = $payload->getContent();
            $sendMessage = [
                'content' => $this->content,
                'template' => function ($gateway) use ($smsConfig) {
                    /* @var $gateway GatewayInterface */
                    $name = $gateway->getName();

                    return ArrayHelper::getValue($smsConfig, "gateways.$name._templateId");
                },
                'data' => $payload->getData(),
            ];

            $easySms = new EasySms($smsConfig);
            try {
                $easySms->send($this->mobile_phone, $sendMessage);
                if ($payload->useCache()) {
                    Yii::$app->getCache()->set(self::CACHE_PREFIX . $this->mobile_phone, [
                        'value' => $payload->getCacheValue(),
                        'expired_datetime' => time() + $payload->getCacheDuration(),
                    ], $payload->getCacheDuration());
                }
            } catch (InvalidArgumentException $e) {
                $this->addError('mobile_phone', $e->getMessage());

                return false;
            } catch (NoGatewayAvailableException $e) {
                $error = $e->getMessage();
                foreach ($e->getExceptions() as $exception) {
                    /* @var $exception Exception */
                    if ($exception->getMessage()) {
                        $error = $exception->getMessage();
                        break;
                    }
                }
                $this->addError('mobile_phone', $error);

                return false;
            }
        } catch (\Exception $e) {
            $this->addError('mobile_phone', $e->getMessage());
            Yii::error($class . ':' . $e->getMessage(), 'member.business');

            return false;
        }

        return true;
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'type' => '短信类型',
            'mobile_phone' => '手机号码',
            'content' => '内容',
        ];
    }

}