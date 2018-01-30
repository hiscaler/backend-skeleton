<?php

namespace app\modules\admin\modules\slide\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\slide\models\Slide;

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
            [['id', 'group_id', 'ordering', 'enabled', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'url', 'url_open_target'], 'safe'],
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
            'id' => $this->id,
            'group_id' => $this->group_id,
            'ordering' => $this->ordering,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'url_open_target', $this->url_open_target]);

        return $dataProvider;
    }

}
