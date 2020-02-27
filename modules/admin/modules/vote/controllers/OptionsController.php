<?php

namespace app\modules\admin\modules\vote\controllers;

use app\modules\admin\modules\vote\models\Vote;
use app\modules\admin\modules\vote\models\VoteOption;
use app\modules\admin\modules\vote\models\VoteOptionSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * 投票选项管理
 *
 * @package app\modules\admin\modules\vote\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class OptionsController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 投票选项列表
     *
     * @rbacDescription 投票选项列表查看权限
     * @param $voteId
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($voteId)
    {
        $vote = $this->findVoteModel($voteId);
        $searchModel = new VoteOptionSearch();
        $searchModel->vote_id = (int) $voteId;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'vote' => $vote,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 投票选项详情
     *
     * @rbacDescription 投票选项详情查看权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 投票选项添加
     *
     * @rbacDescription 投票选项添加权限
     * @param $voteId
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($voteId)
    {
        $vote = $this->findVoteModel($voteId);

        $model = new VoteOption();
        $model->vote_id = $vote->id;
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'vote' => $vote,
            'model' => $model,
        ]);
    }

    /**
     * 投票选项更新
     *
     * @rbacDescription 投票选项更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 投票选项删除
     *
     * @rbacDescription 投票选项删除权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the VoteOption model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return VoteOption the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VoteOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return Vote|null
     * @throws NotFoundHttpException
     */
    protected function findVoteModel($id)
    {
        if (($model = Vote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
