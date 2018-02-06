<?php

namespace app\modules\admin\modules\slide\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SlideSearch represents the model behind the search form about `app\models\Slide`.
 */
class SlideSearch extends Slide
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'enabled'], 'integer'],
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
        $query = Slide::find();

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
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }

}
