<?php

namespace app\modules\api\modules\slide\models;

use app\modules\api\modules\notice\models\Notice;
use yii\data\ActiveDataProvider;

class NoticeSearch extends Notice
{

    public function rules()
    {
        return [
            [['title'], 'string'],
            [['category_id', 'enabled'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        $query = Notice::find();

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
            'category_id' => $this->category_id,
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['LIKE', 'title', $this->title]);

        return $dataProvider;
    }

}