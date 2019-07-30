<?php

namespace app\modules\api\modules\feedback\controllers;

use app\models\Meta;
use app\modules\admin\forms\DynamicForm;
use app\modules\api\extensions\yii\rest\CreateAction;
use app\modules\api\modules\feedback\models\Feedback;
use app\modules\api\modules\feedback\models\FeedbackSearch;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;

/**
 * /api/feedback/default
 *
 * @package app\modules\api\modules\feedback\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    public $modelClass = Feedback::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['create']['class'] = CreateAction::class;
        unset($actions['create']);

        return $actions;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'delete' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'view', 'delete', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        $search = new FeedbackSearch();

        return $search->search(Yii::$app->getRequest()->getQueryParams());
    }

    /**
     * 提交留言反馈
     *
     * @return Feedback|array
     * @throws \yii\base\ErrorException
     * @api POST /api/feedback/default/submit
     */
    public function actionCreate()
    {
        $model = new Feedback();
        $model->loadDefaultValues();

        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $payload = [];
        foreach (Yii::$app->getRequest()->post() as $key => $value) {
            if (strpos($key, '_') !== false) {
                $key = Inflector::camel2id($key, '_');
            }
            $payload[$key] = $value;
        }
        if ($payload) {
            if (($model->load($payload, '') && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($payload) && $dynamicModel->validate()))) {
                if ($model->save(false)) {
                    $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

                    return $model;
                }
            }

            Yii::$app->getResponse()->setStatusCode(400);

            return $model->errors;
        } else {
            throw new InvalidArgumentException('未检测到提交的内容。');
        }
    }

}
