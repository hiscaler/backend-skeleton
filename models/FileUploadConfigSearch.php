<?php

namespace app\models;

use app\models\FileUploadConfig;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FileUploadConfigSearch represents the model behind the search form about `app\models\FileUploadConfig`.
 */
class FileUploadConfigSearch extends FileUploadConfig
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['model_name'], 'safe'],
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
        $query = FileUploadConfig::find()->with(['creater', 'updater', 'deleter'])->asArray(true);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'model_name' => SORT_ASC,
                    'attribute' => SORT_ASC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'model_name', $this->model_name]);

        return $dataProvider;
    }

}
