<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;

/**
 * 文本内容翻译
 *
 * Class TranslateController
 *
 * @package app\modules\api\controllers
 */
class TranslateController extends BaseController
{

    private $languages = [
        'ar', 'et', 'bg', 'pl', 'ko', 'bs-Latn',
    ];

    private $errors = [
        0 => '成功',
        1001 => '不支持的语言类型',
        1002 => '文本过长',
        1003 => '无效PID',
        1004 => '试用Pid限额已满',
        1005 => 'Pid请求流量过高',
        1006 => '余额不足',
        1007 => '随机数不存在',
        1008 => '签名不存在',
        1009 => '签名不正确',
        10010 => '文本不存在',
        1050 => '内部服务错误',
    ];

    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->params['translate']['class'])) {
            throw new InvalidConfigException('无效的翻译配置。');
        }
    }

    private function _parseHtml($html)
    {
        // @todo Parse HTML
        return $html;
    }

    /**
     * 翻译
     *
     * @param string $from
     * @param string $to
     * @param bool $isHtml
     * @return array
     * @throws BadRequestHttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionIndex($from = 'en', $to = 'zh-CHS', $isHtml = false)
    {
        $config = Yii::$app->params['translate'];
        if ($config['class'] == 'sogou') {
            $pid = $config['pid'];
            $secretKey = $config['secretKey'];
        }

        $message = trim(Yii::$app->getRequest()->post('message'));
        if ($message) {
            if ($isHtml) {
                $message = $this->_parseHtml($message);
            }
            $client = new Client([
                'base_uri' => 'http://fanyi.sogou.com/reventondc/api/sogouTranslate',
                'timeout' => 10,
                'allow_redirects' => false,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded;',
                    'Accept' => 'application/json',
                ]
            ]);
            $salt = $config['class'] . time() . rand(1, 99999);
            try {
                $response = $client->request('POST', '', [
                    'form_params' => [
                        'q' => $message,
                        'from' => $from,
                        'to' => $to,
                        'pid' => $pid,
                        'salt' => $salt,
                        'sign' => md5($pid . $message . $salt . $secretKey),
                        'charset' => 'UTF-8',
                    ]
                ]);
                if ($response->getStatusCode() == 200) {
                    $body = json_decode($response->getBody(), true);
                    if ($body) {
                        if ($body['errorCode'] == 0) {
                            return [
                                '_message' => $body['query'],
                                'message' => $body['translation'],
                            ];
                        } else {
                            // Fail
                            Yii::$app->getResponse()->setStatusCode($response->getStatusCode());

                            return [
                                'error' => [
                                    'code' => $body['errorCode'],
                                    'message' => isset($this->errors[$body['errorCode']]) ? $this->errors[$body['errorCode']] : '未知错误。'
                                ]
                            ];
                        }
                    } else {
                        Yii::$app->getResponse()->setStatusCode($response->getStatusCode());

                        return [
                            'error' => [
                                'message' => $response->getBody(),
                            ]
                        ];
                    }
                } else {
                    Yii::$app->getResponse()->setStatusCode($response->getStatusCode());

                    return [
                        'error' => [
                            'message' => $response->getReasonPhrase(),
                        ]
                    ];
                }
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    throw new BadRequestHttpException($e->getResponse());
                }
            }
        } else {
            throw new BadRequestHttpException('message 参数不能为空。');
        }
    }

}