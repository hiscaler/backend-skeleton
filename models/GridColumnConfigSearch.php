<?php

namespace app\models;

use app\models\GridColumnConfig;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GridColumnConfigSearch represents the model behind the search form about `app\models\GridColumnConfig`.
 */
class GridColumnConfigSearch extends GridColumnConfig
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'], 'integer'],
            [['name'], 'safe'],
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
        $query = GridColumnConfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'visible' => $this->visible,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
