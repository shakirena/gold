<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Costs;

/**
 * CostsSearch represents the model behind the search form about `app\models\Costs`.
 */
class CostsSearch extends Costs
{
    /**
     * @inheritdoc
     */
	public $date_start;
    public $date_end;
	public $type;
    public function rules()
    {
        return [
            [['id', 'id_type','id_kassa','id_user'], 'integer'],
            [['sum'], 'number'],
            [['note', 'datetime','date_start','date_end', 'type'], 'safe'],
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
        $query = Costs::find();

        // add conditions that should always apply here

		
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		
		$query->joinWith(["idType"]);
		
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		
		if ( $this->date_start)  $this->date_start=$this->date_start." 00:00:00";
        if ($this->date_end)  $this->date_end=$this->date_end." 23:59:59";
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
			'id_kassa' => $this->id_kassa,
            'id_type' => $this->id_type,
			'type_costs.type' => $this->type,
            'sum' => $this->sum,
            
        ]);

        $query->andFilterWhere(['between','datetime',$this->date_start,$this->date_end]);
        $query->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
