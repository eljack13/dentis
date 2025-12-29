<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paciente".
 *
 * @property int $id
 * @property int|null $usuario_id
 * @property string $nombre
 * @property string|null $apellidos
 * @property string $telefono
 * @property string|null $email
 * @property string|null $fecha_nacimiento
 * @property string|null $notas
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Cita[] $citas
 * @property FotoSesion[] $fotoSesions
 * @property NotificacionQueue[] $notificacionQueues
 * @property Usuario $usuario
 */
class Paciente extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paciente';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'apellidos', 'email', 'fecha_nacimiento', 'notas'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['usuario_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['nombre', 'telefono', 'created_at', 'updated_at'], 'required'],
            [['fecha_nacimiento'], 'safe'],
            [['notas'], 'string'],
            [['nombre', 'email'], 'string', 'max' => 120],
            [['apellidos'], 'string', 'max' => 160],
            [['telefono'], 'string', 'max' => 30],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::class, 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'notas' => 'Notas',
            'status' => 'Status',
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
        return $this->hasMany(Cita::class, ['paciente_id' => 'id']);
    }

    /**
     * Gets query for [[FotoSesions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFotoSesions()
    {
        return $this->hasMany(FotoSesion::class, ['paciente_id' => 'id']);
    }

    /**
     * Gets query for [[NotificacionQueues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificacionQueues()
    {
        return $this->hasMany(NotificacionQueue::class, ['paciente_id' => 'id']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::class, ['id' => 'usuario_id']);
    }

}
