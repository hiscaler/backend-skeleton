<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MemberSearch represents the model behind the search form about `app\models\Member`.
 */
class MemberSearch extends Member
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'usable_scope', 'category_id', 'parent_id'], 'integer'],
            [['username', 'mobile_phone'], 'safe'],
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
     * @throws \yii\db\Exception
     */
    public function search($params)
    {
        $query = Member::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => $this->type,
            'status' => $this->status,
            'usable_scope' => $this->usable_scope,
            'parent_id' => $this->parent_id,
        ]);

        if ($this->category_id) {
            $categoryIds = Category::getChildrenIds($this->category_id);
            $categoryIds[] = $this->category_id;
            $query->andWhere(['IN', 'category_id', $categoryIds]);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone]);

        return $dataProvider;
    }

}
