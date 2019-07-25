<?php

namespace app\modules\admin\modules\finance\models;

use app\modules\admin\components\QueryConditionCache;
use DateTime;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * FinanceSearch represents the model behind the search form of `app\modules\admin\modules\finance\models\Finance`.
 */
class FinanceSearch extends Finance
{

    const QUERY_CONDITION_CACHE_KEY = self::class;

    /**
     * @var string 会员帐号
     */
    public $member_username;

    /**
     * @var string 开始时间
     */
    public $begin_date;

    /**
     * @var string 结束时间
     */
    public $end_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'source', 'status', 'member_id'], 'integer'],
            [['related_key'], 'safe'],
            ['member_username', 'trim'],
            ['member_username', 'string'],
            [['begin_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Finance::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => $this->type,
            'source' => $this->source,
            'status' => $this->status,
            'member_id' => $this->member_id,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        if ($this->member_username) {
            $query->andWhere(['IN', 'member_id', (new Query())
                ->select(['id'])
                ->from('{{%member}}')
                ->where(['username' => $this->member_username])
            ]);
        }

        if ($this->begin_date && $this->end_date) {
            $query->andWhere([
                'BETWEEN',
                'created_at',
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
            'member_username' => '会员帐号',
            'begin_date' => '开始时间',
            'end_date' => '结束时间',
        ]);
    }

}
