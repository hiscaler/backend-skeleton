<?php

namespace app\models;

use app\models\Lookup;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LookupSearch represents the model behind the search form about `app\models\Lookup`.
 */
class LookupSearch extends Lookup
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled'], 'integer'],
            [['label', 'description', 'return_type'], 'safe'],
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
        $query = Lookup::find()->with(['creater', 'updater'])->asArray(true);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'label' => SORT_ASC,
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'return_type' => $this->return_type,
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

}
