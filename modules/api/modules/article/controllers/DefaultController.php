<?php

namespace app\modules\api\modules\article\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\article\models\Article;
use yii\web\NotFoundHttpException;

/**
 * /api/article/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends ActiveController
{

    public $modelClass = Article::class;

    /**
     * @param $id
     * @return Article|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        if (is_numeric($id)) {
            $condition = ['id' => (int) $id];
        } else {
            $condition = ['alias' => $id];
        }
        $model = Article::find()->where($condition)->one();
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $model;
    }

}
