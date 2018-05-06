<?php

namespace app\modules\api\controllers;

use app\models\Category;
use app\modules\api\extensions\BaseController;
use yadjet\helpers\ArrayHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class CategoryController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class CategoryController extends BaseController
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 分类数据
     *
     * @param null|string $sign
     * @param int $level
     * @param bool $flat
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionIndex($sign = null, $level = 0, $flat = true)
    {
        if ($sign) {
            $parentId = \Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => $sign])->queryScalar();
            if (!$parentId) {
                return [];
            }
        } else {
            $parentId = 0;
        }
        $items = Category::getChildren($parentId, $level);
        !$flat && $items = ArrayHelper::toTree($items, 'id', 'parent');

        return $items;
    }

    /**
     * 添加分类
     *
     * @return Category|array
     */
    public function actionCreate()
    {
        $model = new Category();
        $model->loadDefaultValues();
        $model->load(Yii::$app->getRequest()->post(), '');
        if ($model->validate()) {
            $model->save(false);

            return $model;
        } else {
            Yii::$app->getResponse()->setStatusCode(400);

            return $model->getFirstErrors();
        }
    }

    /**
     * 更新分类
     *
     * @param $id
     * @return Category|array
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->getRequest()->post(), '');
        if ($model->validate()) {
            $model->save(false);

            return $model;
        } else {
            Yii::$app->getResponse()->setStatusCode(400);

            return $model->getFirstErrors();
        }
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}