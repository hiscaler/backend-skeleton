<?php

namespace app\modules\admin\modules\signin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SigninCreditConfigSearch represents the model behind the search form of `app\modules\admin\modules\signin\models\SigninCreditConfig`.
 */
class SigninCreditConfigSearch extends SigninCreditConfig
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'credits'], 'integer'],
            [['message'], 'safe'],
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
        $query = SigninCreditConfig::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'credits' => SORT_ASC,
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
            'credits' => $this->credits,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
