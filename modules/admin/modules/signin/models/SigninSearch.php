<?php

namespace app\modules\admin\modules\signin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\modules\signin\models\Signin;

/**
 * SigninSearch represents the model behind the search form of `app\modules\admin\modules\signin\models\Signin`.
 */
class SigninSearch extends Signin
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'ymd', 'signin_datetime'], 'integer'],
            [['ip_address'], 'safe'],
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
        $query = Signin::find();

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
            'member_id' => $this->member_id,
            'ymd' => $this->ymd,
            'signin_datetime' => $this->signin_datetime,
        ]);

        $query->andFilterWhere(['like', 'ip_address', $this->ip_address]);

        return $dataProvider;
    }
}
