<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servicio".
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property int $duracion_min
 * @property int $buffer_min
 * @property string|null $color
 * @property int $activo
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Cita[] $citas
 */
class Servicio extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'color'], 'default', 'value' => null],
            [['buffer_min'], 'default', 'value' => 0],
            [['activo'], 'default', 'value' => 1],
            [['nombre', 'duracion_min', 'created_at', 'updated_at'], 'required'],
            [['descripcion'], 'string'],
            [['duracion_min', 'buffer_min', 'activo', 'created_at', 'updated_at'], 'integer'],
            [['nombre'], 'string', 'max' => 120],
            [['color'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'duracion_min' => 'Duracion Min',
            'buffer_min' => 'Buffer Min',
            'color' => 'Color',
            'activo' => 'Activo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Citas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCitas()
    {
        return $this->hasMany(Cita::class, ['servicio_id' => 'id']);
    }

}
