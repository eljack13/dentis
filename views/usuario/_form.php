<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Usuario $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="usuario-form">

    <?php $form = ActiveForm::begin([
        'id' => 'usuario-form',
        'options' => ['class' => 'needs-validation'],
    ]); ?>

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 text-dark fw-bold">
                <i class="bi bi-person-badge me-2 text-primary"></i>Información del Usuario
            </h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">

                <div class="col-lg-7 border-end-lg">
                    <h6 class="text-muted text-uppercase small fw-bold mb-3 ls-1">Datos Personales</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nombre Completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                            <?= $form->field($model, 'nombre', ['template' => '{input}{error}', 'options' => ['tag' => false]])
                                ->textInput(['class' => 'form-control border-start-0', 'placeholder' => 'Ej. Dr. Juan Pérez']) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                <?= $form->field($model, 'email', ['template' => '{input}{error}', 'options' => ['tag' => false]])
                                    ->textInput(['type' => 'email', 'class' => 'form-control border-start-0', 'placeholder' => 'usuario@clinica.com']) ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Teléfono Móvil</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-phone"></i></span>
                                <?= $form->field($model, 'telefono', ['template' => '{input}{error}', 'options' => ['tag' => false]])
                                    ->textInput(['class' => 'form-control border-start-0', 'placeholder' => '(000) 000-0000']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 ps-lg-4">
                    <h6 class="text-muted text-uppercase small fw-bold mb-3 ls-1">Configuración de Cuenta</h6>

                    <div class="mb-3">
                        <?= $form->field($model, 'rol')->dropDownList(
                            [
                                'DENTISTA' => 'Dentista / Doctor',
                                'RECEPCION' => 'Recepción / Asistente',
                                'PACIENTE' => 'Paciente',
                                'ADMIN' => 'Administrador',
                            ],
                            ['class' => 'form-select', 'prompt' => 'Seleccione un rol...']
                        )->label('Rol en el Sistema', ['class' => 'form-label small fw-bold text-secondary']) ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">
                            <?= $model->isNewRecord ? 'Contraseña' : 'Nueva Contraseña (Opcional)' ?>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-key"></i>
                            </span>

                            <?= $form->field($model, 'password_hash', [
                                'template' => '{input}{error}',
                                'options' => ['tag' => false] // Importante para que no rompa el input-group
                            ])->passwordInput([
                                'class' => 'form-control border-start-0 border-end-0',
                                'placeholder' => '••••••••',
                                'value' => '', // Siempre vacío por seguridad al editar
                                'id' => 'txtPassword' // ID específico para el JS
                            ]) ?>

                            <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="btnTogglePassword" tabindex="-1">
                                <i class="bi bi-eye" id="iconEye"></i>
                            </button>
                        </div>

                        <?php if (!$model->isNewRecord): ?>
                            <div class="form-text small">Dejar en blanco para mantener la contraseña actual.</div>
                        <?php endif; ?>
                    </div>

                    <hr class="border-secondary opacity-25 my-4">

                    <div class="bg-light rounded-3 p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block fw-bold text-dark small">Estado de la Cuenta</span>
                            <span class="d-block text-muted" style="font-size: 0.7rem;">Permitir acceso al sistema</span>
                        </div>
                        <div class="form-check form-switch custom-switch">
                            <?= $form->field($model, 'status', ['template' => '{input}'])
                                ->checkbox(['class' => 'form-check-input', 'style' => 'width: 3em; height: 1.5em; cursor: pointer;'], false) ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card-footer bg-white py-3 px-4 d-flex justify-content-end gap-2 border-top">
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary rounded-pill px-4']) ?>
            <?= Html::submitButton(
                $model->isNewRecord ? '<i class="bi bi-person-plus me-2"></i>Crear Usuario' : '<i class="bi bi-save me-2"></i>Guardar Cambios',
                ['class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-medium']
            ) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    .ls-1 {
        letter-spacing: 1px;
    }

    .input-group-text {
        color: #64748b;
        background-color: #f8fafc;
        border-color: #dee2e6;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
    }

    .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    /* Borde separador en pantallas grandes */
    @media (min-width: 992px) {
        .border-end-lg {
            border-right: 1px solid #f1f5f9;
            padding-right: 1.5rem;
        }
    }
</style>

<?php
$js = <<<JS
    document.getElementById('btnTogglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('txtPassword');
        const icon = document.getElementById('iconEye');

        // Cambiar tipo de input
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash'); // Cambia icono a "ojo tachado"
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye'); // Regresa icono a "ojo normal"
        }
    });
JS;
$this->registerJs($js);
?>