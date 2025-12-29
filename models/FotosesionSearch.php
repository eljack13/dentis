<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Fotosesion;

/**
 * FotosesionSearch represents the model behind the search form of `app\models\Fotosesion`.
 */
class FotosesionSearch extends Fotosesion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'paciente_id', 'cita_id', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['fecha_sesion', 'titulo', 'notas'], 'safe'],
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
        $query = Fotosesion::find();

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
            'cita_id' => $this->cita_id,
            'fecha_sesion' => $this->fecha_sesion,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'titulo', $this->titulo])
            ->andFilterWhere(['like', 'notas', $this->notas]);

        return $dataProvider;
    }
}
