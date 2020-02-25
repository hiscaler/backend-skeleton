<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MemberLoginLogSearch represents the model behind the search form of `app\models\MemberLoginLog`.
 */
class MemberLoginLogSearch extends MemberLoginLog
{

    public $username;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip', 'username'], 'safe'],
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
     * @throws \yii\db\Exception
     */
    public function search($params)
    {
        $query = MemberLoginLog::find();

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

        if ($this->username) {
            $memberId = \Yii::$app->getDb()->createCommand("SELECT [[id]] FROM {{%member}} WHERE [[username]] = :username", [':username' => $this->username])->queryScalar();
            if ($memberId) {
                $query->andWhere(['member_id' => $memberId]);
            } else {
                $query->where('0 = 1');
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ip' => $this->ip,
        ]);

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'username' => '会员帐号',
        ]);
    }

}
