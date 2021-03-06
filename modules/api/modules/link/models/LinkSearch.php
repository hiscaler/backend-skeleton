<?php

namespace app\modules\api\modules\link\models;

use yii\data\ActiveDataProvider;

class LinkSearch extends Link
{

    public function rules()
    {
        return [
            [['title'], 'string'],
            [['category_id', 'type'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        $query = Link::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider; // If validation fails, just return the unfiltered list
        }

        $query->andFilterWhere([
            'type' => $this->type,
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['LIKE', 'title', $this->title]);

        return $dataProvider;
    }

}