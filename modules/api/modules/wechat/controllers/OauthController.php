<?php

namespace app\modules\api\modules\wechat\controllers;

use app\modules\api\models\Constant;
use app\modules\api\models\Member;
use Exception;
use yadjet\helpers\UrlHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

/**
 * OAuth 授权
 * Class OauthController
 *
 * @property \Overtrue\Socialite\Providers\WeChatProvider $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class OauthController extends BaseController
{

    /**
     * 授权回调类型
     */
    const CALLBACK_NORMAL_TYPE = 'normal'; // 正常登录
    const CALLBACK_OPEN_TYPE = 'open'; // 第三方登录

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->oauth;
    }

    /**
     * 拉起授权
     *
     * @param $url
     * @param null $type
     */
    public function actionRedirect($url, $type = null)
    {
        if ($url || $type) {
            $callbackUrl = $this->wxConfig['oauth']['callback'];
            $url && $callbackUrl = UrlHelper::addQueryParam($callbackUrl, "url", $url);
            $type && $callbackUrl = UrlHelper::addQueryParam($callbackUrl, "type", $type);
            if (strcmp($callbackUrl, $this->wxConfig['oauth']['callback']) !== 0) {
                $this->wxApplication['config']->set('oauth.callback', $callbackUrl);
                $this->refreshWxApplication();
            }
        }

        $this->wxService
            ->scopes(['snsapi_userinfo'])
            ->redirect()
            ->send();
    }

    /**
     * 授权回调
     *
     * @param $url
     * @param string $type
     * @return Member|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionCallback($url, $type = self::CALLBACK_NORMAL_TYPE)
    {
        if ($type == self::CALLBACK_OPEN_TYPE && $this->enableThirdPartyLogin) {
            $this->wxApplication['config']->set('app_id', $this->wxConfig['thirdPartyLogin']['app_id']);
            $this->wxApplication['config']->set('secret', $this->wxConfig['thirdPartyLogin']['secret']);
            $scopes = ['snsapi_login'];
            $this->wxApplication['config']->set('oauth.scopes', $scopes);
            $this->refreshWxApplication();
        } else {
            $scopes = $this->wxConfig['oauth']['scopes'];
        }
        $user = $this->wxService->scopes($scopes)->user();
        if ($user) {
            $originalUser = $user->getOriginal();
            $openid = $user->getId();
            if ($this->enableThirdPartyLogin) {
                $wxFieldName = 'unionid';
                $wxFieldValue = $originalUser['unionid']; // unionid
            } else {
                $wxFieldName = 'openid';
                $wxFieldValue = $user->getId(); // openid
            }
            if ($type == self::CALLBACK_OPEN_TYPE) {
                // 第三方登录
                $db = \Yii::$app->getDb();
                $now = time();
                $accessToken = null; // 认证成功后的会员 access token 值
                $memberId = $db->createCommand("SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[$wxFieldName]] = :wxId", [':wxId' => $wxFieldValue])->queryScalar();
                if ($memberId) {
                    $member = Member::findOne(['id' => $memberId]);
                    if ($member !== null) {
                        $member->generateAccessToken();
                        $accessToken = $member->access_token;
                        $member->save(false);
                    }
                } elseif ($memberId === false) {
                    $transaction = $db->beginTransaction();
                    try {
                        if (isset($this->wxConfig['other']['oauth']['autoRegister']) && $this->wxConfig['other']['oauth']['autoRegister']) {
                            $member = new Member();
                            $nickname = preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $user->getNickname());
                            $maxId = $db->createCommand('SELECT MAX([[id]]) FROM {{%member}}')->queryScalar();
                            $member->username = sprintf('wx%08d', $maxId + 1) . rand(1000, 9999);
                            $member->nickname = $nickname ?: $member->username;
                            $member->real_name = $member->nickname;
                            $member->setPassword($member->username);
                            $member->avatar = $user->getAvatar();
                            $member->status = Member::STATUS_ACTIVE;
                            if ($member->save()) {
                                $accessToken = $member->access_token;
                                $memberId = $member->id;
                            } else {
                                return $member;
                            }
                        } else {
                            $memberId = 0;
                        }
                        $columns = [
                            'member_id' => $memberId,
                            'subscribe' => Constant::BOOLEAN_TRUE,
                            'openid' => $openid,
                            'nickname' => $originalUser['nickname'],
                            'sex' => $originalUser['sex'],
                            'country' => $originalUser['country'],
                            'province' => $originalUser['province'],
                            'city' => $originalUser['city'],
                            'language' => $originalUser['language'],
                            'headimgurl' => $originalUser['headimgurl'],
                            'subscribe_time' => $now,
                            'unionid' => $originalUser['unionid'],
                        ];
                        $db->createCommand()->insert('{{%wechat_member}}', $columns)->execute();
                        $transaction->commit();
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        throw new HttpException($e->getMessage());
                    }
                }

                $redirectUrl = urldecode($url);
                if ($accessToken) {
                    // $accessToken 有值则表示会员和微信记录均存在，是一个有效的会员，可以发起登录请求
                    $redirectUrl = UrlHelper::addQueryParam($redirectUrl, 'accessToken', $accessToken, false);
                } else {
                    $redirectUrl = UrlHelper::addQueryParam($redirectUrl, 'xId', $wxFieldValue, false);
                }

                if ($memberId) {
                    $paramKey = 'appendValueIfExistsMember';
                } else {
                    $paramKey = 'appendValueIfNotExistsMember';
                }
                $appendValue = isset($this->wxConfig['other']['oauth'][$paramKey]) ? trim($this->wxConfig['other']['oauth'][$paramKey]) : null;
                if ($appendValue) {
                    if (stripos($redirectUrl, '?') === false) {
                        $redirectUrl .= '?';
                    } else {
                        $redirectUrl .= '&';
                    }
                    $redirectUrl .= $appendValue;
                }

                $this->redirect($redirectUrl);
            } else {
                $isSubscribed = Yii::$app->getDb()->createCommand("SELECT [[subscribe]] FROM {{%wechat_member}} WHERE [[$wxFieldName]] = :wxId", [':wxId' => $wxFieldValue])->queryScalar();
                if ($isSubscribed == Constant::BOOLEAN_TRUE) {
                    $url = UrlHelper::addQueryParam($url, 'openid', $wxFieldValue);
                } else {
                    if (
                        ArrayHelper::getValue($this->wxConfig, 'other.subscribe.required', false) &&
                        ($redirectUrl = ArrayHelper::getValue($this->wxConfig, 'other.subscribe.redirectUrl'))
                    ) {
                        $url = $redirectUrl;
                    }
                }

                return $this->redirect($url);
            }
        } else {
            throw new BadRequestHttpException("Bad request.");
        }
    }

}
