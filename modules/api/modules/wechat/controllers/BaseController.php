<?php

namespace app\modules\api\modules\wechat\controllers;

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
use yii\rest\Controller;

/**
 * 微信处理接口
 * Class BaseController
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseController extends Controller
{

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
        if (!isset(Yii::$app->params['wechat']) || !Yii::$app->params['wechat'] || !isset(Yii::$app->params['wechat']['app_id'], Yii::$app->params['wechat']['secret'])) {
            throw new InvalidConfigException('无效的微信配置。');
        }
        $this->wxConfig = Yii::$app->params['wechat'];
        if (isset($this->wxConfig['enableThirdPartyLogin']) && $this->wxConfig['enableThirdPartyLogin']
        ) {
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
