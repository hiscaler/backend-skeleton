<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Constant;
use app\modules\api\models\Member;
use Overtrue\Wechat\Payment;
use Overtrue\Wechat\Payment\Business;
use Overtrue\Wechat\Payment\Order;
use Overtrue\Wechat\Payment\UnifiedOrder;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use yii\web\NotFoundHttpException;

/**
 * 小程序认证处理
 *
 * @package app\modules\api\controllers
 * @see https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxchecksessionobject
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class WxappController extends Controller
{

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

        $options = isset(Yii::$app->params['wechat']) ? Yii::$app->params['wechat'] : null;
        if (!is_array($options) || !isset($options['appid']) || !isset($options['secret'])) {
            throw new InvalidConfigException('无效的微信参数配置（请在 params.php 中配置 wechat 项，并赋予 appid 和 secret 正确值）。');
        }
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$options['appid']}&secret={$options['secret']}&js_code={$code}&grant_type=authorization_code";
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
            $member->avatar = $info["avatarUrl"];
            $member->nickname = $nickname;
            $member->last_login_time = $now;
            $member->login_count += 1;
            $member->access_token = $accessToken;
            $isNewRecord = false;
        } else {
            // 添加新会员
            $member = new Member();
            $member->setPassword(substr($openid, -10));
            $member->avatar = $info["avatarUrl"];
            $member->nickname = $nickname;
            $member->username = substr(md5($openid), -20);
            $member->last_login_time = $now;
            $member->login_count = 1;
            $member->access_token = $accessToken;
            $isNewRecord = true;
        }
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
     * @return array|string
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionPayment()
    {
        $options = isset(Yii::$app->params['wechat']) ? Yii::$app->params['wechat'] : [];
        if (!isset($options['appid'], $options['secret'], $options['mch_id'], $options['mch_key'])) {
            throw new InvalidConfigException('无效的微信公众号配置。');
        }
        $request = Yii::$app->getRequest();
        $body = trim($request->post('body', ''));
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
        $notifyUrl = $request->post('notify_url');
        if (!$notifyUrl || !filter_var($notifyUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidParamException('无效的 notify_url 参数值。');
        }
        $order = new Order();
        $order->body = $body;
        $order->out_trade_no = $outTradeNo;
        $order->total_fee = $totalFee;
        $order->openid = $openid;
        $order->notify_url = $notifyUrl;

        $unifiedOrder = new UnifiedOrder(new Business($options['appid'], $options['secret'], $options['mch_id'], $options['mch_key']), $order);
        $payment = new Payment($unifiedOrder);

        // 创建订单
        $columns = $order->toArray();
        unset($columns['notify_url']);
        $columns['create_time'] = time();
        $columns['status'] = \app\modules\admin\modules\wxpay\models\Order::STATUS_PENDING;
        \Yii::$app->getDb()->createCommand()->insert('{{%wx_order}}', $columns)->execute();

        return $payment->getConfigJssdk(false);
    }

}