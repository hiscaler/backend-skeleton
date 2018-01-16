<?php

namespace app\modules\api\controllers;

use app\models\Meta;
use app\modules\api\models\Constant;
use app\modules\api\models\Member;
use Yii;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;

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

        $options = Yii::$app->params['wechat'];
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$options['appid']}&secret={$options['secret']}&js_code={$code}&grant_type=authorization_code";
        $token = file_get_contents($url);
        $token = json_decode($token, true);
        if (empty($token) || isset($token['errcode'])) {
            throw new InvalidValueException('微信接口访问出错（' . $token['errmsg'] . '）。');
        }
        $openid = $token['openid'];
        if (empty($openid)) {
            throw new InvalidValueException('openid 无效。');
        }

        $sessionKey = md5($openid . $token['session_key']);
        $now = time();
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
            $isNewRecord = true;
        }
        if ($member->validate() && $member->save()) {
            $wechatMember = [
                'subscribe' => Constant::BOOLEAN_FALSE,
                'openid' => $openid,
                'nickname' => $nickname,
                'sex' => $info['gender'],
                'country' => isset($info['country']) ? $info['country'] : null,
                'province' => isset($info['province']) ? $info['province'] : null,
                'city' => isset($info['city']) ? $info['city'] : null,
                'language' => isset($info['language']) ? $info['language'] : null,
                'headimgurl' => $info['avatarUrl'],
                'subscribe_time' => null,
                'unionid' => isset($info['union_id']) ? $info['union_id'] : null,
            ];
            if ($isNewRecord) {
                $wechatMember['member_id'] = $member->id;
                $db->createCommand()->insert('{{%wechat_member}}', $wechatMember)->execute();
            } else {
                $db->createCommand()->update('{{%wechat_member}}', $wechatMember, ['openid' => $openid])->execute();
            }

            Meta::updateValue('wechat_member', 'wxapp_session_key', $member->id, $sessionKey);
            Meta::updateValue('wechat_member', 'wxapp_session_time', $member->id, $now + 86400);

            return [
                'session' => $sessionKey,
            ];
        } else {
            Yii::$app->getResponse()->setStatusCode(400);

            return $member->errors;
        }
    }

}