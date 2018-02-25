<?php

namespace app\modules\api\modules\article\controllers;

use app\modules\api\modules\article\models\Article;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

/**
 * /api/article/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    public $modelClass = 'app\modules\api\modules\article\models\Article';

    /**
     * 文章列表
     *
     * @api /api/article/default/index
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function actionIndex($page = 1, $pageSize = 20)
    {
        return new ActiveDataProvider([
            'query' => (new ActiveQuery(Article::class)),
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     * 文章详情
     *
     * @api /api/article/default/view?alias=ARTICLE-ALIAS
     *
     * @param $alias
     * @return Article|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($alias)
    {
        $model = $this->findModel($alias);

        return $model;
    }

    public function findModel($alias)
    {
        $model = Article::find()->where(['alias' => $alias])->one();
        if (!$model) {
            throw new NotFoundHttpException('文章不存在。');
        }

        return $model;
    }
}
