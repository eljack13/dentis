<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cita;

/**
 * CitaSearch represents the model behind the search form of `app\models\Cita`.
 */
class CitaSearch extends Cita
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'paciente_id', 'servicio_id', 'created_at', 'updated_at'], 'integer'],
            [['folio', 'inicio', 'fin', 'estado', 'motivo_cancelacion', 'canal', 'notas'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Cita::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'paciente_id' => $this->paciente_id,
            'servicio_id' => $this->servicio_id,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'folio', $this->folio])
            ->andFilterWhere(['like', 'estado', $this->estado])
            ->andFilterWhere(['like', 'motivo_cancelacion', $this->motivo_cancelacion])
            ->andFilterWhere(['like', 'canal', $this->canal])
            ->andFilterWhere(['like', 'notas', $this->notas]);

        return $dataProvider;
    }
}
