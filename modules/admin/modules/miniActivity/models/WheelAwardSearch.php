<?php

namespace app\modules\admin\modules\miniActivity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WheelAwardSearch represents the model behind the search form of `app\modules\admin\modules\miniActivity\models\WheelAward`.
 */
class WheelAwardSearch extends WheelAward
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'wheel_id', 'ordering', 'total_quantity', 'remaining_quantity'], 'integer'],
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
        $query = WheelAward::find();

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
            'wheel_id' => $this->wheel_id,
            'ordering' => $this->ordering,
            'total_quantity' => $this->total_quantity,
            'remaining_quantity' => $this->remaining_quantity,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'enabled', $this->enabled]);

        return $dataProvider;
    }
}
