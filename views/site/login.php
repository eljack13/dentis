<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

$this->title = 'Iniciar sesión';
?>
<div class="site-login" style="max-width:420px;margin:40px auto;">
    <h2 class="mb-3"><?= Html::encode($this->title) ?></h2>
    <p class="text-muted">Acceso para el sistema Dentis.</p>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

    <?= $form->field($model, 'email')->textInput([
        'autofocus' => true,
        'placeholder' => 'correo@dominio.com'
    ])->label('Correo') ?>

    <?= $form->field($model, 'password')->passwordInput([
        'placeholder' => '********'
    ])->label('Contraseña') ?>

    <?= $form->field($model, 'rememberMe')->checkbox()->label('Recordarme') ?>

    <div class="mt-3">
        <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>