<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\extensions\UtilsHelper;
use app\modules\api\models\Member;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;
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

    public function actionIndex($username = null, $fields = null, $page = 1, $pageSize = 20)
    {
        // Basic condition
        $condition = [];
        $selectColumns = UtilsHelper::filterQuerySelectColumns(['t.id', 't.type', 't.category_id', 'c.name AS category_name', 't.group', 't.username', 't.nickname', 't.real_name', 't.avatar', 't.access_token', 't.email', 't.tel', 't.mobile_phone', 't.address', 't.status', 't.remark', 't.created_at', 't.updated_at', 'u.nickname AS editor'], $fields, []);
        $query = (new \yii\db\ActiveQuery(Member::class))
            ->alias('t')
            ->select($selectColumns);
        if (in_array('c.name AS category_name', $selectColumns)) {
            $query->leftJoin('{{%category}} c', '[[t.category_id]] = [[c.id]]');
        }
        if (in_array('u.nickname AS editor', $selectColumns)) {
            $query->leftJoin('{{%user}} u', 't.updated_by = u.id');
        }

        $query->offset($page)->limit($pageSize);

        $query->where($condition);

        // Order By
        $orderByColumns = [];
        if (!empty($orderBy)) {
            $orderByColumnLimit = ['id', 'usernames', 'categoryId', 'createdAt', 'updatedAt']; // Supported order by column names
            foreach (explode(',', trim($orderBy)) as $string) {
                if (!empty($string)) {
                    $string = explode('.', $string);
                    if (in_array($string[0], $orderByColumnLimit)) {
                        $orderByColumns['t.' . Inflector::camel2id($string[0], '_')] = isset($string[1]) && $string[1] == 'asc' ? SORT_ASC : SORT_DESC;
                    }
                }
            }
        }

        $query->orderBy($orderByColumns ?: ['t.id' => SORT_DESC]);
        if ($this->debug) {
            Yii::debug($query->createCommand()->getRawSql(), 'API DEBUG');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
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