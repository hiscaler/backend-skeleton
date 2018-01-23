<?php

namespace app\modules\admin\modules\wxpay\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderRefundSearch represents the model behind the search form of `app\modules\admin\modules\wxpay\models\OrderRefund`.
 */
class RefundOrderSearch extends RefundOrder
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'total_fee', 'refund_fee', 'created_at', 'created_by'], 'integer'],
            [['appid', 'mch_id', 'nonce_str', 'sign', 'sign_type', 'transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id', 'refund_fee_type', 'refund_desc', 'refund_account'], 'safe'],
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
        $query = RefundOrder::find();

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
            'order_id' => $this->order_id,
            'total_fee' => $this->total_fee,
            'refund_fee' => $this->refund_fee,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'appid', $this->appid])
            ->andFilterWhere(['like', 'mch_id', $this->mch_id])
            ->andFilterWhere(['like', 'nonce_str', $this->nonce_str])
            ->andFilterWhere(['like', 'sign', $this->sign])
            ->andFilterWhere(['like', 'sign_type', $this->sign_type])
            ->andFilterWhere(['like', 'transaction_id', $this->transaction_id])
            ->andFilterWhere(['like', 'out_trade_no', $this->out_trade_no])
            ->andFilterWhere(['like', 'out_refund_no', $this->out_refund_no])
            ->andFilterWhere(['like', 'refund_id', $this->refund_id])
            ->andFilterWhere(['like', 'refund_fee_type', $this->refund_fee_type])
            ->andFilterWhere(['like', 'refund_desc', $this->refund_desc])
            ->andFilterWhere(['like', 'refund_account', $this->refund_account]);

        return $dataProvider;
    }
}
