<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuario".
 *
 * @property int $id
 * @property string $rol
 * @property string $nombre
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $password_hash
 * @property string|null $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property BloqueoAgenda[] $bloqueoAgendas
 * @property CitaHistorial[] $citaHistorials
 * @property FotoSesion[] $fotoSesions
 * @property Paciente[] $pacientes
 */
class Usuario extends ActiveRecord implements IdentityInterface

{

    /**
     * ENUM field values
     */
    const ROL_DENTISTA = 'DENTISTA';
    const ROL_PACIENTE = 'PACIENTE';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['telefono', 'email', 'password_hash', 'auth_key'], 'default', 'value' => null],
            [['rol'], 'default', 'value' => 'PACIENTE'],
            [['status'], 'default', 'value' => 1],
            [['rol'], 'string'],
            [['nombre'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['nombre', 'email'], 'string', 'max' => 120],
            [['telefono'], 'string', 'max' => 30],
            [['password_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            ['rol', 'in', 'range' => array_keys(self::optsRol())],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rol' => 'Rol',
            'nombre' => 'Nombre',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[BloqueoAgendas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBloqueoAgendas()
    {
        return $this->hasMany(BloqueoAgenda::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[CitaHistorials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCitaHistorials()
    {
        return $this->hasMany(CitaHistorial::class, ['realizado_por' => 'id']);
    }

    /**
     * Gets query for [[FotoSesions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFotoSesions()
    {
        return $this->hasMany(FotoSesion::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[Pacientes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPacientes()
    {
        return $this->hasMany(Paciente::class, ['usuario_id' => 'id']);
    }


    /**
     * column rol ENUM value labels
     * @return string[]
     */
    public static function optsRol()
    {
        return [
            self::ROL_DENTISTA => 'DENTISTA',
            self::ROL_PACIENTE => 'PACIENTE',
        ];
    }

    /**
     * @return string
     */
    public function displayRol()
    {
        return self::optsRol()[$this->rol];
    }

    /**
     * @return bool
     */
    public function isRolDentista()
    {
        return $this->rol === self::ROL_DENTISTA;
    }

    public function setRolToDentista()
    {
        $this->rol = self::ROL_DENTISTA;
    }

    /**
     * @return bool
     */
    public function isRolPaciente()
    {
        return $this->rol === self::ROL_PACIENTE;
    }

    public function setRolToPaciente()
    {
        $this->rol = self::ROL_PACIENTE;
    }

    // =========================
    // AUTH / LOGIN HELPERS
    // =========================

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $now = time();
        if ($this->isNewRecord) {
            if (empty($this->created_at)) {
                $this->created_at = $now;
            }
            if (empty($this->auth_key)) {
                $this->auth_key = Yii::$app->security->generateRandomString(32);
            }
        }

        $this->updated_at = $now;
        return true;
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        if (empty($this->password_hash)) {
            return false;
        }
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => 1]);
    }

    public static function findByTelefono($telefono)
    {
        return static::findOne(['telefono' => $telefono, 'status' => 1]);
    }

    public function esDentista(): bool
    {
        return $this->rol === self::ROL_DENTISTA;
    }

    // =========================
    // IdentityInterface
    // =========================

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => 1]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // no se usa por ahora
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
}
