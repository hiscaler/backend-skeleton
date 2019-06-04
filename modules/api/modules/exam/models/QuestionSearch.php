<?php

namespace app\modules\api\modules\exam\models;

use yii\data\ActiveDataProvider;

class QuestionSearch extends Question
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_bank_id', 'status'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = Question::find();

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
            'question_bank_id' => $this->question_bank_id,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

}