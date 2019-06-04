<?php

namespace app\modules\api\modules\exam\models;

use yii\data\ActiveDataProvider;

class QuestionBankSearch extends QuestionBank
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = QuestionBank::find();

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
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

}