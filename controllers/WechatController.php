<?php

namespace app\controllers;

use app\models\Constant;
use app\models\Member;
use Yii;
use yii\helpers\Url;

class WechatController extends Controller
{

    public function actionAuth($redirectUrl = null)
    {
        $webUser = Yii::$app->getUser();
        $db = Yii::$app->getDb();
        if ($webUser->isGuest) {
            $wechatConfig = Yii::$app->params['wechat'];
            $auth = new Auth($wechatConfig['appid'], $wechatConfig['secret']);
            $user = $auth->authorize($to = null, 'snsapi_userinfo', 'STATE');
            if ($user) {
                $openid = $user->openid;
                $exists = $db->createCommand('SELECT [[id]] FROM {{%member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryScalar();
                if (!$exists) {
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
                        $db->createCommand()->insert('{{%member}}', $columns)->execute();
                    }
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
