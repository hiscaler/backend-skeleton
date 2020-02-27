<?php

namespace app\modules\admin\controllers;

use app\models\MemberGroup;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 用户分组管理
 * Class MemberGroupsController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberGroupsController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete'],
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
     * 会员分组数据展示
     *
     * @rbacDescription 会员分组数据展示
     * @return mixed
     */
    public function actionIndex()
    {
        $query = (new Query())
            ->from('{{%member_group}}')
            ->orderBy(['min_credits' => SORT_ASC]);

        $userGroupDataProvider = new ActiveDataProvider([
            'query' => $query->where(['type' => MemberGroup::TYPE_USER_GROUP]),
            'pagination' => [
                'pageSize' => $query->count(),
            ],
        ]);

        $systemGroupQuery = clone($query);
        $systemGroupDataProvider = new ActiveDataProvider([
            'query' => $systemGroupQuery->where(['type' => MemberGroup::TYPE_SYSTEM_GROUP]),
            'pagination' => [
                'pageSize' => $systemGroupQuery->count(),
            ],
        ]);

        return $this->render('index', [
            'userGroupDataProvider' => $userGroupDataProvider,
            'systemGroupDataProvider' => $systemGroupDataProvider,
        ]);
    }

    /**
     * 添加会员分组
     *
     * @rbacDescription 添加会员分组
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MemberGroup();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 更新会员分组
     *
     * @rbacDescription 更新会员分组
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 删除会员分组
     *
     * @rbacDescription 删除会员分组
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return MemberGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MemberGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
