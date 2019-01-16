<?php

namespace app\modules\api\models;

use yii\data\ActiveDataProvider;

class LabelSearch extends Label
{

    public function rules()
    {
        return [
            [['name', 'alias'], 'string'],
            [['enabled'], 'boolean'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        $query = Label::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['alias' => SORT_ASC],
            ]
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider; // If validation fails, just return the unfiltered list
        }

        $query->andFilterHaving([
            'name' => $this->name,
            'alias' => $this->alias,
            'enabled' => $this->enabled,
        ]);

        return $dataProvider;
    }

}