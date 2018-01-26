<?php

namespace app\modules\admin\modules\wechat\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderSearch represents the model behind the search form of `app\modules\admin\modules\wechat\models\Order`.
 */
class OrderSearch extends Order
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'total_fee', 'time_start', 'time_expire', 'status'], 'integer'],
            [['appid', 'mch_id', 'device_info', 'nonce_str', 'sign', 'sign_type', 'transaction_id', 'out_trade_no', 'body', 'detail', 'attach', 'fee_type', 'spbill_create_ip', 'goods_tag', 'trade_type', 'product_id', 'limit_pay', 'openid', 'trade_state'], 'safe'],
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

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'total_fee' => $this->total_fee,
            'time_start' => $this->time_start,
            'time_expire' => $this->time_expire,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'appid', $this->appid])
            ->andFilterWhere(['like', 'mch_id', $this->mch_id])
            ->andFilterWhere(['like', 'device_info', $this->device_info])
            ->andFilterWhere(['like', 'nonce_str', $this->nonce_str])
            ->andFilterWhere(['like', 'sign', $this->sign])
            ->andFilterWhere(['like', 'sign_type', $this->sign_type])
            ->andFilterWhere(['like', 'transaction_id', $this->transaction_id])
            ->andFilterWhere(['like', 'out_trade_no', $this->out_trade_no])
            ->andFilterWhere(['like', 'body', $this->body])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'attach', $this->attach])
            ->andFilterWhere(['like', 'fee_type', $this->fee_type])
            ->andFilterWhere(['like', 'spbill_create_ip', $this->spbill_create_ip])
            ->andFilterWhere(['like', 'goods_tag', $this->goods_tag])
            ->andFilterWhere(['like', 'trade_type', $this->trade_type])
            ->andFilterWhere(['like', 'product_id', $this->product_id])
            ->andFilterWhere(['like', 'limit_pay', $this->limit_pay])
            ->andFilterWhere(['like', 'openid', $this->openid])
            ->andFilterWhere(['=', 'trade_state', $this->trade_state]);

        return $dataProvider;
    }

}
