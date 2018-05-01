<?php

namespace app\modules\admin\modules\vote\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VoteSearch represents the model behind the search form of `app\modules\admin\modules\votes\models\Vote`.
 */
class VoteSearch extends Vote
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'begin_datetime', 'end_datetime', 'interval_seconds', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'description', 'allow_anonymous', 'allow_view_results', 'allow_multiple_choice', 'enabled'], 'safe'],
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
        $query = Vote::find();

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
            'category_id' => $this->category_id,
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
            'interval_seconds' => $this->interval_seconds,
            'ordering' => $this->ordering,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'allow_anonymous', $this->allow_anonymous])
            ->andFilterWhere(['like', 'allow_view_results', $this->allow_view_results])
            ->andFilterWhere(['like', 'allow_multiple_choice', $this->allow_multiple_choice])
            ->andFilterWhere(['like', 'enabled', $this->enabled]);

        return $dataProvider;
    }
}
