<?php

namespace app\modules\admin\modules\exam\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * QuestionSearch represents the model behind the search form about `app\modules\admin\modules\exam\models\Question`.
 */
class QuestionSearch extends Question
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'question_bank_id', 'type', 'status'], 'integer'],
            [['content', 'options', 'answer', 'resolve'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Question::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'question_bank_id' => $this->question_bank_id,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'answer', $this->answer])
            ->andFilterWhere(['like', 'resolve', $this->resolve]);

        return $dataProvider;
    }

}
