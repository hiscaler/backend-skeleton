<?php

namespace app\modules\api\models;

use Yii;
use yii\data\ActiveDataProvider;

class MemberSearch extends Member
{

    public function rules()
    {
        return [
            [['username', 'real_name', 'mobile_phone'], 'string'],
            [['type'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        $query = Member::find();
        $where = [];
        if ($identity = Yii::$app->getUser()->getIdentity()) {
            /* @var $identity Member */
            if ($identity->type !== Member::TYPE_ADMINISTRATOR) {
                $where = '1 = 0'; // 非管理员不能查看会员信息
            }
        }
        $where && $query->where($where);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider; // If validation fails, just return the unfiltered list
        }

        $query->andFilterHaving([
            'username' => $this->username,
            'real_name' => $this->real_name,
            'mobile_phone' => $this->mobile_phone,
            'created_by' => $this->created_by,
        ]);

        return $dataProvider;
    }

}