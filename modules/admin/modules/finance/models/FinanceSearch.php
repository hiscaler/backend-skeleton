<?php

namespace app\modules\admin\modules\finance\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * FinanceSearch represents the model behind the search form of `app\modules\admin\modules\finance\models\Finance`.
 */
class FinanceSearch extends Finance
{

    public $member_username;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'source', 'status', 'member_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['related_key'], 'safe'],
            ['member_username', 'trim'],
            ['member_username', 'string'],
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
            'id' => $this->id,
            'type' => $this->type,
            'source' => $this->source,
            'status' => $this->status,
            'member_id' => $this->member_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        if ($this->member_username) {
            $query->andWhere(['IN', 'member_id', (new Query())
                ->select(['id'])
                ->from('{{%member}}')
                ->where(['username' => $this->member_username])
            ]);
        }

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'member_username' => '会员帐号',
        ]);
    }

}
