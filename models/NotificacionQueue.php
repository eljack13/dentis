<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notificacion_queue".
 *
 * @property int $id
 * @property int|null $cita_id
 * @property int|null $paciente_id
 * @property string $canal
 * @property string $tipo
 * @property string $mensaje
 * @property string $scheduled_at
 * @property string $status
 * @property string|null $error_msg
 * @property int $intentos
 * @property string|null $sent_at
 * @property int $created_at
 *
 * @property Cita $cita
 * @property Paciente $paciente
 */
class NotificacionQueue extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const CANAL_WHATSAPP = 'WHATSAPP';
    const CANAL_EMAIL = 'EMAIL';
    const TIPO_CONFIRMACION = 'CONFIRMACION';
    const TIPO_RECORDATORIO_24H = 'RECORDATORIO_24H';
    const TIPO_RECORDATORIO_2H = 'RECORDATORIO_2H';
    const TIPO_CANCELACION = 'CANCELACION';
    const TIPO_REPROGRAMACION = 'REPROGRAMACION';
    const STATUS_PENDIENTE = 'PENDIENTE';
    const STATUS_ENVIADA = 'ENVIADA';
    const STATUS_ERROR = 'ERROR';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notificacion_queue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cita_id', 'paciente_id', 'error_msg', 'sent_at'], 'default', 'value' => null],
            [['canal'], 'default', 'value' => 'WHATSAPP'],
            [['status'], 'default', 'value' => 'PENDIENTE'],
            [['intentos'], 'default', 'value' => 0],
            [['cita_id', 'paciente_id', 'intentos', 'created_at'], 'integer'],
            [['canal', 'tipo', 'mensaje', 'status', 'error_msg'], 'string'],
            [['tipo', 'mensaje', 'scheduled_at', 'created_at'], 'required'],
            [['scheduled_at', 'sent_at'], 'safe'],
            ['canal', 'in', 'range' => array_keys(self::optsCanal())],
            ['tipo', 'in', 'range' => array_keys(self::optsTipo())],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['cita_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cita::class, 'targetAttribute' => ['cita_id' => 'id']],
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
            'cita_id' => 'Cita ID',
            'paciente_id' => 'Paciente ID',
            'canal' => 'Canal',
            'tipo' => 'Tipo',
            'mensaje' => 'Mensaje',
            'scheduled_at' => 'Scheduled At',
            'status' => 'Status',
            'error_msg' => 'Error Msg',
            'intentos' => 'Intentos',
            'sent_at' => 'Sent At',
            'created_at' => 'Created At',
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
     * Gets query for [[Paciente]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaciente()
    {
        return $this->hasOne(Paciente::class, ['id' => 'paciente_id']);
    }


    /**
     * column canal ENUM value labels
     * @return string[]
     */
    public static function optsCanal()
    {
        return [
            self::CANAL_WHATSAPP => 'WHATSAPP',
            self::CANAL_EMAIL => 'EMAIL',
        ];
    }

    /**
     * column tipo ENUM value labels
     * @return string[]
     */
    public static function optsTipo()
    {
        return [
            self::TIPO_CONFIRMACION => 'CONFIRMACION',
            self::TIPO_RECORDATORIO_24H => 'RECORDATORIO_24H',
            self::TIPO_RECORDATORIO_2H => 'RECORDATORIO_2H',
            self::TIPO_CANCELACION => 'CANCELACION',
            self::TIPO_REPROGRAMACION => 'REPROGRAMACION',
        ];
    }

    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDIENTE => 'PENDIENTE',
            self::STATUS_ENVIADA => 'ENVIADA',
            self::STATUS_ERROR => 'ERROR',
        ];
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
    public function isCanalWhatsapp()
    {
        return $this->canal === self::CANAL_WHATSAPP;
    }

    public function setCanalToWhatsapp()
    {
        $this->canal = self::CANAL_WHATSAPP;
    }

    /**
     * @return bool
     */
    public function isCanalEmail()
    {
        return $this->canal === self::CANAL_EMAIL;
    }

    public function setCanalToEmail()
    {
        $this->canal = self::CANAL_EMAIL;
    }

    /**
     * @return string
     */
    public function displayTipo()
    {
        return self::optsTipo()[$this->tipo];
    }

    /**
     * @return bool
     */
    public function isTipoConfirmacion()
    {
        return $this->tipo === self::TIPO_CONFIRMACION;
    }

    public function setTipoToConfirmacion()
    {
        $this->tipo = self::TIPO_CONFIRMACION;
    }

    /**
     * @return bool
     */
    public function isTipoRecordatorio24h()
    {
        return $this->tipo === self::TIPO_RECORDATORIO_24H;
    }

    public function setTipoToRecordatorio24h()
    {
        $this->tipo = self::TIPO_RECORDATORIO_24H;
    }

    /**
     * @return bool
     */
    public function isTipoRecordatorio2h()
    {
        return $this->tipo === self::TIPO_RECORDATORIO_2H;
    }

    public function setTipoToRecordatorio2h()
    {
        $this->tipo = self::TIPO_RECORDATORIO_2H;
    }

    /**
     * @return bool
     */
    public function isTipoCancelacion()
    {
        return $this->tipo === self::TIPO_CANCELACION;
    }

    public function setTipoToCancelacion()
    {
        $this->tipo = self::TIPO_CANCELACION;
    }

    /**
     * @return bool
     */
    public function isTipoReprogramacion()
    {
        return $this->tipo === self::TIPO_REPROGRAMACION;
    }

    public function setTipoToReprogramacion()
    {
        $this->tipo = self::TIPO_REPROGRAMACION;
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusPendiente()
    {
        return $this->status === self::STATUS_PENDIENTE;
    }

    public function setStatusToPendiente()
    {
        $this->status = self::STATUS_PENDIENTE;
    }

    /**
     * @return bool
     */
    public function isStatusEnviada()
    {
        return $this->status === self::STATUS_ENVIADA;
    }

    public function setStatusToEnviada()
    {
        $this->status = self::STATUS_ENVIADA;
    }

    /**
     * @return bool
     */
    public function isStatusError()
    {
        return $this->status === self::STATUS_ERROR;
    }

    public function setStatusToError()
    {
        $this->status = self::STATUS_ERROR;
    }
}
