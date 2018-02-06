<?php

namespace app\modules\api\modules\feedback\controllers;

use app\models\Meta;
use app\modules\admin\forms\DynamicForm;
use app\modules\api\modules\feedback\models\Feedback;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

/**
 * /api/feedback/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    public $modelClass = 'app\modules\api\modules\feedback\models\Feedback';

    /**
     * 文章列表
     *
     * @api /api/feedback/default/index
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function actionIndex($page = 1, $pageSize = 20)
    {
        return new ActiveDataProvider([
            'query' => (new ActiveQuery(Feedback::className())),
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     * 文章详情
     *
     * @api /api/feedback/default/view?id=:id
     *
     * @param $id
     * @return Article|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $model;
    }

    public function actionSubmit()
    {
        $model = new Feedback();
        $model->loadDefaultValues();

        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();
        if ($post) {
            if (($model->load($post, '') && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
                if ($model->save(false)) {
                    $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

                    return $model;
                }
            }

            Yii::$app->getResponse()->setStatusCode(400);

            return $model->errors;
        } else {
            throw new InvalidParamException('未检测到提交的内容。');
        }
    }

    public function findModel($id)
    {
        $model = Feedback::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('记录不存在。');
        }

        return $model;
    }
}
