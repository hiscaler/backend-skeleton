<?php

namespace app\modules\admin\modules\accessStatistic\models;

use app\modules\admin\components\QueryConditionCache;
use DateTime;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * AccessStatisticSiteLogSearch represents the model behind the search form of `app\modules\admin\modules\accessStatistic\models\AccessStatisticSiteLog`.
 */
class AccessStatisticSiteLogSearch extends AccessStatisticSiteLog
{

    public $ip_repeat_times;
    public $access_begin_datetime;
    public $access_end_datetime;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'ip_repeat_times'], 'integer'],
            [['ip', 'referrer'], 'safe'],
            [['access_begin_datetime', 'access_end_datetime'], 'safe'],
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
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
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
            'site_id' => $this->site_id,
        ]);

        if (!$this->access_begin_datetime || !$this->access_end_datetime) {
            $this->access_begin_datetime = $this->access_end_datetime = (new DateTime())->setTime(0, 0, 0)->format('Y-m-d');
        }

        $query->andWhere(['between', 'access_datetime', (new DateTime($this->access_begin_datetime))->setTime(0, 0, 0)->getTimestamp(), (new DateTime($this->access_end_datetime))->setTime(23, 59, 59)->getTimestamp()]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'referrer', $this->referrer]);

        if ($this->ip_repeat_times) {
            $subQuery = (new Query())->select(['ip', 'COUNT(*) AS c'])
                ->from('{{%access_statistic_site_log}}')
                ->groupBy('ip')
                ->having('c >= :c', [':c' => $this->ip_repeat_times])
                ->all();
            $query->andWhere(['IN', 'ip', $subQuery]);
        }

        QueryConditionCache::set(get_parent_class(self::class), $query);

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'ip_repeat_times' => 'IP 重复次数',
            'access_begin_datetime' => '开始时间',
            'access_end_datetime' => '结束时间',
        ]);
    }

}
