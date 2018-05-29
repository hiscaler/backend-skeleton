<?php
/**
 * Created by PhpStorm.
 * User: hiscaler
 * Date: 2018-04-19
 * Time: 23:27
 */

namespace app\controllers;

use app\models\Constant;
use app\models\Member;
use EasyWeChat\Foundation\Application;
use Yii;
use yii\base\InvalidCallException;

class WechatController extends Controller
{

    public function actionAuth($redirectUrl = null, $redirect = 1)
    {
        if ($redirectUrl) {
            $redirectUrl = urldecode($redirectUrl);
        } else {
            $redirectUrl = Yii::$app->getRequest()->getHostInfo();
        }
        $webUser = Yii::$app->getUser();
        $db = Yii::$app->getDb();
        if ($webUser->isGuest) {
            $application = new Application(Yii::$app->params['wechat']);

            if ($redirect) {
                $oauth = $application->oauth;
                $request = Yii::$app->getRequest();
                $url = $request->getHostInfo() . $request->getUrl();

                if (strpos($url, '?') === false) {
                    $url .= '?';
                } else {
                    $url .= '&';
                }
                $url .= 'redirect=0';
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
                    $nickname = preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $userOriginal["nickname"]);
                    $member = new Member();
                    $maxId = $db->createCommand('SELECT MAX[[id]] FROM {{%member}}')->queryScalar();
                    $member->username = sprintf('wx%08d', $maxId + 1);
                    $member->nickname = $nickname ?: $member->username;
                    $member->setPassword($member->username);
                    $member->avatar = $userOriginal['headimgurl'];
                    $member->status = Member::STATUS_ACTIVE;
                    if ($member->save()) {
                        $memberId = $member->id;
                        $columns = [
                            'member_id' => $memberId,
                            'subscribe' => Constant::BOOLEAN_TRUE,
                            'openid' => $openid,
                            'nickname' => $nickname,
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
                    // Update user access_token value
                    $db->createCommand()->update('{{%member}}', ['access_token' => $accessToken], ['id' => $memberId])->execute();
                    $this->redirect($redirectUrl);
                } else {
                    throw new InvalidCallException('微信登录失败。');
                }
            } else {
                throw new InvalidCallException('拉取微信认证失败。');
            }
        } else {
            $this->redirect($redirectUrl);
        }
    }

}