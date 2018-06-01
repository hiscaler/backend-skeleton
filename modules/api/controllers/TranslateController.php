<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use DOMDocument;
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
 * @see http://deepi.sogou.com/docs/fanyiDoc
 *
 * @package app\modules\api\controllers
 */
class TranslateController extends BaseController
{

    private $languages = [
        'ar', 'et', 'bg', 'pl', 'ko', 'bs-Latn', 'fa', 'mww', 'da', 'de', 'ru', 'fr', 'fi', 'tlh-Qaak', 'tlh', 'hr', 'otq', 'ca', 'cs', 'ro', 'lv', 'ht', 'lt', 'nl', 'ms', 'mt', 'pt', 'ja', 'sl', 'th', 'tr', 'sr-Latn', 'sr-Cyrl', 'sk', 'sw', 'af', 'no', 'en', 'es', 'uk', 'ur', 'el', 'hu', 'cy', 'dddddd', 'yua', 'he', 'zh-CHS', 'it', 'hi', 'id', 'zh-CHT', 'vi', 'sv', 'yue', 'fj', 'fil', 'sm', 'to', 'ty', 'mg', 'bn'
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

    private function _htmlTranslateByNode(&$x, $config)
    {
        foreach ($x->childNodes as $node)
            if ($this->_htmlNodeHasChild($node)) {
                $this->_htmlTranslateByNode($node, $config);
            } elseif ($node->nodeType == XML_ELEMENT_NODE) {
                // @
            } else {
                $value = trim($node->textContent);
                if ($value) {
                    $translateRes = $this->_t($value, 'en', 'zh-CHS', null, $config);
                    if ($translateRes && $translateRes['success']) {
                        $value = $translateRes['message'];
                    }
                    $node->textContent = $value;
                }
            }
    }

    private function _htmlNodeHasChild($p)
    {
        if ($p->hasChildNodes()) {
            foreach ($p->childNodes as $c) {
                if ($c->nodeType == XML_ELEMENT_NODE or $c->nodeType == XML_TEXT_NODE) {
                    return true;
                }
            }
        }

        return false;
    }

    private function _t($text, $from = 'en', $to = 'zh-CHS', $client = null, $config = null)
    {
        if ($client === null) {
            $client = new Client([
                'base_uri' => 'http://fanyi.sogou.com/reventondc/api/sogouTranslate',
                'timeout' => 10,
                'allow_redirects' => false,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded;',
                    'Accept' => 'application/json',
                ]
            ]);
        }
        $salt = $config['class'] . time() . rand(1, 99999);
        try {
            $response = $client->request('POST', '', [
                'form_params' => [
                    'q' => $text,
                    'from' => $from,
                    'to' => $to,
                    'pid' => $config['pid'],
                    'salt' => $salt,
                    'sign' => md5($config['pid'] . $text . $salt . $config['secretKey']),
                    'charset' => 'UTF-8',
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody(), true);
                if ($body) {
                    if ($body['errorCode'] == 0) {
                        return [
                            'success' => true,
                            '_message' => $body['query'],
                            'message' => isset($body['translation']) ? $body['translation'] : $body['query'],
                        ];
                    } else {
                        // Fail
                        return [
                            'success' => false,
                            'error' => [
                                'code' => $body['errorCode'],
                                'message' => isset($this->errors[$body['errorCode']]) ? $this->errors[$body['errorCode']] : '未知错误。'
                            ]
                        ];
                    }
                } else {
                    return [
                        'success' => false,
                        'error' => [
                            'message' => $response->getBody(),
                        ]
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'message' => $response->getReasonPhrase(),
                    ]
                ];
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return [
                    'success' => false,
                    'error' => [
                        'message' => $e->getResponse(),
                    ]
                ];
            }
        }

        return null;
    }

    /**
     * 清理 Html 中的样式
     *
     * @param $content
     * @return string
     */
    private function _clean($content)
    {
        return \yii\helpers\HtmlPurifier::process($content, [
            'HTML.Allowed' => 'div,em,a[href|title|style],ul,ol,li,p[style],br',
        ]);
    }

    /**
     * 翻译
     *
     * @param string $from
     * @param string $to
     * @param bool $isHtml
     * @param bool $clean
     * @return array
     * @throws BadRequestHttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionIndex($from = 'en', $to = 'zh-CHS', $isHtml = false, $clean = false)
    {
        if (!in_array($from, $this->languages)) {
            throw new BadRequestHttpException('from 参数无效，可用参数为：' . implode(', ', $this->languages));
        }
        if (!in_array($to, $this->languages)) {
            throw new BadRequestHttpException('to 参数无效，可用参数为：' . implode(', ', $this->languages));
        }

        $config = Yii::$app->params['translate'];
        if ($config['class'] == 'sogou') {
            $pid = $config['pid'];
            $secretKey = $config['secretKey'];
        }

        $message = trim(Yii::$app->getRequest()->post('message'));
        if ($message) {
            $message = preg_replace('/>\s+</', '><', $message);
            if ($isHtml) {
                if ($clean) {
                    $message = $this->_clean($message);
                }
                $doc = new DOMDocument();
                $tag = 'TRANSLATEHTML';
                $doc->loadXML("<{$tag}>{$message}</{$tag}>");
                $this->_htmlTranslateByNode($doc, $config);
                $translateMessage = $doc->saveHTML();
                $translateMessage = str_replace(["<$tag>", "</$tag>"], '', $translateMessage);

                return [
                    '_message' => $message,
                    'message' => $translateMessage,
                ];
            } else {
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
            }
        } else {
            throw new BadRequestHttpException('message 参数不能为空。');
        }
    }

}