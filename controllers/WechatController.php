<?php
/**
 * Created by PhpStorm.
 * User: hiscaler
 * Date: 2018-04-19
 * Time: 23:27
 */

namespace app\controllers;

use app\models\Member;
use EasyWeChat\Foundation\Application;
use Yii;
use yii\base\InvalidCallException;

class WechatController extends Controller
{

    public function actionAuth($redirectUrl = null, $redirect = 1)
    {
        $webUser = Yii::$app->getUser();
        $db = Yii::$app->getDb();
        if ($webUser->isGuest) {
            $application = new Application(Yii::$app->params['wechat']);

            if ($redirect) {
                $oauth = $application->oauth;
                $request = Yii::$app->getRequest();
                $url = $request->getHostInfo() . $request->getUrl();
                $url .= '&redirect=0';
                $oauth->setRedirectUrl($url);
                $url = $oauth->redirect()->getTargetUrl();
                header("Location: $url");
                Yii::$app->end();
            }
            $oauth = $application->oauth;
            $user = $oauth->user()->toArray();
            $userOriginal = $user['original'];
            if ($user) {
                $openid = $userOriginal['openid'];
                $memberId = $db->createCommand('SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryScalar();
                if (!$memberId) {
                    $member = new Member();
                    $member->setPassword($openid);
                    $member->username = $userOriginal['nickname'];
                    $member->nickname = $userOriginal['nickname'];
                    $member->avatar = $userOriginal['headimgurl'];
                    $member->status = Member::STATUS_ACTIVE;
                    if ($member->save()) {
                        $memberId = $member->id;
                        $columns = [
                            'member_id' => $memberId,
                            'subscribe' => Constant::BOOLEAN_TRUE,
                            'openid' => $openid,
                            'nickname' => $userOriginal['nickname'],
                            'sex' => $userOriginal['sex'],
                            'country' => $userOriginal['country'],
                            'province' => $userOriginal['province'],
                            'city' => $userOriginal['city'],
                            'language' => $userOriginal['language'],
                            'headimgurl' => $userOriginal['headimgurl'],
                            'subscribe_time' => time(),
                        ];
                        $db->createCommand()->insert('{{%wechat_member}}', $columns)->execute();
                    } else {
                        $memberId = null;
                    }
                }
                if ($memberId) {
                    $member = Member::findByOpenid($openid);
                    $webUser->login($member, 3600 * 24 * 30);
                    $accessTokenExpire = isset(Yii::$app->params['user.accessTokenExpire']) ? (int) Yii::$app->params['user.accessTokenExpire'] : 7200;
                    $accessTokenExpire = $accessTokenExpire ?: 7200;
                    $accessToken = Yii::$app->getSecurity()->generateRandomString() . '.' . (time() + $accessTokenExpire);
                    Yii::$app->getCache()->set('_access_token', $accessToken);
                    // Update user access_token value
                    $db->createCommand()->update('{{%member}}', ['access_token' => $accessToken], ['id' => $memberId])->execute();
                } else {
                    $accessToken = null;
                }

                $this->redirect(urldecode($redirectUrl));
            } else {
                throw new InvalidCallException('拉取微信认证失败。');
            }
        } else {
            $this->redirect(urldecode($redirectUrl));
        }
    }

}