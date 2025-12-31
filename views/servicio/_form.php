<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Servicio $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="servicio-form">

    <?php $form = ActiveForm::begin([
        'id' => 'servicio-form',
        'options' => ['class' => 'needs-validation'],
    ]); ?>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 text-dark fw-bold">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Datos del Tratamiento
            </h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">

                <div class="col-lg-8">
                    <div class="mb-4">
                        <?= $form->field($model, 'nombre', [
                            'inputOptions' => ['class' => 'form-control form-control-lg', 'placeholder' => 'Ej. Limpieza Dental Profunda']
                        ])->textInput(['maxlength' => true])->label('Nombre del Servicio', ['class' => 'form-label fw-bold text-secondary small text-uppercase']) ?>
                    </div>

                    <div class="mb-2">
                        <?= $form->field($model, 'descripcion')->textarea([
                            'rows' => 6,
                            'class' => 'form-control',
                            'placeholder' => 'Describe en qué consiste el procedimiento...'
                        ])->label('Descripción Detallada', ['class' => 'form-label fw-bold text-secondary small text-uppercase']) ?>
                        <div class="form-text text-muted">Esta descripción aparecerá en la ficha del paciente o presupuesto.</div>
                    </div>
                </div>

                <div class="col-lg-4 bg-light rounded-3 p-4">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Configuración</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Duración Estimada</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-clock"></i></span>
                            <?= $form->field($model, 'duracion_min', ['template' => '{input}{error}', 'options' => ['tag' => false]])
                                ->textInput(['type' => 'number', 'class' => 'form-control border-start-0', 'placeholder' => '0']) ?>
                            <span class="input-group-text bg-white">min</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tiempo de Buffer</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-hourglass-split"></i></span>
                            <?= $form->field($model, 'buffer_min', ['template' => '{input}{error}', 'options' => ['tag' => false]])
                                ->textInput(['type' => 'number', 'class' => 'form-control border-start-0', 'placeholder' => '0']) ?>
                            <span class="input-group-text bg-white">min</span>
                        </div>
                        <div class="form-text" style="font-size: 0.75rem;">Tiempo necesario limpieza entre pacientes.</div>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'color', [
                            'template' => "{label}\n{input}\n{error}"
                        ])->input('color', [
                            'class' => 'form-control form-control-color w-100',
                            'title' => 'Elige un color para la agenda',
                            'value' => $model->isNewRecord ? '#3b82f6' : $model->color // Azul por defecto
                        ])->label('Color en Agenda', ['class' => 'form-label small fw-bold text-secondary']) ?>
                    </div>

                    <hr class="border-secondary opacity-25">

                    <div class="form-check form-switch custom-switch ps-0">
                        <?= $form->field($model, 'activo', [
                            'template' => "<div class='d-flex justify-content-between align-items-center'><label class='form-check-label fw-bold' for='servicio-activo'>¿Servicio Activo?</label>\n{input}</div>\n{error}"
                        ])->checkbox([
                            'class' => 'form-check-input ms-3',
                            'style' => 'width: 3em; height: 1.5em; cursor: pointer;',
                            'id' => 'servicio-activo'
                        ], false) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white py-3 px-4 d-flex justify-content-end gap-2 border-top">
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary rounded-pill px-4']) ?>
            <?= Html::submitButton(
                $model->isNewRecord ? '<i class="bi bi-save me-2"></i>Guardar Servicio' : '<i class="bi bi-check-lg me-2"></i>Actualizar',
                ['class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-medium']
            ) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    /* Input Groups más limpios */
    .input-group-text {
        color: #64748b;
        border-color: #dee2e6;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
    }

    /* Input Color Personalizado */
    .form-control-color {
        border: 1px solid #dee2e6;
        padding: 0.375rem;
        border-radius: 0.5rem;
        cursor: pointer;
    }

    /* Labels mayúsculas sutiles */
    .form-label.small {
        letter-spacing: 0.5px;
        font-size: 0.75rem;
    }

    /* Quitar estilos por defecto feos del checkbox de yii */
    .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    /* Ajuste de inputs numéricos */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        opacity: 1;
    }
</style>