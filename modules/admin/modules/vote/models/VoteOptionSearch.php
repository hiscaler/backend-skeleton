<?php

namespace app\modules\admin\modules\vote\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VoteOptionSearch represents the model behind the search form of `app\modules\admin\modules\vote\models\VoteOption`.
 */
class VoteOptionSearch extends VoteOption
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'vote_id', 'ordering', 'votes_count'], 'integer'],
            [['title', 'description', 'photo', 'enabled'], 'safe'],
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
        $query = VoteOption::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'ordering' => SORT_ASC,
                ]
            ]
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
            'vote_id' => $this->vote_id,
            'ordering' => $this->ordering,
            'votes_count' => $this->votes_count,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'enabled', $this->enabled]);

        return $dataProvider;
    }
}
