<?php

namespace app\modules\admin\modules\link\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LinkSearch represents the model behind the search form of `app\modules\admin\modules\link\models\Link`.
 */
class LinkSearch extends Link
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'type', 'enabled'], 'integer'],
            [['title'], 'safe'],
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
        $query = Link::find()->with(['creater', 'updater'])->asArray();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'category_id' => SORT_ASC,
                    'ordering' => SORT_ASC,
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
            'category_id' => $this->category_id,
            'type' => $this->type,
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }

}
