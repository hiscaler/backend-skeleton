<?php

namespace app\modules\admin\modules\wechat\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\modules\wechat\models\PayOrder;

/**
 * PayOrderSearch represents the model behind the search form of `app\modules\admin\modules\wechat\models\PayOrder`.
 */
class PayOrderSearch extends PayOrder
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'transfer_time', 'payment_time', 'amount', 'created_at', 'created_by'], 'integer'],
            [['mch_appid', 'mchid', 'device_info', 'nonce_str', 'sign', 'partner_trade_no', 'payment_no', 'openid', 'check_name', 're_user_name', 'desc', 'spbill_create_ip', 'status', 'reason'], 'safe'],
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
        $query = PayOrder::find();

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
            'transfer_time' => $this->transfer_time,
            'payment_time' => $this->payment_time,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'mch_appid', $this->mch_appid])
            ->andFilterWhere(['like', 'mchid', $this->mchid])
            ->andFilterWhere(['like', 'device_info', $this->device_info])
            ->andFilterWhere(['like', 'nonce_str', $this->nonce_str])
            ->andFilterWhere(['like', 'sign', $this->sign])
            ->andFilterWhere(['like', 'partner_trade_no', $this->partner_trade_no])
            ->andFilterWhere(['like', 'payment_no', $this->payment_no])
            ->andFilterWhere(['like', 'openid', $this->openid])
            ->andFilterWhere(['like', 'check_name', $this->check_name])
            ->andFilterWhere(['like', 're_user_name', $this->re_user_name])
            ->andFilterWhere(['like', 'desc', $this->desc])
            ->andFilterWhere(['like', 'spbill_create_ip', $this->spbill_create_ip])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}
