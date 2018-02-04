<?php

namespace app\modules\api\controllers;

use app\models\Member;
use app\modules\api\extensions\BaseController;
use app\modules\api\models\Constant;
use Overtrue\Wechat\Auth;
use Overtrue\Wechat\Js;
use Yii;
use yii\base\InvalidCallException;

class WechatController extends BaseController
{

    public function actionAuth($redirectUri)
    {
        $db = Yii::$app->getDb();
        $wechatOptions = Yii::$app->params['wechat'];
        $auth = new Auth($wechatOptions['appid'], $wechatOptions['secret']);
        $user = $auth->authorize($to = null, 'snsapi_userinfo', 'STATE');
        if ($user) {
            $openid = $user->openid;
            $memberId = $db->createCommand('SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryScalar();
            if (!$memberId) {
                $member = new Member();
                $member->username = $user->nickname;
                $member->nickname = $user->nickname;
                $member->avatar = $user->headimgurl;
                $member->status = Member::STATUS_ACTIVE;
                if ($member->save()) {
                    $memberId = $member->id;
                    $columns = [
                        'member_id' => $memberId,
                        'subscribe' => Constant::BOOLEAN_TRUE,
                        'openid' => $openid,
                        'nickname' => $user->nickname,
                        'sex' => $user->sex,
                        'country' => $user->country,
                        'province' => $user->province,
                        'city' => $user->city,
                        'language' => $user->language,
                        'headimgurl' => $user->headimgurl,
                        'subscribe_time' => time(),
                    ];
                    $db->createCommand()->insert('{{%wechat_member}}', $columns)->execute();
                } else {
                    $memberId = null;
                }
            }
            if ($memberId) {
                $accessTokenExpire = isset(Yii::$app->params['user.accessTokenExpire']) ? (int) Yii::$app->params['user.accessTokenExpire'] : 7200;
                $accessTokenExpire = $accessTokenExpire ?: 7200;
                $accessToken = Yii::$app->getSecurity()->generateRandomString() . '.' . (time() + $accessTokenExpire);
                // Update user access_token value
                $db->createCommand()->update('{{%member}}', ['access_token' => $accessToken], ['id' => $memberId])->execute();
            } else {
                $accessToken = null;
            }

            if (strpos($redirectUri, '?') === false) {
                $redirectUri .= '?';
            } else {
                $redirectUri .= '&';
            }
            $redirectUri .= "accessToken={$accessToken}&showDialog=1";

            $this->redirect($redirectUri);
        } else {
            throw new InvalidCallException('拉取微信认证失败。');
        }
    }

    /**
     * JsSdk 配置值
     *
     * @param null $url
     * @param string $apis
     * @param bool $debug
     * @param bool $beta
     * @return array|string
     */
    public function actionJssdk($url = null, $apis = '', $debug = false, $beta = true)
    {
        $validApis = ['checkJsApi', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'];
        $apis = array_filter(explode(',', $apis), function ($api) use ($validApis) {
            return $api && in_array($api, $validApis);
        });
        empty($apis) && $apis = ['checkJsApi'];

        $wechatConfig = Yii::$app->params['wechat'];
        $js = new Js($wechatConfig['appid'], $wechatConfig['secret']);
        $url = $url ? urldecode($url) : Yii::$app->getRequest()->getHostInfo();
        $js->setUrl($url);
        $config = $js->config($apis, $debug, $beta);

        return $config;
    }
}
