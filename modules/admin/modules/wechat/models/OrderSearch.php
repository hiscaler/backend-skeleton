<?php

namespace app\modules\admin\modules\wechat\models;

use app\modules\admin\components\QueryConditionCache;
use DateTime;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderSearch represents the model behind the search form of `app\modules\admin\modules\wechat\models\Order`.
 */
class OrderSearch extends Order
{

    const QUERY_CONDITION_CACHE_KEY = self::class;

    /**
     * @var string 开始时间
     */
    public $begin_date;

    /**
     * @var string 结束时间
     */
    public $end_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'integer'],
            [['transaction_id', 'out_trade_no', 'trade_state'], 'safe'],
            [['begin_date', 'end_date'], 'date', 'format' => 'php:Y-m-d']
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
     * @throws \Exception
     */
    public function search($params)
    {
        $query = Order::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'out_trade_no' => $this->out_trade_no,
            'trade_state' => $this->trade_state,
        ]);

        if ($this->begin_date && $this->end_date) {
            $query->andWhere([
                'BETWEEN',
                'time_start',
                (new DateTime($this->begin_date))->getTimestamp(),
                (new DateTime($this->end_date))->setTime(23, 59, 59)->getTimestamp(),
            ]);
        }
        
        QueryConditionCache::set(self::QUERY_CONDITION_CACHE_KEY, $query);

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'begin_date' => '开始时间',
            'end_date' => '结束时间',
        ]);
    }

}
