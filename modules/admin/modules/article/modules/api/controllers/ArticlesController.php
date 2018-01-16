<?php

namespace app\modules\admin\modules\article\modules\api\controllers;

use app\modules\admin\modules\article\modules\api\models\Article;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

/**
 * /admin/article/api/articles
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class ArticlesController extends Controller
{

    public $modelClass = 'app\modules\admin\modules\article\modules\api\models\Article';

    /**
     * 红包列表
     *
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     * @api /admin/article/api/articles/index
     */
    public function actionIndex($page = 1, $pageSize = 20)
    {
        return new ActiveDataProvider([
            'query' => (new ActiveQuery(Article::className())),
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     *文章详情
     *
     * @api /admin/article/api/articles/view?alias=about
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
