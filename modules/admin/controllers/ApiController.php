<?php

namespace app\modules\admin\controllers;

use app\models\Lookup;
use Yii;

/**
 * 接口
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class ApiController extends \yii\rest\Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $formatter = Yii::$app->getFormatter();
            $language = Lookup::getValue('language', 'zh-CN');
            if ($language) {
                Yii::$app->language = $language;
            }
            $timezone = Lookup::getValue('timezone', 'PRC');
            if ($timezone) {
                Yii::$app->timeZone = $timezone;
            }

            $formatter->defaultTimeZone = Yii::$app->timeZone;
            $dateFormat = Lookup::getValue('dateFormat', 'php:Y-m-d');
            if ($dateFormat) {
                $formatter->dateFormat = $dateFormat;
            }
            $timeFormat = Lookup::getValue(('timeFormat', 'php:H:i:s');
            if ($timeFormat) {
                $formatter->timeFormat = $timeFormat;
            }
            $datetimeFormat = Lookup::getValue(('datetimeFormat', 'php:Y-m-d H:i:s');
            if ($datetimeFormat) {
                $formatter->datetimeFormat = $datetimeFormat;
            }

            Yii::$app->getResponse()->format = 'json';

            return true;
        }

        return false;
    }

    /**
     * 数据验证规则
     *
     * @return array
     */
    public function actionValidators()
    {
        $validators = [
            'required' => [
                'class' => '\yii\validators\RequiredValidator',
                'label' => Yii::t('meta', 'Required Validator'),
            ],
            'integer' => [
                'class' => '\yii\validators\IntegerValidator',
                'label' => Yii::t('meta', 'Integer Validator'),
                'options' => [
                    'min' => null,
                    'max' => null,
                    'message' => null,
                ]
            ],
            'string' => [
                'class' => '\yii\validators\StringValidator',
                'label' => Yii::t('meta', 'String Validator'),
                'options' => [
                    'length' => null,
                    'min' => null,
                    'max' => null,
                    'message' => null,
                    'encoding' => Yii::$app->charset
                ]
            ],
            'email' => [
                'class' => '\yii\validators\EmailValidator',
                'label' => Yii::t('meta', 'Email Validator'),
            ],
            'url' => [
                'class' => '\yii\validators\UrlValidator',
                'label' => Yii::t('meta', 'Url Validator'),
            ],
            'date' => [
                'class' => '\yii\validators\DateValidator',
                'label' => Yii::t('meta', 'Date Validator'),
                'options' => [
                    'format' => null,
                    'timeZone' => Yii::$app->getTimeZone(),
                ]
            ],
        ];

        foreach ($validators as $name => $config) {
            if (!isset($config['options']) || empty($config['options'])) {
                $config['messages'] = $config['options'] = new \stdClass();
            } else {
                $messages = [];
                foreach ($config['options'] as $opt => $value) {
                    $messages[$opt] = Yii::t('meta', ucwords($name) . ' ' . ucwords($opt));
                }
                $config['messages'] = $messages;
            }
            $validators[$name] = $config;
        }

        return $validators;
    }

    /**
     * 指定数据的验证规则
     *
     * @param $metaId
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionMetaValidators($metaId)
    {
        $metaValidators = Yii::$app->getDb()->createCommand('SELECT [[name]], [[options]] FROM {{%meta_validator}} WHERE [[meta_id]] = :metaId', [':metaId' => (int) $metaId])->queryAll();
        foreach ($metaValidators as $key => $item) {
            $options = unserialize($item['options']);
            if (!$options) {
                $options = new \stdClass();
            }
            $metaValidators[$key]['options'] = $options;
        }

        return $metaValidators;
    }

}
