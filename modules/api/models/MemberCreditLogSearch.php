<?php

namespace app\modules\api\models;

use yii\data\ActiveDataProvider;

class MemberCreditLogSearch extends MemberCreditLog
{

    public function rules()
    {
        return [
            [['operation', 'related_key'], 'string'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        $query = MemberCreditLog::find()
            ->where(['member_id' => \Yii::$app->getUser()->getId()]);

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
            'operation' => $this->operation,
            'related_key' => $this->related_key,
        ]);

        return $dataProvider;
    }

}