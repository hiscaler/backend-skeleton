<?php

namespace app\modules\admin\modules\ticket\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TicketMessageSearch represents the model behind the search form of `app\modules\admin\modules\ticket\models\TicketMessage`.
 */
class TicketMessageSearch extends TicketMessage
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'type'], 'integer'],
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
        $query = TicketMessage::find()
            ->where(['ticket_id' => $this->ticket_id]);

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
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'reply_username', $this->reply_username]);

        return $dataProvider;
    }

}
