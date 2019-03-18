<?php

namespace app\modules\api\models;

use yii\data\ActiveDataProvider;

class FileUploadConfigSearch extends FileUploadConfig
{

    public function rules()
    {
        return [
            ['type', 'integer'],
            [['model_name', 'attribute'], 'string'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        $query = FileUploadConfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['model_name' => SORT_ASC],
            ]
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider; // If validation fails, just return the unfiltered list
        }

        $query->andFilterHaving([
            'type' => $this->type,
            'model_name' => $this->model_name,
            'attribute' => $this->attribute,
        ]);

        return $dataProvider;
    }

}