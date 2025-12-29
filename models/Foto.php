<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "foto".
 *
 * @property int $id
 * @property int $foto_sesion_id
 * @property string $archivo
 * @property string|null $mime
 * @property int|null $size_bytes
 * @property int|null $ancho
 * @property int|null $alto
 * @property string|null $etiqueta
 * @property int $orden
 * @property int $created_at
 *
 * @property FotoSesion $fotoSesion
 */
class Foto extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mime', 'size_bytes', 'ancho', 'alto', 'etiqueta'], 'default', 'value' => null],
            [['orden'], 'default', 'value' => 1],
            [['foto_sesion_id', 'archivo', 'created_at'], 'required'],
            [['foto_sesion_id', 'size_bytes', 'ancho', 'alto', 'orden', 'created_at'], 'integer'],
            [['archivo'], 'string', 'max' => 255],
            [['mime', 'etiqueta'], 'string', 'max' => 60],
            [['foto_sesion_id'], 'exist', 'skipOnError' => true, 'targetClass' => FotoSesion::class, 'targetAttribute' => ['foto_sesion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'foto_sesion_id' => 'Foto Sesion ID',
            'archivo' => 'Archivo',
            'mime' => 'Mime',
            'size_bytes' => 'Size Bytes',
            'ancho' => 'Ancho',
            'alto' => 'Alto',
            'etiqueta' => 'Etiqueta',
            'orden' => 'Orden',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[FotoSesion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFotoSesion()
    {
        return $this->hasOne(FotoSesion::class, ['id' => 'foto_sesion_id']);
    }

}
