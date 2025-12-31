<?php

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Servicios Clínicos';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-family: 'Segoe UI', sans-serif;">Servicios Clínicos</h2>
            <p class="text-muted mb-0">Administra los tratamientos. <small class="text-primary"><i class="bi bi-mouse"></i> Doble clic para ficha rápida.</small></p>
        </div>
        <?= Html::a('<i class="bi bi-plus-lg me-2"></i>Nuevo Servicio', ['create'], [
            'class' => 'btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-medium',
            'style' => 'background-color: #2563eb; border: none;'
        ]) ?>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n<div class='col-12 d-flex justify-content-center mt-5'>{pager}</div>",
        'options' => ['class' => 'row g-4'],
        'itemOptions' => ['class' => 'col-12 col-md-6 col-lg-4 col-xl-3'],
        'itemView' => function ($model, $key, $index, $widget) {

            // Preparamos los datos para JS
            $nombre = Html::encode($model->nombre);
            $descCorta = !empty($model->descripcion) ? Html::encode(substr($model->descripcion, 0, 90)) . '...' : 'Sin descripción.';
            $descCompleta = !empty($model->descripcion) ? Html::encode($model->descripcion) : 'No hay descripción detallada registrada para este procedimiento.';
            $color = !empty($model->color) ? $model->color : '#475569';

            // URLs
            $urlUpdate = Url::to(['update', 'id' => $model->id]);
            $urlDelete = Url::to(['delete', 'id' => $model->id]);

            // Estado para el badge
            $estadoTexto = $model->activo ? 'Activo' : 'Inactivo';
            $estadoClass = $model->activo ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
            $estadoIcon = $model->activo ? 'bi-check-circle-fill' : 'bi-dash-circle-fill';

            return '
            <div class="card h-100 border-0 shadow-sm pro-card service-card-trigger" 
                 style="border-top: 5px solid ' . $color . ' !important; cursor: pointer;"
                 
                 data-id="' . $model->id . '"
                 data-nombre="' . $nombre . '"
                 data-descripcion="' . $descCompleta . '"
                 data-duracion="' . ($model->duracion_min ?? 0) . '"
                 data-buffer="' . ($model->buffer_min ?? 0) . '"
                 data-color="' . $color . '"
                 data-estado-texto="' . $estadoTexto . '"
                 data-estado-class="' . $estadoClass . '"
                 data-estado-icon="' . $estadoIcon . '"
                 data-url-update="' . $urlUpdate . '"
                 data-url-delete="' . $urlDelete . '"
                 
                 title="Doble clic para ficha técnica">
                
                <div class="card-body p-4 d-flex flex-column text-center">
                    <div class="icon-wrapper mx-auto mb-3" style="color: ' . $color . '; background-color: ' . $color . '15;">
                        <i class="bi bi-clipboard2-pulse-fill"></i>
                    </div>

                    <h5 class="card-title fw-bold text-dark mb-3">' . $nombre . '</h5>
                    
                    <p class="card-text text-muted small flex-grow-1 mb-4" style="line-height: 1.6;">
                        ' . $descCorta . '
                    </p>

                    <button type="button" class="btn btn-outline-dark rounded-pill w-100 mb-3 btn-details">
                        Ver Ficha
                    </button>

                    <div class="d-flex justify-content-center gap-3 actions-footer">
                        <a href="' . $urlUpdate . '" class="action-link edit"><i class="bi bi-pencil-square me-1"></i> Editar</a>
                        <span class="text-black-50 opacity-25">|</span>
                        <a href="' . $urlDelete . '" class="action-link delete" data-method="post" data-confirm="¿Eliminar?"><i class="bi bi-trash3 me-1"></i></a>
                    </div>
                </div>
            </div>';
        },
        'pager' => ['class' => \yii\bootstrap5\LinkPager::class],
    ]); ?>
</div>

<style>
    /* Estilos base (Mismos que te gustaron) */
    .pro-card {
        background: #fff;
        border-radius: 12px;
        transition: transform 0.2s;
    }

    .pro-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .btn-details {
        font-size: 0.9rem;
        border-color: #cbd5e1;
        color: #475569;
    }

    .btn-details:hover {
        background-color: #475569;
        color: #fff;
    }

    .action-link {
        text-decoration: none;
        font-size: 0.85rem;
        color: #64748b;
    }

    .action-link:hover {
        color: #2563eb;
    }

    .action-link.delete:hover {
        color: #ef4444;
    }

    /* Estilos específicos para el SweetAlert Personalizado */
    .swal2-html-container {
        margin: 0 !important;
        overflow: hidden;
        text-align: left !important;
    }

    .swal-clinical-popup {
        border-radius: 16px !important;
        padding: 0 !important;
        overflow: hidden;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #64748b;
        font-size: 0.9rem;
    }

    .detail-value {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }

    /* Estilos específicos para el SweetAlert Personalizado */
    .swal2-html-container {
        margin: 0 !important;
        overflow: hidden;
        text-align: left !important;
    }

    .swal-clinical-popup {
        border-radius: 16px !important;
        padding: 0 !important;
        overflow: hidden;
    }

    /* --- ARREGLO DEL BOTÓN 'X' --- */
    .swal2-close {
        /* Quita el borde azul y el fondo al hacer foco/hover */
        box-shadow: none !important;
        outline: none !important;
        font-size: 1.5rem !important;
        /* Posición más precisa */
        top: 5px !important;
        right: 5px !important;
        /* Color sutil que combine */
        color: #94a3b8 !important;
    }

    .swal2-close:hover {
        color: #475569 !important;
        /* Un poco más oscuro al pasar el mouse */
        background: transparent !important;
    }
</style>

<?php
// Lógica JS con SweetAlert
$js = <<<JS
    $('.service-card-trigger').on('dblclick', function() {
        // 1. Extraer datos de la tarjeta (sin petición Ajax = instantáneo)
        let d = $(this).data();

        // 2. Construir el HTML del SweetAlert
        let contentHtml = `
            <div style="background: linear-gradient(to right, \${d.color}, \${d.color}dd); height: 10px; width: 100%;"></div>
            
            <div class="p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px; background-color: \${d.color}20; color: \${d.color}; font-size: 1.5rem;">
                        <i class="bi bi-clipboard2-pulse-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">\${d.nombre}</h4>
                        <span class="badge \${d.estadoClass} rounded-pill border px-2 mt-1">
                            <i class="bi \${d.estadoIcon}"></i> \${d.estadoTexto}
                        </span>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-3 mb-4 text-secondary small" style="line-height: 1.6; max-height: 150px; overflow-y: auto;">
                    \${d.descripcion}
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="border rounded-3 p-2 text-center">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Duración</small>
                            <span class="fw-bold fs-5 text-dark">\${d.duracion}</span> <span class="small text-muted">min</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-2 text-center">
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Buffer</small>
                            <span class="fw-bold fs-5 text-dark">\${d.buffer}</span> <span class="small text-muted">min</span>
                        </div>
                    </div>
                </div>
            </div>
            
                    <div class="bg-light px-4 py-3 d-flex justify-content-between align-items-center border-top">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="Swal.close()">
                Cerrar
            </button>
            
            <a href="\${d.urlUpdate}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-pencil-square me-1"></i> Editar Ficha
            </a>
        </div>
        `;

        // 3. Disparar SweetAlert
        Swal.fire({
            html: contentHtml,
            showConfirmButton: false, // Usamos nuestros propios botones
            showCloseButton: true,
            width: 500,
            customClass: {
                popup: 'swal-clinical-popup'
            },
            animation: true
        });
    });

    // Permitir clic en el botón "Ver Ficha" para simular doble clic (por si están en móvil)
    $('.btn-details').on('click', function(e) {
        e.stopPropagation(); // Evitar burbujeo
        $(this).closest('.service-card-trigger').trigger('dblclick');
    });
JS;
$this->registerJs($js);
?>