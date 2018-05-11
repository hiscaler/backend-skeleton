<?php

namespace app\modules\admin\modules\miniActivity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MiniActivityWheelSearch represents the model behind the search form of `app\modules\admin\modules\miniActivity\models\Wheel`.
 */
class WheelSearch extends Wheel
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'begin_datetime', 'end_datetime', 'estimated_people_count', 'actual_people_count', 'play_times_per_person', 'play_limit_type', 'play_times_per_person_by_limit_type', 'win_times_per_person', 'win_interval_seconds', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'win_message', 'get_award_message', 'description', 'photo', 'repeat_play_message', 'background_image', 'background_image_repeat_type', 'finished_title', 'finished_description', 'finished_photo', 'show_awards_quantity', 'enabled'], 'safe'],
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
        $query = Wheel::find();

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
            'begin_datetime' => $this->begin_datetime,
            'end_datetime' => $this->end_datetime,
            'estimated_people_count' => $this->estimated_people_count,
            'actual_people_count' => $this->actual_people_count,
            'play_times_per_person' => $this->play_times_per_person,
            'play_limit_type' => $this->play_limit_type,
            'play_times_per_person_by_limit_type' => $this->play_times_per_person_by_limit_type,
            'win_times_per_person' => $this->win_times_per_person,
            'win_interval_seconds' => $this->win_interval_seconds,
            'ordering' => $this->ordering,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'win_message', $this->win_message])
            ->andFilterWhere(['like', 'get_award_message', $this->get_award_message])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'repeat_play_message', $this->repeat_play_message])
            ->andFilterWhere(['like', 'background_image', $this->background_image])
            ->andFilterWhere(['like', 'background_image_repeat_type', $this->background_image_repeat_type])
            ->andFilterWhere(['like', 'finished_title', $this->finished_title])
            ->andFilterWhere(['like', 'finished_description', $this->finished_description])
            ->andFilterWhere(['like', 'finished_photo', $this->finished_photo])
            ->andFilterWhere(['like', 'show_awards_quantity', $this->show_awards_quantity])
            ->andFilterWhere(['like', 'enabled', $this->enabled]);

        return $dataProvider;
    }
}
