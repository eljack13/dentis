<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cita".
 *
 * @property int $id
 * @property string|null $folio
 * @property int $paciente_id
 * @property int $servicio_id
 * @property string $inicio
 * @property string $fin
 * @property string $estado
 * @property string|null $motivo_cancelacion
 * @property string $canal
 * @property string|null $notas
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CitaHistorial[] $citaHistorials
 * @property FotoSesion[] $fotoSesions
 * @property NotificacionQueue[] $notificacionQueues
 * @property Paciente $paciente
 * @property Servicio $servicio
 */
class Cita extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ESTADO_PENDIENTE = 'PENDIENTE';
    const ESTADO_CONFIRMADA = 'CONFIRMADA';
    const ESTADO_CANCELADA_PACIENTE = 'CANCELADA_PACIENTE';
    const ESTADO_CANCELADA_DENTISTA = 'CANCELADA_DENTISTA';
    const ESTADO_NO_ASISTIO = 'NO_ASISTIO';
    const ESTADO_ATENDIDA = 'ATENDIDA';
    const CANAL_WEB = 'WEB';
    const CANAL_DENTISTA = 'DENTISTA';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cita';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['folio', 'motivo_cancelacion', 'notas'], 'default', 'value' => null],
            [['estado'], 'default', 'value' => 'PENDIENTE'],
            [['canal'], 'default', 'value' => 'WEB'],
            [['paciente_id', 'servicio_id', 'inicio', 'fin', 'created_at', 'updated_at'], 'required'],
            [['paciente_id', 'servicio_id', 'created_at', 'updated_at'], 'integer'],
            [['inicio', 'fin'], 'safe'],
            [['estado', 'motivo_cancelacion', 'canal', 'notas'], 'string'],
            [['folio'], 'string', 'max' => 20],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
            ['canal', 'in', 'range' => array_keys(self::optsCanal())],
            [['folio'], 'unique'],
            [['paciente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Paciente::class, 'targetAttribute' => ['paciente_id' => 'id']],
            [['servicio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Servicio::class, 'targetAttribute' => ['servicio_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'folio' => 'Folio',
            'paciente_id' => 'Paciente ID',
            'servicio_id' => 'Servicio ID',
            'inicio' => 'Inicio',
            'fin' => 'Fin',
            'estado' => 'Estado',
            'motivo_cancelacion' => 'Motivo Cancelacion',
            'canal' => 'Canal',
            'notas' => 'Notas',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[CitaHistorials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCitaHistorials()
    {
        return $this->hasMany(CitaHistorial::class, ['cita_id' => 'id']);
    }

    /**
     * Gets query for [[FotoSesions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFotoSesions()
    {
        return $this->hasMany(FotoSesion::class, ['cita_id' => 'id']);
    }

    /**
     * Gets query for [[NotificacionQueues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificacionQueues()
    {
        return $this->hasMany(NotificacionQueue::class, ['cita_id' => 'id']);
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

    /**
     * Gets query for [[Servicio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicio()
    {
        return $this->hasOne(Servicio::class, ['id' => 'servicio_id']);
    }


    /**
     * column estado ENUM value labels
     * @return string[]
     */
    public static function optsEstado()
    {
        return [
            self::ESTADO_PENDIENTE => 'PENDIENTE',
            self::ESTADO_CONFIRMADA => 'CONFIRMADA',
            self::ESTADO_CANCELADA_PACIENTE => 'CANCELADA_PACIENTE',
            self::ESTADO_CANCELADA_DENTISTA => 'CANCELADA_DENTISTA',
            self::ESTADO_NO_ASISTIO => 'NO_ASISTIO',
            self::ESTADO_ATENDIDA => 'ATENDIDA',
        ];
    }

    /**
     * column canal ENUM value labels
     * @return string[]
     */
    public static function optsCanal()
    {
        return [
            self::CANAL_WEB => 'WEB',
            self::CANAL_DENTISTA => 'DENTISTA',
        ];
    }

    /**
     * @return string
     */
    public function displayEstado()
    {
        return self::optsEstado()[$this->estado];
    }

    /**
     * @return bool
     */
    public function isEstadoPendiente()
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function setEstadoToPendiente()
    {
        $this->estado = self::ESTADO_PENDIENTE;
    }

    /**
     * @return bool
     */
    public function isEstadoConfirmada()
    {
        return $this->estado === self::ESTADO_CONFIRMADA;
    }

    public function setEstadoToConfirmada()
    {
        $this->estado = self::ESTADO_CONFIRMADA;
    }

    /**
     * @return bool
     */
    public function isEstadoCanceladapaciente()
    {
        return $this->estado === self::ESTADO_CANCELADA_PACIENTE;
    }

    public function setEstadoToCanceladapaciente()
    {
        $this->estado = self::ESTADO_CANCELADA_PACIENTE;
    }

    /**
     * @return bool
     */
    public function isEstadoCanceladadentista()
    {
        return $this->estado === self::ESTADO_CANCELADA_DENTISTA;
    }

    public function setEstadoToCanceladadentista()
    {
        $this->estado = self::ESTADO_CANCELADA_DENTISTA;
    }

    /**
     * @return bool
     */
    public function isEstadoNoasistio()
    {
        return $this->estado === self::ESTADO_NO_ASISTIO;
    }

    public function setEstadoToNoasistio()
    {
        $this->estado = self::ESTADO_NO_ASISTIO;
    }

    /**
     * @return bool
     */
    public function isEstadoAtendida()
    {
        return $this->estado === self::ESTADO_ATENDIDA;
    }

    public function setEstadoToAtendida()
    {
        $this->estado = self::ESTADO_ATENDIDA;
    }

    /**
     * @return string
     */
    public function displayCanal()
    {
        return self::optsCanal()[$this->canal];
    }

    /**
     * @return bool
     */
    public function isCanalWeb()
    {
        return $this->canal === self::CANAL_WEB;
    }

    public function setCanalToWeb()
    {
        $this->canal = self::CANAL_WEB;
    }

    /**
     * @return bool
     */
    public function isCanalDentista()
    {
        return $this->canal === self::CANAL_DENTISTA;
    }

    public function setCanalToDentista()
    {
        $this->canal = self::CANAL_DENTISTA;
    }
}
