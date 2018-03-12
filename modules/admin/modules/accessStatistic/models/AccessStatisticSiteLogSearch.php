<?php

namespace app\modules\admin\modules\accessStatistic\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLog;

/**
 * AccessStatisticSiteLogSearch represents the model behind the search form of `app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLog`.
 */
class AccessStatisticSiteLogSearch extends AccessStatisticSiteLog
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'site_id', 'access_datetime'], 'integer'],
            [['ip', 'referrer'], 'safe'],
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
        $query = AccessStatisticSiteLog::find();

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
            'site_id' => $this->site_id,
            'access_datetime' => $this->access_datetime,
        ]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'referrer', $this->referrer]);

        return $dataProvider;
    }
}
