<?php

namespace app\modules\api\modules\ticket\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TicketMessageSearch extends TicketMessage
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ticket_id', 'parent_id', 'member_id', 'reply_user_id', 'created_at'], 'integer'],
            [['reply_username'], 'safe'],
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
        $query = TicketMessage::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'parent_id' => $this->parent_id,
            'member_id' => $this->member_id,
            'reply_user_id' => $this->reply_user_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'reply_username', $this->reply_username]);

        return $dataProvider;
    }

}
