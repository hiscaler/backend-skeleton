<?php

namespace app\controllers;

use app\models\Constant;
use app\models\Member;
use Overtrue\Wechat\Auth;
use Yii;
use yii\helpers\StringHelper;
use yii\helpers\Url;

class WechatController extends Controller
{

    public function actionAuth($redirectUrl = null)
    {
        $webUser = Yii::$app->getUser();
        $db = Yii::$app->getDb();
        if ($webUser->isGuest) {
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
                        $columns = [
                            'member_id' => $member->id,
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
                    }
                } else {
                    $member = Member::find($memberId);
                }
                $webUser->login($member, 3600 * 24 * 30);
            }
        }

        if (empty($redirectUrl)) {
            $redirectUrl = Url::home();
        } else {
            $redirectUrl = StringHelper::base64UrlDecode($redirectUrl);
        }


        $this->redirect($redirectUrl);
    }

}
