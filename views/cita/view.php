<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Cita $model */

$this->title = 'Cita #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Citas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);

// Helpers
$fmt = Yii::$app->formatter;

// Relación (si existe)
$pacienteNombre = $model->paciente ? $model->paciente->nombre : null;
$servicioNombre = $model->servicio ? $model->servicio->nombre : null;

// Badges por estado
$estado = $model->estado ?: 'PENDIENTE';
$estadoLabelMap = [
    'PENDIENTE' => 'Pendiente',
    'CONFIRMADA' => 'Confirmada',
    'CANCELADA_PACIENTE' => 'Cancelada (Paciente)',
    'CANCELADA_DENTISTA' => 'Cancelada (Dentista)',
    'NO_ASISTIO' => 'No asistió',
    'ATENDIDA' => 'Atendida',
];
$estadoLabel = $estadoLabelMap[$estado] ?? $estado;

// Colores tipo “medical”
$estadoClassMap = [
    'PENDIENTE' => 'badge-soft-warning',
    'CONFIRMADA' => 'badge-soft-primary',
    'CANCELADA_PACIENTE' => 'badge-soft-danger',
    'CANCELADA_DENTISTA' => 'badge-soft-danger',
    'NO_ASISTIO' => 'badge-soft-dark',
    'ATENDIDA' => 'badge-soft-success',
];
$estadoClass = $estadoClassMap[$estado] ?? 'badge-soft-secondary';

// Canal
$canal = $model->canal ?: 'WEB';
$canalLabelMap = ['WEB' => 'Web', 'DENTISTA' => 'Dentista'];
$canalLabel = $canalLabelMap[$canal] ?? $canal;

// Duración estimada en minutos
$duracionMin = null;
try {
    if (!empty($model->inicio) && !empty($model->fin)) {
        $ini = strtotime($model->inicio);
        $fin = strtotime($model->fin);
        if ($ini && $fin && $fin >= $ini) {
            $duracionMin = (int) round(($fin - $ini) / 60);
        }
    }
} catch (\Throwable $e) {
}

// Formatos
$inicioFmt = $model->inicio ? $fmt->asDatetime($model->inicio, 'php:D d/m/Y H:i') : '-';
$finFmt    = $model->fin ? $fmt->asDatetime($model->fin, 'php:D d/m/Y H:i') : '-';

$createdFmt = $model->created_at ? $fmt->asDatetime($model->created_at, 'php:d/m/Y H:i') : '-';
$updatedFmt = $model->updated_at ? $fmt->asDatetime($model->updated_at, 'php:d/m/Y H:i') : '-';

$createdAgo = $model->created_at ? $fmt->asRelativeTime($model->created_at) : null;
$updatedAgo = $model->updated_at ? $fmt->asRelativeTime($model->updated_at) : null;

$this->registerCss(<<<CSS
/* ====== VIEW PRO (Dentis) ====== */
.dv-wrap{max-width:1100px;margin:0 auto;}
.page-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px;}
.page-title{margin:0;font-weight:900;letter-spacing:-.02em;}
.page-sub{color:#6b7280;margin-top:6px;}
.head-actions{display:flex;gap:10px;flex-wrap:wrap;}
.btn-pro{border-radius:14px;font-weight:800;padding:.55rem .85rem;border:1px solid rgba(0,0,0,.12);}
.btn-pro-primary{background:#111827;color:#fff;border-color:#111827;}
.btn-pro-primary:hover{filter:brightness(.95);}
.btn-pro-soft{background:rgba(0,0,0,.04);color:#111827;}
.btn-pro-danger{background:#dc2626;color:#fff;border-color:#dc2626;}
.btn-pro-danger:hover{filter:brightness(.95);}

.grid{display:grid;grid-template-columns:1.2fr .8fr;gap:16px;}
@media (max-width: 992px){.grid{grid-template-columns:1fr;}}

.card-pro{background:#fff;border-radius:18px;border:1px solid rgba(0,0,0,.06);box-shadow:0 14px 34px rgba(0,0,0,.06);overflow:hidden;}
.card-head{padding:14px 16px;border-bottom:1px solid rgba(0,0,0,.06);display:flex;align-items:center;justify-content:space-between;gap:12px;}
.card-title{margin:0;font-weight:900;letter-spacing:-.01em;font-size:1.05rem;}
.card-body{padding:16px;}

.chips{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
.badge-soft{display:inline-flex;align-items:center;gap:8px;padding:7px 10px;border-radius:999px;font-weight:900;font-size:.82rem;border:1px solid rgba(0,0,0,.10);}
.badge-soft-primary{background:rgba(59,130,246,.10);color:#1d4ed8;border-color:rgba(59,130,246,.20);}
.badge-soft-success{background:rgba(34,197,94,.12);color:#15803d;border-color:rgba(34,197,94,.25);}
.badge-soft-warning{background:rgba(245,158,11,.14);color:#92400e;border-color:rgba(245,158,11,.25);}
.badge-soft-danger{background:rgba(239,68,68,.12);color:#b91c1c;border-color:rgba(239,68,68,.25);}
.badge-soft-dark{background:rgba(17,24,39,.08);color:#111827;border-color:rgba(17,24,39,.18);}
.badge-soft-secondary{background:rgba(107,114,128,.10);color:#374151;border-color:rgba(107,114,128,.18);}

.kpi{display:grid;grid-template-columns:repeat(3, 1fr);gap:12px;margin-top:10px;}
@media (max-width: 768px){.kpi{grid-template-columns:1fr;}}
.kpi-item{border:1px solid rgba(0,0,0,.06);background:rgba(0,0,0,.02);border-radius:16px;padding:12px;}
.kpi-label{color:#6b7280;font-weight:800;font-size:.85rem;}
.kpi-value{margin-top:4px;font-weight:950;letter-spacing:-.02em;font-size:1.05rem;color:#111827;}

.note-box{border:1px dashed rgba(0,0,0,.18);background:rgba(0,0,0,.02);border-radius:16px;padding:12px;}
.note-title{font-weight:900;margin-bottom:8px;}
.note-muted{color:#6b7280;font-size:.9rem;}

.detailview.table{margin:0;}
.detailview th{width:34%;color:#6b7280;font-weight:900;}
.detailview td{font-weight:800;color:#111827;}
CSS);
?>

<div class="dv-wrap">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <div class="page-sub">
                Revisa la información completa de la cita, su estado y notas clínicas.
            </div>

            <div class="chips" style="margin-top:10px;">
                <span class="badge-soft <?= Html::encode($estadoClass) ?>">
                    Estado: <?= Html::encode($estadoLabel) ?>
                </span>
                <span class="badge-soft badge-soft-secondary">
                    Canal: <?= Html::encode($canalLabel) ?>
                </span>
                <?php if (!empty($model->folio)): ?>
                    <span class="badge-soft badge-soft-secondary">
                        Folio: <?= Html::encode($model->folio) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="kpi">
                <div class="kpi-item">
                    <div class="kpi-label">Inicio</div>
                    <div class="kpi-value"><?= Html::encode($inicioFmt) ?></div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">Fin</div>
                    <div class="kpi-value"><?= Html::encode($finFmt) ?></div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">Duración</div>
                    <div class="kpi-value"><?= $duracionMin !== null ? Html::encode($duracionMin . ' min') : '-' ?></div>
                </div>
            </div>
        </div>

        <div class="head-actions">
            <?= Html::a('← Volver', ['index'], ['class' => 'btn btn-pro btn-pro-soft']) ?>
            <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-pro btn-pro-primary']) ?>
            <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-pro btn-pro-danger',
                'data' => [
                    'confirm' => '¿Seguro que deseas eliminar esta cita? Esta acción no se puede deshacer.',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="grid">
        <!-- LEFT: RESUMEN -->
        <div class="card-pro">
            <div class="card-head">
                <h3 class="card-title">Resumen clínico</h3>
                <div class="chips">
                    <?php if ($model->paciente_id): ?>
                        <span class="badge-soft badge-soft-secondary">Paciente ID: <?= Html::encode($model->paciente_id) ?></span>
                    <?php endif; ?>
                    <?php if ($model->servicio_id): ?>
                        <span class="badge-soft badge-soft-secondary">Servicio ID: <?= Html::encode($model->servicio_id) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="note-box" style="margin-bottom:12px;">
                    <div class="note-title">Paciente</div>
                    <div>
                        <?= $pacienteNombre ? Html::encode($pacienteNombre) : '<span class="note-muted">Sin paciente asociado</span>' ?>
                    </div>
                </div>

                <div class="note-box" style="margin-bottom:12px;">
                    <div class="note-title">Servicio</div>
                    <div>
                        <?= $servicioNombre ? Html::encode($servicioNombre) : '<span class="note-muted">Sin servicio asociado</span>' ?>
                    </div>
                </div>

                <?php if (!empty($model->notas)): ?>
                    <div class="note-box" style="margin-bottom:12px;">
                        <div class="note-title">Notas</div>
                        <div><?= nl2br(Html::encode($model->notas)) ?></div>
                    </div>
                <?php else: ?>
                    <div class="note-box" style="margin-bottom:12px;">
                        <div class="note-title">Notas</div>
                        <div class="note-muted">Sin notas registradas.</div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($model->motivo_cancelacion)): ?>
                    <div class="note-box">
                        <div class="note-title">Motivo de cancelación</div>
                        <div><?= nl2br(Html::encode($model->motivo_cancelacion)) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: DETALLE -->
        <div class="card-pro">
            <div class="card-head">
                <h3 class="card-title">Detalle</h3>
            </div>
            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table detailview'],
                    'attributes' => [
                        [
                            'label' => 'ID',
                            'value' => $model->id,
                        ],
                        [
                            'label' => 'Folio',
                            'value' => $model->folio ?: '-',
                        ],
                        [
                            'label' => 'Paciente',
                            'value' => $pacienteNombre ? $pacienteNombre . ' (ID: ' . $model->paciente_id . ')' : ($model->paciente_id ?: '-'),
                        ],
                        [
                            'label' => 'Servicio',
                            'value' => $servicioNombre ? $servicioNombre . ' (ID: ' . $model->servicio_id . ')' : ($model->servicio_id ?: '-'),
                        ],
                        [
                            'label' => 'Inicio',
                            'value' => $inicioFmt,
                        ],
                        [
                            'label' => 'Fin',
                            'value' => $finFmt,
                        ],
                        [
                            'label' => 'Estado',
                            'format' => 'raw',
                            'value' => '<span class="badge-soft ' . Html::encode($estadoClass) . '"> ' . Html::encode($estadoLabel) . '</span>',
                        ],
                        [
                            'label' => 'Canal',
                            'value' => $canalLabel,
                        ],
                        [
                            'label' => 'Creado',
                            'value' => $createdFmt . ($createdAgo ? " ({$createdAgo})" : ''),
                        ],
                        [
                            'label' => 'Actualizado',
                            'value' => $updatedFmt . ($updatedAgo ? " ({$updatedAgo})" : ''),
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>