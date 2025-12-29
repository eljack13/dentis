<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bloqueo_agenda".
 *
 * @property int $id
 * @property string $titulo
 * @property string $inicio
 * @property string $fin
 * @property string|null $motivo
 * @property int|null $created_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Usuario $createdBy
 */
class BloqueoAgenda extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bloqueo_agenda';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['motivo', 'created_by'], 'default', 'value' => null],
            [['titulo', 'inicio', 'fin', 'created_at', 'updated_at'], 'required'],
            [['inicio', 'fin'], 'safe'],
            [['motivo'], 'string'],
            [['created_by', 'created_at', 'updated_at'], 'integer'],
            [['titulo'], 'string', 'max' => 140],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'inicio' => 'Inicio',
            'fin' => 'Fin',
            'motivo' => 'Motivo',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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

}
