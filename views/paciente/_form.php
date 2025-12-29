<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Paciente $model */
/** @var yii\widgets\ActiveForm $form */

// Registramos los estilos CSS modernos
$this->registerCss("
    :root {
        --primary: #2563eb;
        --primary-hover: #1d4ed8;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-500: #6b7280;
        --gray-700: #374151;
        --gray-900: #111827;
        --border-radius: 12px;
    }

    .paciente-form-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .form-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid var(--gray-100);
        padding: 2.5rem;
    }

    /* Section Titles */
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--gray-100);
    }
    
    .section-title i, .section-title svg {
        color: var(--primary);
    }

    .section-title.secondary {
        margin-top: 2rem;
    }

    /* Labels */
    .control-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* Inputs */
    .form-control {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        line-height: 1.5;
        color: var(--gray-900);
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid var(--gray-200);
        border-radius: 10px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: var(--primary);
        outline: 0;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    /* Textarea adjustment */
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    /* Submit Button */
    .btn-submit {
        background-color: var(--primary);
        color: white;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        width: 100%;
        transition: all 0.2s ease;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .btn-submit:hover {
        background-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    /* Error help blocks */
    .help-block {
        font-size: 0.85rem;
        color: #dc2626;
        margin-top: 0.25rem;
    }
");
?>

<div class="paciente-form-container">

    <div class="form-card">

        <?php $form = ActiveForm::begin([
            'id' => 'paciente-form',
            'options' => ['class' => 'needs-validation'],
        ]); ?>

        <div class="section-title">
            <svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Información Personal
        </div>

        <div class="row">


            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'nombre')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Ej. Juan Carlos'
                ]) ?>
            </div>

            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'apellidos')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Ej. Pérez López'
                ]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'fecha_nacimiento')->input('date', [
                    'class' => 'form-control'
                ]) ?>
            </div>

            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'status')->dropDownList(
                    [1 => 'Activo', 0 => 'Inactivo'],
                    ['prompt' => 'Seleccione estado...']
                ) ?>
            </div>
        </div>

        <div class="section-title secondary">
            <svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            Información de Contacto
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'telefono')->textInput([
                    'maxlength' => true,
                    'placeholder' => '(000) 000-0000'
                ]) ?>
            </div>

            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'email')->input('email', [
                    'maxlength' => true,
                    'placeholder' => 'correo@ejemplo.com'
                ]) ?>
            </div>
        </div>

        <div class="section-title secondary">
            <svg style="width:24px;height:24px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Notas Médicas / Observaciones
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <?= $form->field($model, 'notas')->textarea([
                    'rows' => 4,
                    'placeholder' => 'Escriba aquí antecedentes importantes, alergias o notas generales...'
                ])->label(false) ?>
            </div>
        </div>


        <div class="form-group mt-4">
            <?= Html::submitButton('
                <svg style="width:20px;height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Guardar Paciente
            ', ['class' => 'btn-submit']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>