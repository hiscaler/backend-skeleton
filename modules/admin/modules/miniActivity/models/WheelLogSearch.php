<?php

namespace app\modules\admin\modules\miniActivity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WheelLogSearch represents the model behind the search form of `app\modules\admin\modules\miniActivity\models\WheelLog`.
 */
class WheelLogSearch extends WheelLog
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'wheel_id', 'award_id', 'post_datetime', 'member_id', 'get_datetime'], 'integer'],
            [['is_win', 'ip_address', 'is_get', 'get_password', 'remark'], 'safe'],
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
        $query = WheelLog::find();

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
            'award_id' => $this->award_id,
            'post_datetime' => $this->post_datetime,
            'member_id' => $this->member_id,
            'get_datetime' => $this->get_datetime,
        ]);

        $query->andFilterWhere(['like', 'is_win', $this->is_win])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'is_get', $this->is_get])
            ->andFilterWhere(['like', 'get_password', $this->get_password])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
