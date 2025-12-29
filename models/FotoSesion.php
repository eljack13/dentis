<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "foto_sesion".
 *
 * @property int $id
 * @property int $paciente_id
 * @property int|null $cita_id
 * @property string $fecha_sesion
 * @property string|null $titulo
 * @property string|null $notas
 * @property int|null $created_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Cita $cita
 * @property Usuario $createdBy
 * @property Foto[] $fotos
 * @property Paciente $paciente
 */
class FotoSesion extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foto_sesion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cita_id', 'titulo', 'notas', 'created_by'], 'default', 'value' => null],
            [['paciente_id', 'fecha_sesion', 'created_at', 'updated_at'], 'required'],
            [['paciente_id', 'cita_id', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['fecha_sesion'], 'safe'],
            [['notas'], 'string'],
            [['titulo'], 'string', 'max' => 160],
            [['cita_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cita::class, 'targetAttribute' => ['cita_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::class, 'targetAttribute' => ['created_by' => 'id']],
            [['paciente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Paciente::class, 'targetAttribute' => ['paciente_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paciente_id' => 'Paciente ID',
            'cita_id' => 'Cita ID',
            'fecha_sesion' => 'Fecha Sesion',
            'titulo' => 'Titulo',
            'notas' => 'Notas',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Cita]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCita()
    {
        return $this->hasOne(Cita::class, ['id' => 'cita_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Usuario::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Fotos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFotos()
    {
        return $this->hasMany(Foto::class, ['foto_sesion_id' => 'id']);
    }

    /**
     * Gets query for [[Paciente]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaciente()
    {
        return $this->hasOne(Paciente::class, ['id' => 'paciente_id']);
    }

}
