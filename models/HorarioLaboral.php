<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "horario_laboral".
 *
 * @property int $id
 * @property int $dia_semana
 * @property string $hora_inicio
 * @property string $hora_fin
 * @property int $activo
 * @property int $created_at
 * @property int $updated_at
 */
class HorarioLaboral extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'horario_laboral';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activo'], 'default', 'value' => 1],
            [['dia_semana', 'hora_inicio', 'hora_fin', 'created_at', 'updated_at'], 'required'],
            [['dia_semana', 'activo', 'created_at', 'updated_at'], 'integer'],
            [['hora_inicio', 'hora_fin'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dia_semana' => 'Dia Semana',
            'hora_inicio' => 'Hora Inicio',
            'hora_fin' => 'Hora Fin',
            'activo' => 'Activo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
