<?php

namespace app\modules\api\modules\finance\models;

use yii\data\ActiveDataProvider;

class FinanceSearch extends Finance
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'source', 'status'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Finance::find()
            ->where(['member_id' => \Yii::$app->getUser()->getId()]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'source' => $this->source,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

}