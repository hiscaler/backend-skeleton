<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\models\Member;
use yii\web\NotFoundHttpException;

/**
 * api/member/ 接口
 * Class MemberController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberController extends BaseController
{

    public function actionIndex()
    {
    }

    /**
     * @param $id
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
    }

    /**
     * @param $id
     * @return Member|null
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Member::findOne((int) $id);
        if ($model === null) {
            throw new NotFoundHttpException('Not found');
        }

        return $model;
    }

}