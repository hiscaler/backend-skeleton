<?php

namespace app\modules\api\controllers;

use app\models\Lookup;
use app\models\Meta;
use app\modules\api\extensions\BaseController;
use app\modules\api\models\Constant;
use app\modules\api\models\Member;
use BadMethodCallException;
use EasyWeChat\Foundation\Application;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 小程序认证处理
 *
 * @package app\modules\api\controllers
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxchecksessionobject
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class WxappController extends BaseController
{

    /* @var \EasyWeChat\Foundation\Application */
    private $wechatApplication;

    public function init()
    {
        parent::init();
        $options = isset(Yii::$app->params['wechat']) ? Yii::$app->params['wechat'] : null;
        if ($options === null || !is_array($options) || !isset($options['app_id']) || !isset($options['secret'])) {
            throw new InvalidConfigException('无效的微信参数配置（请在 params.php 中配置 wechat 项，并赋予 app_id 和 secret 正确值）。');
        }

        $this->wechatApplication = new Application(Yii::$app->params['wechat']);
    }

    /**
     * XML to array
     *
     * @param $xml
     * @return mixed
     */
    private function _xml2array($xml)
    {
        $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);

        return json_decode(json_encode($xml), true);
    }

    /**
     * 根据小程序提供的 key 获取用户的登录资料
     *
     * @return mixed
     * @throws InvalidParamException
     * @throws InvalidValueException
     * @throws \Exception
     */
    public function actionLogin()
    {
        $request = Yii::$app->getRequest();
        $code = $request->getQueryParam('code');
        if (empty($code)) {
            throw new InvalidParamException('code 值不能为空。');
        }
        $info = $request->getQueryParam('info');
        $userInfoIsValid = empty($info) ? false : true;
        if ($userInfoIsValid) {
            $info = json_decode($info, true);
            if ($info === null) {
                $userInfoIsValid = false; // Is not a valid json string
            } else {
                foreach (['avatarUrl', 'nickName', 'gender'] as $key) {
                    if (!isset($info[$key])) {
                        $userInfoIsValid = false;
                        break;
                    }
                }
            }
        }
        if (!$userInfoIsValid) {
            throw new InvalidParamException('info 值无效。');
        }

        $options = Yii::$app->params['wechat'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$options['app_id']}&secret={$options['secret']}&js_code={$code}&grant_type=authorization_code";
        $token = file_get_contents($url);
        $token && $token = json_decode($token, true);
        if (empty($token) || isset($token['errcode'])) {
            throw new InvalidValueException("微信接口访问出错（{$token['errmsg']}）。");
        }
        $openid = $token['openid'];
        if (empty($openid)) {
            throw new InvalidValueException('openid 无效。');
        }

        $now = time();
        $accessToken = 'wxapp.' . md5($openid . $token['session_key']) . '.' . ($now + 7200);
        $db = \Yii::$app->getDb();
        $memberId = $db->createCommand('SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryScalar();
        $nickname = preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $info["nickName"]);
        if ($memberId) {
            // 更新会员的相关信息
            $member = Member::findOne($memberId);
            $member->login_count += 1;
            $isNewRecord = false;
        } else {
            // 添加新会员
            $member = new Member();
            $member->setPassword(substr($openid, -10));
            $member->username = substr(md5($openid), -20);
            $member->nickname = $nickname ?: $member->username;
            $member->login_count = 1;
            $isNewRecord = true;
        }
        $nickname && $member->nickname = $nickname;
        $member->last_login_time = $now;
        $member->last_login_ip = ip2long(Yii::$app->getRequest()->getUserIP());
        $member->access_token = $accessToken;
        if ($member->validate() && $member->save()) {
            $wechatMember = [
                'openid' => $openid,
                'nickname' => $nickname,
                'sex' => $info['gender'],
                'country' => isset($info['country']) ? $info['country'] : null,
                'province' => isset($info['province']) ? $info['province'] : null,
                'city' => isset($info['city']) ? $info['city'] : null,
                'language' => isset($info['language']) ? $info['language'] : null,
                'headimgurl' => $info['avatarUrl'],
                'unionid' => isset($info['union_id']) ? $info['union_id'] : null,
            ];
            if ($isNewRecord) {
                $wechatMember['member_id'] = $member->id;
                $wechatMember['subscribe'] = Constant::BOOLEAN_FALSE;
                $wechatMember['subscribe_time'] = null;
                $db->createCommand()->insert('{{%wechat_member}}', $wechatMember)->execute();
            } else {
                $db->createCommand()->update('{{%wechat_member}}', $wechatMember, ['openid' => $openid])->execute();
            }

            return [
                'session' => $accessToken,
                'openid' => $openid,
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(400);

            return $member->errors;
        }
    }

    /**
     * 验证 session 值是否有效
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionCheckSession()
    {
        $session = Yii::$app->getRequest()->get("session");
        if (empty($session)) {
            throw new InvalidParamException("Session value can't empty.");
        }
        $accessToken = Yii::$app->getDb()->createCommand('SELECT [[access_token]] FROM {{%member}} WHERE [[access_token]] = :accessToken', [':accessToken' => $session])->queryScalar();
        if ($accessToken) {
            $t = explode('.', $accessToken);
            $timestamp = isset($t[2]) ? $t[2] : null;

            return [
                'valid' => $timestamp && $timestamp > time()
            ];
        } else {
            throw new NotFoundHttpException('Member data is not exists.');
        }
    }

    /**
     * 微信付款
     *
     * @return array|string
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionPayment()
    {
        $request = Yii::$app->getRequest();
        $body = trim($request->post('body', '')) ?: ' ';
        $outTradeNo = trim($request->post('out_trade_no'));
        $outTradeNo || $outTradeNo = date('YmdHis') . mt_rand(1000, 9999);
        $totalFee = (int) $request->post('total_fee');
        if (!$totalFee) {
            throw new InvalidParamException('无效的 total_fee 参数值。');
        }
        $openid = trim($request->post('openid'));
        if (!$openid) {
            throw new InvalidParamException('无效的 openid 参数值。');
        }
        $exist = \Yii::$app->getDb()->createCommand('SELECT COUNT(*) FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryScalar();
        if (!$exist) {
            throw new InvalidParamException("openid $openid 不存在。");
        }

        $attributes = [
            'trade_type' => 'JSAPI',
            'body' => $body,
            'out_trade_no' => $outTradeNo,
            'total_fee' => $totalFee,
            'openid' => $openid,
            'notify_url' => Url::toRoute(['wxapp/payment-notify'], true),
        ];
        $order = new \EasyWeChat\Payment\Order($attributes);
        $payment = $this->wechatApplication->payment;
        $response = $payment->prepare($order);
        if ($response->return_code == 'SUCCESS' && $response->result_code == 'SUCCESS') {
            $prepayId = $response->prepay_id;
            $config = $payment->configForJSSDKPayment($prepayId);

            // 创建商户订单
            $columns = $attributes;
            unset($columns['notify_url']);
            $wechatOptions = Yii::$app->params['wechat'];
            $columns['appid'] = $wechatOptions['app_id'];
            $columns['mch_id'] = $wechatOptions['payment']['merchant_id'];
            $columns['nonce_str'] = $config['nonceStr'];
            $columns['sign'] = $config['paySign'];
            $columns['sign_type'] = $config['signType'];
            $columns['time_start'] = time();
            $columns['status'] = \app\modules\admin\modules\wechat\models\Order::STATUS_PENDING;
            $columns['spbill_create_ip'] = Yii::$app->getRequest()->getUserIP();
            \Yii::$app->getDb()->createCommand()->insert('{{%wechat_order}}', $columns)->execute();

            return $config;
        } else {
            throw new BadRequestHttpException('支付失败。');
        }
    }

    /**
     * 支付回调通知
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws InvalidConfigException
     * @throws \EasyWeChat\Core\Exceptions\FaultException
     */
    public function actionPaymentNotify()
    {
        $response = $this->wechatApplication->payment->handleNotify(function ($notify, $successful) {
            if ($successful) {
                $db = \Yii::$app->getDb();
                $orderId = $db->createCommand('SELECT [[id]] FROM {{%wechat_order}} WHERE [[appid]] = :appId AND [[nonce_str]] = :nonceStr AND [[out_trade_no]] = :outTradeNo AND [[openid]] = :openid', [':appId' => $notify['appid'], ':nonceStr' => $notify['nonce_str'], ':outTradeNo' => $notify['out_trade_no'], ':openid' => $notify['openid']])->queryScalar();
                if ($orderId) {
                    $columns = [
                        'transaction_id' => $notify['transaction_id'],
                        'time_expire' => $notify['time_end'],
                    ];
                    if (isset($notify['trade_state'])) {
                        $columns['trade_state'] = $notify['trade_state'];
                        $columns['trade_state_desc'] = $notify['trade_state_desc'];
                    }
                    $db->createCommand()->update('{{%wechat_order}}', $columns, ['id' => $orderId])->execute();

                    return true;
                } else {
                    throw new NotFoundHttpException('ORDER NOT FOUND');
                }
            } else {
                return false;
            }
        });

        Yii::$app->getResponse()->format = Response::FORMAT_RAW;
        Yii::$app->getResponse()->content = ($response->getContent());
        Yii::$app->end();
    }

    /**
     * 退款
     *
     * @param $outTradeNo
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionRefund($outTradeNo)
    {
        $options = isset(Yii::$app->params['wechat']) ? Yii::$app->params['wechat'] : [];
        if (!isset($options['app_id'], $options['secret'], $options['mch_id'], $options['mch_key'])) {
            throw new InvalidConfigException('无效的微信参数配置（请在 params.php 中配置 wechat 项，并赋予 app_id、secret、mch_id、mch_key 正确值）。');
        }

        $db = \Yii::$app->getDb();
        $order = $db->createCommand('SELECT * FROM {{%wechat_order}} WHERE [[out_trade_no]] = :outTradeNo', [':outTradeNo' => $outTradeNo])->queryOne();
        if ($order) {
            if (!in_array($order['status'], [\app\modules\admin\modules\wechat\models\Order::STATUS_PENDING, \app\modules\admin\modules\wechat\models\Order::STATUS_CANCEL])) {
                throw new BadRequestHttpException('该订单不能退款。');
            } elseif ($order['total']) {
            }
        } else {
            throw new NotFoundHttpException("订单 $outTradeNo 不存在。");
        }
    }

    /**
     * 退款回调通知
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_16#menu1
     * @throws \yii\base\ExitException
     */
    public function actionRefundNotify()
    {
        $response = $this->wechatApplication->payment->handleRefundedNotify(function ($message, $fail) {
            if ($fail) {
                $fail('参数格式校验错误');
            } else {
                $db = \Yii::$app->getDb();
                $reqInfo = $this->reqInfo();
                $outTradeNo = $reqInfo['out_trade_no'];
                $orderId = $db->createCommand('SELECT [[id]] FROM {{%wechat_order}} WHERE [[out_trade_no]] = :outTradeNo', [':outTradeNo' => $outTradeNo])->queryScalar();
                if ($orderId) {
                    $columns = [
                        'refund_id' => $reqInfo['refund_id'],
                    ];
                    $db->createCommand()->update('{{%wechat_refund_order}}', $columns, ['id' => $orderId])->execute();

                    return true;
                } else {
                    throw new NotFoundHttpException("Order not found.");
                }
            }
        });

        Yii::$app->getResponse()->format = Response::FORMAT_RAW;
        Yii::$app->getResponse()->content = ($response->getContent());
        Yii::$app->end();

        $code = 'FAIL';
        $msg = 'DATA ERROR';
        $returnXml = <<<EOT
<xml>
  <return_code><![CDATA[{code}]]></return_code>
  <return_msg><![CDATA[{msg}]]></return_msg>
</xml>
EOT;
        $xml = <<<EOT
<xml>
<return_code>SUCCESS</return_code>
   <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
   <mch_id><![CDATA[10000100]]></mch_id>
   <nonce_str><![CDATA[TeqClE3i0mvn3DrK]]></nonce_str>
   <req_info><![CDATA[T87GAHG17TGAHG1TGHAHAHA1Y1CIOA9UGJH1GAHV871HAGAGQYQQPOOJMXNBCXBVNMNMAJAA]]></req_info>
</xml>
EOT;
        $xml = Yii::$app->getRequest()->getRawBody();
        $xml = file_get_contents("php://input");
        file_put_contents(__DIR__ . '/a.txt', $xml);
        $responseXml = $this->_xml2array($xml);
        if ($responseXml) {
            if ($responseXml['return_code'] == 'SUCCESS') {
                /**
                 * 解密步骤如下：
                 * （1）对加密串A做base64解码，得到加密串B
                 *（2）对商户key做md5，得到32位小写key* ( key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置 )
                 *（3）用key*对加密串B做AES-256-ECB解密（PKCS7Padding）
                 */
                $reqInfo = base64_decode($responseXml['req_info']);
                $key = md5('mF6VNVY7oit9j3SZWIBwMtLihfeRsFRa');
                $reqInfo = openssl_decrypt($reqInfo, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
                if ($reqInfo !== false) {
                }
            } else {
                $msg = $responseXml['return_msg'];
            }
        } else {
            throw new BadMethodCallException('无效的请求。');
        }
        Yii::$app->getResponse()->format = Response::FORMAT_RAW;
        echo strtr($returnXml, ['{code}' => $code, '{msg}' => $msg]);
        Yii::$app->end();
    }

    /**
     * 微信提现（企业付款）
     *
     * @param $openid
     * @param $amount
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionTransfer($openid, $amount)
    {
        $db = \Yii::$app->getDb();
        $wechatMember = $db->createCommand('SELECT * FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryOne();
        if ($wechatMember && $wechatMember['member_id'] == \Yii::$app->getUser()->getId()) {
            $money = (int) Meta::getValue('member', 'money', $wechatMember['member_id']);
            if ($money && $money >= $amount) {
                $artnerTradeNo = md5(uniqid(microtime()));
                $amount = $amount * 100;
                $merchantPayData = [
                    'partner_trade_no' => $artnerTradeNo,
                    'openid' => $openid,
                    'check_name' => 'NO_CHECK',
                    'amount' => $amount,
                    'desc' => '提现测试',
                    'spbill_create_ip' => Yii::$app->getRequest()->getUserIP(),
                ];
                $webrootPath = Yii::getAlias('@webroot');
                // @see https://stackoverflow.com/questions/24611640/curl-60-ssl-certificate-unable-to-get-local-issuer-certificate
                $this->wechatApplication['config']->set('payment.cert_path', $webrootPath . Lookup::getValue('custom.wxapp.cert.cert'));
                $this->wechatApplication['config']->set('payment.key_path', $webrootPath . Lookup::getValue('custom.wxapp.cert.key'));
                $merchantPay = $this->wechatApplication->merchant_pay;
                $response = $merchantPay->send($merchantPayData);
                //$db->createCommand()->insert('{{%wechat_pay_order}}', $columns)->execute();
            } else {
                throw new BadRequestHttpException('提现金额不能大于用户的剩余金额。');
            }
        } else {
            throw new NotFoundHttpException('Member is not exists.');
        }
    }

}