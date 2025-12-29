<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Cita $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cita-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'folio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'paciente_id')->textInput() ?>

    <?= $form->field($model, 'servicio_id')->textInput() ?>

    <?= $form->field($model, 'inicio')->textInput() ?>

    <?= $form->field($model, 'fin')->textInput() ?>

    <?= $form->field($model, 'estado')->dropDownList([ 'PENDIENTE' => 'PENDIENTE', 'CONFIRMADA' => 'CONFIRMADA', 'CANCELADA_PACIENTE' => 'CANCELADA PACIENTE', 'CANCELADA_DENTISTA' => 'CANCELADA DENTISTA', 'NO_ASISTIO' => 'NO ASISTIO', 'ATENDIDA' => 'ATENDIDA', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'motivo_cancelacion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'canal')->dropDownList([ 'WEB' => 'WEB', 'DENTISTA' => 'DENTISTA', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'notas')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
