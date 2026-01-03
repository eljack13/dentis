<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Panel de Control';

// Registrar Chart.js desde CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="site-index container-fluid py-4">

    <div class="mb-5">
        <h2 class="fw-bold text-dark" style="font-family: 'Segoe UI', sans-serif;">
            Hola, <?= Html::encode(Yii::$app->user->identity->nombre ?? 'Doctor') ?> 👋
        </h2>
        <p class="text-muted">Aquí tienes el resumen hoy.</p>
    </div>

    <div class="row g-4 mb-5">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold ls-1">Citas Hoy</span>
                        <h2 class="mb-0 fw-bold text-dark mt-1"><?= $citasHoy ?></h2>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bi bi-calendar-event fs-3"></i>
                    </div>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 70%"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold ls-1">Pacientes</span>
                        <h2 class="mb-0 fw-bold text-dark mt-1"><?= $totalPacientes ?></h2>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold ls-1">Pendientes</span>
                        <h2 class="mb-0 fw-bold text-dark mt-1"><?= $citasPendientes ?></h2>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold ls-1">Citas Mes</span>
                        <h2 class="mb-0 fw-bold text-dark mt-1"><?= $ingresosMes ?></h2>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-primary"></i>Flujo de Citas (7 Días)</h5>
                </div>
                <div class="card-body">
                    <canvas id="lineChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Estados</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div style="width: 100%; max-width: 280px;">
                        <canvas id="donutChart"></canvas>
                    </div>
                    <div class="mt-4 text-center text-muted small">
                        Distribución de citas por estado actual.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Próximas Citas Agendadas</h5>
            <a href="<?= Url::to(['cita/index']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Ver Agenda Completa</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-muted small fw-bold text-uppercase">Paciente</th>
                        <th class="text-muted small fw-bold text-uppercase">Fecha / Hora</th>
                        <th class="text-muted small fw-bold text-uppercase">Tratamiento</th>
                        <th class="text-muted small fw-bold text-uppercase text-center">Estado</th>
                        <th class="text-end pe-4 text-muted small fw-bold text-uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($proximasCitas) > 0): ?>
                        <?php foreach ($proximasCitas as $cita): ?>
                            <tr>
                                <td class="ps-4 fw-medium text-dark">
                                    <i class="bi bi-person-circle text-secondary me-2"></i>
                                    <?= Html::encode($cita->paciente->nombre ?? 'Desconocido') ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark"><?= Yii::$app->formatter->asDate($cita->inicio, 'php:d M, Y') ?></span>
                                        <small class="text-muted"><?= Yii::$app->formatter->asTime($cita->inicio, 'php:H:i A') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= Html::encode($cita->servicio->nombre ?? 'Consulta General') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $estadoColor = match (strtoupper($cita->estado)) {
                                        'CONFIRMADA' => 'success',
                                        'PENDIENTE' => 'warning',
                                        'CANCELADA' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $estadoColor ?>-subtle text-<?= $estadoColor ?> rounded-pill px-3">
                                        <?= Html::encode($cita->estado) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= Url::to(['cita/view', 'id' => $cita->id]) ?>" class="btn btn-sm btn-link text-primary"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                                No hay citas próximas registradas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. CONFIGURACIÓN GRÁFICO DE LÍNEA (FLUJO)
        const ctxLine = document.getElementById('lineChart').getContext('2d');

        // Datos traídos de PHP
        const labelsLine = <?= json_encode($chartLabels) ?>;
        const dataLine = <?= json_encode($chartData) ?>;

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labelsLine,
                datasets: [{
                    label: 'Citas',
                    data: dataLine,
                    borderColor: '#3b82f6', // Azul profesional
                    backgroundColor: 'rgba(59, 130, 246, 0.1)', // Relleno suave
                    borderWidth: 2,
                    tension: 0.4, // Curva suave
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointRadius: 4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    } // Ocultar leyenda para limpieza
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            color: '#f1f5f9'
                        },
                        ticks: {
                            precision: 0
                        } // Solo números enteros
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 2. CONFIGURACIÓN GRÁFICO DE DONA (ESTADOS)
        const ctxDonut = document.getElementById('donutChart').getContext('2d');

        // Datos traídos de PHP
        const labelsDonut = <?= json_encode($donutLabels) ?>;
        const dataDonut = <?= json_encode($donutData) ?>;

        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: labelsDonut,
                datasets: [{
                    data: dataDonut,
                    backgroundColor: [
                        '#10b981', // Verde (Confirmada)
                        '#f59e0b', // Amarillo (Pendiente)
                        '#ef4444', // Rojo (Cancelada)
                        '#3b82f6' // Azul (Otra)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                cutout: '75%' // Dona más fina
            }
        });
    });
</script>

<style>
    /* Pequeños ajustes visuales */
    .ls-1 {
        letter-spacing: 1px;
    }

    .bg-primary-subtle {
        background-color: #cfe2ff;
    }

    .text-primary {
        color: #0a58ca;
    }

    .card {
        border-radius: 12px;
    }

    .table thead th {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
</style>