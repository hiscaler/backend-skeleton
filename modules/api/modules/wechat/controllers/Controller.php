<?php

namespace app\modules\api\modules\wechat\controllers;

use app\helpers\Config;
use app\modules\api\extensions\BaseController;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Material\Temporary;
use EasyWeChat\ShakeAround\ShakeAround;
use EasyWeChat\User\Tag;
use Overtrue\Socialite\Providers\WeChatProvider;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * 微信处理接口
 * Class BaseController
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class Controller extends BaseController
{

    /**
     * 登录类型
     */
    const LOGIN_BY_WX = "wx";
    const LOGIN_BY_WXAPP = "wxapp";
    const LOGIN_BY_OPEN = "open";

    const DEFAULT_RETURN_MESSAGE = '';

    protected $wxConfig;

    /* @var $_application Application */
    protected $wxApplication;

    /* @var $wxService \EasyWeChat\Core\AccessToken|\EasyWeChat\Server\Guard|\EasyWeChat\User\User|\EasyWeChat\User\Tag|\EasyWeChat\User\Group|\EasyWeChat\Js\Js|\Overtrue\Socialite\Providers\WeChatProvider|\EasyWeChat\Menu\Menu|\EasyWeChat\Notice\Notice|\EasyWeChat\Material\Material|\EasyWeChat\Material\Temporary|\EasyWeChat\Staff\Staff|\EasyWeChat\Url\Url|\EasyWeChat\QRCode\QRCode|\EasyWeChat\Semantic\Semantic\EasyWeChat\Stats\Stats|\EasyWeChat\Payment\Merchant|\EasyWeChat\Payment\Payment|\EasyWeChat\Payment\LuckyMoney\LuckyMoney|\EasyWeChat\Payment\MerchantPay\MerchantPay|\EasyWeChat\Payment\CashCoupon\CashCoupon|\EasyWeChat\Reply\Reply|\EasyWeChat\Broadcast\Broadcast|\EasyWeChat\Card\Card|\EasyWeChat\Device\Device|\EasyWeChat\Comment\Comment|\EasyWeChat\ShakeAround\ShakeAround|\EasyWeChat\OpenPlatform\OpenPlatform|\EasyWeChat\MiniProgram\MiniProgram */
    protected $wxService;

    /**
     * @var bool 是否激活第三方登录
     */
    protected $enableThirdPartyLogin = false;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->params['wechat']) || !Yii::$app->params['wechat']) {
            throw new InvalidConfigException('无效的微信配置。');
        }
        $loginBy = strtolower(Yii::$app->getRequest()->get('_loginBy', self::LOGIN_BY_WX));
        switch ($loginBy) {
            case self::LOGIN_BY_WX:
                $appIdKey = 'wechat.app_id';
                $appSecretKey = 'wechat.secret';
                break;

            case self::LOGIN_BY_WXAPP:
                $appIdKey = 'wechat.wxapp.app_id';
                $appSecretKey = 'wechat.wxapp.secret';
                break;

            default:
                $appIdKey = 'wechat.thirdPartyLogin.app_id';
                $appSecretKey = 'wechat.thirdPartyLogin.secret';
                break;
        }

        $config = Yii::$app->params['wechat'];
        $config['app_id'] = Config::get($appIdKey);
        $config['secret'] = Config::get($appSecretKey);
        if (!$config['app_id'] || !$config['secret']) {
            throw new InvalidConfigException('请设置有效的 appid 和 secret。');
        }
        $this->wxConfig = $config;
        if (isset($this->wxConfig['enableThirdPartyLogin']) && $this->wxConfig['enableThirdPartyLogin']) {
            if (!isset($this->wxConfig['thirdPartyLogin'], $this->wxConfig['thirdPartyLogin']['app_id'], $this->wxConfig['thirdPartyLogin']['secret'])) {
                throw new InvalidConfigException('无效的微信第三方登录配置。');
            } else {
                $this->enableThirdPartyLogin = true;
            }
        }

        if (is_array($this->wxConfig['oauth']['callback'])) {
            $this->wxConfig['oauth']['callback'] = Url::toRoute($this->wxConfig['oauth']['callback']);
        }

        // 支付设置
        if (isset($this->wxConfig['merchant_id'])) {
            $certPath = $this->wxConfig['cert_path'];
            $keyPath = $this->wxConfig['key_path'];
            if ($certPath || $keyPath) {
                $dir = Yii::getAlias('@webroot');
                if (!file_exists($certPath)) {
                    $certPath = $dir . '/' . trim($certPath, '/');
                    $this->wxConfig['cert_path'] = FileHelper::normalizePath($certPath, '/');
                }
                if ($keyPath && !file_exists($keyPath)) {
                    $keyPath = $dir . '/' . trim($keyPath, '/');
                    $this->wxConfig['key_path'] = FileHelper::normalizePath($keyPath, '/');
                }
            }
        }

        $this->wxApplication = new Application($this->wxConfig);
    }

    /**
     * 刷新 wxApplication
     */
    public function refreshWxApplication()
    {
        $this->wxApplication = new Application($this->wxApplication['config']->all());
        if ($this->wxService !== null) {
            $className = get_class($this->wxService);
            switch ($className) {
                case WeChatProvider::class:
                    $className = 'oauth';
                    break;

                case Tag::class:
                    $className = 'user_tag';
                    break;

                case Temporary::class:
                    $className = 'material_temporary';
                    break;

                case ShakeAround::class:
                    $className = 'shakearound';
                    break;

                default:
                    if (($index = strrpos($className, '\\')) !== false) {
                        $className = substr($className, $index + 1);
                        $className = Inflector::camel2id($className, '_');
                    }
                    $className = strtolower($className);
            }

            $this->wxService = $this->wxApplication->{$className};
        }
    }

}
