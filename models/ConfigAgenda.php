<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "config_agenda".
 *
 * @property int $id
 * @property int $slot_min
 * @property int $min_anticipacion_horas
 * @property int $max_anticipacion_dias
 * @property int $permitir_domingo
 * @property string $zona_horaria
 * @property int $created_at
 * @property int $updated_at
 */
class ConfigAgenda extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_agenda';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slot_min'], 'default', 'value' => 10],
            [['min_anticipacion_horas'], 'default', 'value' => 2],
            [['max_anticipacion_dias'], 'default', 'value' => 60],
            [['permitir_domingo'], 'default', 'value' => 0],
            [['zona_horaria'], 'default', 'value' => 'America/Monterrey'],
            [['slot_min', 'min_anticipacion_horas', 'max_anticipacion_dias', 'permitir_domingo', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['zona_horaria'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slot_min' => 'Slot Min',
            'min_anticipacion_horas' => 'Min Anticipacion Horas',
            'max_anticipacion_dias' => 'Max Anticipacion Dias',
            'permitir_domingo' => 'Permitir Domingo',
            'zona_horaria' => 'Zona Horaria',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
