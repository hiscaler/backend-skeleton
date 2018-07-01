<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use Yii;

/**
 * Class UrlController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UrlController extends BaseController
{

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function behaviors()
    {
        $cmd = Yii::$app->getDb()->createCommand('SELECT MAX([[updated_at]]) FROM {{%category}}');
        if ($this->dbCacheTime !== null) {
            $cmd->cache($this->dbCacheTime);
        }
        $timestamp = $cmd->queryScalar();

        return array_merge(parent::behaviors(), [
            [
                'class' => 'yii\filters\HttpCache',
                'lastModified' => function () use ($timestamp) {
                    return $timestamp;
                },
                'etagSeed' => function () use ($timestamp) {
                    return $timestamp;
                }
            ],
        ]);
    }

    /**
     * URL 生成规则
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionRules()
    {
        $rules = [];
        $items = \Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]] FROM {{%category}}')->queryAll();
        foreach ($items as $item) {
            $rules[] = [
                'pattern' => $item['alias'],
                'route' => null,
                'defaults' => [
                    'category' => $item['id']
                ],
            ];
        }

        return $rules;
    }

}