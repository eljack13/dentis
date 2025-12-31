<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Servicio $model */

// Usamos el nombre del servicio como título, es mucho más amigable que el ID
$this->title = $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Servicios Clínicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="servicio-view container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-0"><?= Html::encode($this->title) ?></h1>
            <span class="text-muted small">Ficha técnica del tratamiento</span>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Volver', ['index'], ['class' => 'btn btn-outline-secondary rounded-pill px-3']) ?>
            <?= Html::a('<i class="bi bi-pencil-square"></i> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary rounded-pill px-4']) ?>
            <?= Html::a('<i class="bi bi-trash"></i> Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger rounded-pill px-3',
                'data' => [
                    'confirm' => '¿Estás seguro de que quieres eliminar este servicio de forma permanente?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-top: 5px solid <?= $model->color ?> !important;">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-clipboard2-pulse fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Descripción del Procedimiento</h5>
                                <small class="text-muted">ID Sistema: #<?= $model->id ?></small>
                            </div>
                        </div>

                        <?php if ($model->activo): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Activo
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">
                                <i class="bi bi-dash-circle-fill me-1"></i> Inactivo
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="bg-light rounded-3 p-4 mb-3">
                        <p class="mb-0 text-secondary" style="line-height: 1.7;">
                            <?= !empty($model->descripcion) ? nl2br(Html::encode($model->descripcion)) : '<em class="text-muted">No se ha proporcionado una descripción detallada para este servicio.</em>' ?>
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 border-bottom">
                    Configuración Agenda
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">

                        <li class="list-group-item p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-stopwatch text-muted me-3 fs-5"></i>
                                <div>
                                    <span class="d-block small text-muted fw-bold text-uppercase">Duración</span>
                                    <span class="fw-medium text-dark">Tiempo de consulta</span>
                                </div>
                            </div>
                            <span class="fs-5 fw-bold text-dark"><?= $model->duracion_min ?> <small class="fs-6 fw-normal text-muted">min</small></span>
                        </li>

                        <li class="list-group-item p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hourglass-split text-muted me-3 fs-5"></i>
                                <div>
                                    <span class="d-block small text-muted fw-bold text-uppercase">Buffer</span>
                                    <span class="fw-medium text-dark">Limpieza post-consulta</span>
                                </div>
                            </div>
                            <span class="fs-5 fw-bold text-dark"><?= $model->buffer_min ?> <small class="fs-6 fw-normal text-muted">min</small></span>
                        </li>

                        <li class="list-group-item p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-palette text-muted me-3 fs-5"></i>
                                <div>
                                    <span class="d-block small text-muted fw-bold text-uppercase">Identificador</span>
                                    <span class="fw-medium text-dark">Color en Agenda</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <code class="text-muted me-2"><?= $model->color ?></code>
                                <div class="rounded-circle border shadow-sm" style="width: 32px; height: 32px; background-color: <?= $model->color ?>;"></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="card-footer bg-light text-muted small py-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Creado:</span>
                        <span><?= Yii::$app->formatter->asDate($model->created_at, 'long') ?></span>
                    </div>
                    <?php if ($model->updated_at): ?>
                        <div class="d-flex justify-content-between">
                            <span>Última act.:</span>
                            <span><?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>