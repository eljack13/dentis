<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cita_historial".
 *
 * @property int $id
 * @property int $cita_id
 * @property string $accion
 * @property string|null $detalle
 * @property string|null $old_inicio
 * @property string|null $old_fin
 * @property string|null $new_inicio
 * @property string|null $new_fin
 * @property int|null $realizado_por
 * @property int $created_at
 *
 * @property Cita $cita
 * @property Usuario $realizadoPor
 */
class CitaHistorial extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ACCION_CREADA = 'CREADA';
    const ACCION_CONFIRMADA = 'CONFIRMADA';
    const ACCION_REPROGRAMADA = 'REPROGRAMADA';
    const ACCION_CANCELADA = 'CANCELADA';
    const ACCION_MARCAR_ATENDIDA = 'MARCAR_ATENDIDA';
    const ACCION_MARCAR_NO_ASISTIO = 'MARCAR_NO_ASISTIO';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cita_historial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['detalle', 'old_inicio', 'old_fin', 'new_inicio', 'new_fin', 'realizado_por'], 'default', 'value' => null],
            [['cita_id', 'accion', 'created_at'], 'required'],
            [['cita_id', 'realizado_por', 'created_at'], 'integer'],
            [['accion', 'detalle'], 'string'],
            [['old_inicio', 'old_fin', 'new_inicio', 'new_fin'], 'safe'],
            ['accion', 'in', 'range' => array_keys(self::optsAccion())],
            [['cita_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cita::class, 'targetAttribute' => ['cita_id' => 'id']],
            [['realizado_por'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::class, 'targetAttribute' => ['realizado_por' => 'id']],
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
            'accion' => 'Accion',
            'detalle' => 'Detalle',
            'old_inicio' => 'Old Inicio',
            'old_fin' => 'Old Fin',
            'new_inicio' => 'New Inicio',
            'new_fin' => 'New Fin',
            'realizado_por' => 'Realizado Por',
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
     * Gets query for [[RealizadoPor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRealizadoPor()
    {
        return $this->hasOne(Usuario::class, ['id' => 'realizado_por']);
    }


    /**
     * column accion ENUM value labels
     * @return string[]
     */
    public static function optsAccion()
    {
        return [
            self::ACCION_CREADA => 'CREADA',
            self::ACCION_CONFIRMADA => 'CONFIRMADA',
            self::ACCION_REPROGRAMADA => 'REPROGRAMADA',
            self::ACCION_CANCELADA => 'CANCELADA',
            self::ACCION_MARCAR_ATENDIDA => 'MARCAR_ATENDIDA',
            self::ACCION_MARCAR_NO_ASISTIO => 'MARCAR_NO_ASISTIO',
        ];
    }

    /**
     * @return string
     */
    public function displayAccion()
    {
        return self::optsAccion()[$this->accion];
    }

    /**
     * @return bool
     */
    public function isAccionCreada()
    {
        return $this->accion === self::ACCION_CREADA;
    }

    public function setAccionToCreada()
    {
        $this->accion = self::ACCION_CREADA;
    }

    /**
     * @return bool
     */
    public function isAccionConfirmada()
    {
        return $this->accion === self::ACCION_CONFIRMADA;
    }

    public function setAccionToConfirmada()
    {
        $this->accion = self::ACCION_CONFIRMADA;
    }

    /**
     * @return bool
     */
    public function isAccionReprogramada()
    {
        return $this->accion === self::ACCION_REPROGRAMADA;
    }

    public function setAccionToReprogramada()
    {
        $this->accion = self::ACCION_REPROGRAMADA;
    }

    /**
     * @return bool
     */
    public function isAccionCancelada()
    {
        return $this->accion === self::ACCION_CANCELADA;
    }

    public function setAccionToCancelada()
    {
        $this->accion = self::ACCION_CANCELADA;
    }

    /**
     * @return bool
     */
    public function isAccionMarcaratendida()
    {
        return $this->accion === self::ACCION_MARCAR_ATENDIDA;
    }

    public function setAccionToMarcaratendida()
    {
        $this->accion = self::ACCION_MARCAR_ATENDIDA;
    }

    /**
     * @return bool
     */
    public function isAccionMarcarnoasistio()
    {
        return $this->accion === self::ACCION_MARCAR_NO_ASISTIO;
    }

    public function setAccionToMarcarnoasistio()
    {
        $this->accion = self::ACCION_MARCAR_NO_ASISTIO;
    }
}
