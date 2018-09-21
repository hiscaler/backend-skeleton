<?php

namespace app\modules\api\controllers;

use app\models\Category;
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
     * URL 生成规则（适用于 Yii）
     *
     * @param null $sign
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionRules($sign = null)
    {
        $rules = [];

        if ($sign) {
            $category = \Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => trim($sign)])->queryOne();
            if (!$category) {
                return [];
            }
            $items = Category::getChildren($category['id']);
            $replaceStr = $category['alias'];
        } else {
            $replaceStr = null;
            $items = \Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]] FROM {{%category}}')->queryAll();
        }

        foreach ($items as $item) {
            $alias = $item['alias'];
            if ($replaceStr) {
                if (($i = strripos($alias, $replaceStr . '/')) !== false) {
                    $alias = substr($alias, $i + strlen($replaceStr) + 1);
                }
            }
            $rules[] = [
                'pattern' => $alias,
                'route' => null,
                'defaults' => [
                    'category' => $item['id']
                ],
            ];
        }

        return $rules;
    }

    /**
     * URL 生成规则（适用于 NodeJs 前端）
     *
     * @param null $sign
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionMap($sign = null)
    {
        $rules = [];

        if ($sign) {
            $category = \Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => trim($sign)])->queryOne();
            if (!$category) {
                return [];
            }
            $items = Category::getChildren($category['id']);
            $replaceStr = $category['alias'];
        } else {
            $replaceStr = null;
            $items = \Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]] FROM {{%category}}')->queryAll();
        }

        foreach ($items as $item) {
            $alias = $item['alias'];
            if ($replaceStr) {
                if (($i = strripos($alias, $replaceStr . '/')) !== false) {
                    $alias = substr($alias, $i + strlen($replaceStr) + 1);
                }
            }
            $rules[$item['id']] = $alias;
        }

        return $rules;
    }

}