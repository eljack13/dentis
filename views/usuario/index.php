<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UsuarioSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Personal y Acceso';
?>

<div class="container-fluid py-4">

    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1" style="font-family: 'Segoe UI', sans-serif;">Gestión de Usuarios</h2>
            <p class="text-muted mb-0">Administra el acceso y roles del personal de la clínica.</p>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end gap-3 mt-3 mt-md-0">
            <form action="<?= Url::to(['index']) ?>" method="get" class="d-flex position-relative" style="flex-grow: 1; max-width: 300px;">
                <input type="text" name="UsuarioSearch[nombre]" class="form-control rounded-pill ps-4" placeholder="Buscar por nombre..." value="<?= Html::encode($searchModel->nombre) ?>">
                <button type="submit" class="btn btn-link position-absolute end-0 top-0 text-muted text-decoration-none">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <?= Html::a('<i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario', ['create'], [
                'class' => 'btn btn-primary px-4 rounded-pill shadow-sm fw-medium d-flex align-items-center',
                'style' => 'background-color: #2563eb; border: none;'
            ]) ?>
        </div>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n<div class='col-12 d-flex justify-content-center mt-5'>{pager}</div>",
        'options' => ['class' => 'row g-4'],
        'itemOptions' => ['class' => 'col-12 col-md-6 col-lg-4 col-xl-3'],
        'itemView' => function ($model, $key, $index, $widget) {

            // 1. Generar Iniciales
            $words = explode(" ", $model->nombre);
            $initials = "";
            foreach ($words as $w) {
                $initials .= mb_substr($w, 0, 1);
                if (strlen($initials) >= 2) break; // Máximo 2 letras
            }
            $initials = strtoupper($initials);

            // 2. Definir Color según Rol (Lógica visual)
            $roleColor = match (strtolower($model->rol)) {
                'admin', 'administrador' => '#ef4444', // Rojo
                'doctor', 'dentista' => '#3b82f6',      // Azul
                'recepcion', 'asistente' => '#10b981',  // Verde
                default => '#64748b'                    // Gris
            };

            // 3. URLs
            $urlUpdate = Url::to(['update', 'id' => $model->id]);
            $urlDelete = Url::to(['delete', 'id' => $model->id]);

            // 4. Datos seguros para JS
            $nombreSafe = Html::encode($model->nombre);
            $emailSafe = Html::encode($model->email);
            $telSafe = Html::encode($model->telefono);
            $rolSafe = ucfirst(Html::encode($model->rol));

            return '
            <div class="card h-100 border-0 shadow-sm user-card user-trigger"
                 style="cursor: pointer;"
                 data-id="' . $model->id . '"
                 data-nombre="' . $nombreSafe . '"
                 data-email="' . $emailSafe . '"
                 data-telefono="' . $telSafe . '"
                 data-rol="' . $rolSafe . '"
                 data-initials="' . $initials . '"
                 data-color="' . $roleColor . '"
                 data-url-update="' . $urlUpdate . '"
                 data-url-delete="' . $urlDelete . '"
                 title="Doble clic para ver ficha">
                
                <div style="height: 4px; background-color: ' . $roleColor . '; width: 100%;"></div>

                <div class="card-body p-4 text-center d-flex flex-column align-items-center">
                    
                    <div class="avatar shadow-sm mb-3" style="background-color: ' . $roleColor . '15; color: ' . $roleColor . ';">
                        ' . $initials . '
                    </div>

                    <h5 class="fw-bold text-dark mb-1">' . $nombreSafe . '</h5>
                    <span class="badge rounded-pill border mb-3" 
                          style="background-color: ' . $roleColor . '10; color: ' . $roleColor . '; border-color: ' . $roleColor . '30 !important;">
                        ' . $rolSafe . '
                    </span>

                    <div class="w-100 mt-auto">
                        <div class="d-flex align-items-center justify-content-center text-muted small mb-2">
                            <i class="bi bi-envelope me-2"></i> ' . $emailSafe . '
                        </div>
                        <div class="d-flex align-items-center justify-content-center text-muted small">
                            <i class="bi bi-telephone me-2"></i> ' . $telSafe . '
                        </div>
                    </div>

                    <div class="action-overlay">
                        <a href="' . $urlUpdate . '" class="btn btn-sm btn-light rounded-circle shadow-sm text-primary me-2" title="Editar">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="' . $urlDelete . '" class="btn btn-sm btn-light rounded-circle shadow-sm text-danger" title="Eliminar" data-method="post" data-confirm="¿Eliminar usuario?">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </div>
                </div>
            </div>';
        },
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
            'options' => ['class' => 'pagination justify-content-center'],
        ],
    ]); ?>
</div>

<style>
    /* === ESTILOS USER CARD === */
    .user-card {
        background: #fff;
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        overflow: hidden;
    }

    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    }

    /* Avatar */
    .avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* Action Overlay (Botones flotantes al hover) */
    .action-overlay {
        position: absolute;
        top: 15px;
        right: 15px;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.2s ease;
    }

    .user-card:hover .action-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    /* Buscador */
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
    }

    /* SweetAlert Styles (Reutilizados y ajustados) */
    .swal-user-popup {
        border-radius: 16px !important;
        padding: 0 !important;
    }

    .swal2-close {
        box-shadow: none !important;
        outline: none !important;
        color: #94a3b8 !important;
    }

    .swal2-close:hover {
        color: #475569 !important;
        background: transparent !important;
    }
</style>

<?php
$js = <<<JS
    // Detección de Doble Clic para Ficha de Usuario
    $('.user-trigger').on('dblclick', function() {
        let d = $(this).data();

        let contentHtml = `
            <div class="text-center pt-5 pb-3 bg-light border-bottom">
                <div class="mx-auto shadow-sm d-flex align-items-center justify-content-center rounded-circle mb-3" 
                     style="width: 90px; height: 90px; background-color: \${d.color}20; color: \${d.color}; font-size: 2rem; font-weight: bold;">
                    \${d.initials}
                </div>
                <h4 class="fw-bold text-dark mb-1">\${d.nombre}</h4>
                <span class="badge rounded-pill px-3" style="background-color: \${d.color};">\${d.rol}</span>
            </div>

            <div class="p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3 text-secondary"><i class="bi bi-envelope fs-4"></i></div>
                    <div>
                        <small class="text-muted text-uppercase d-block" style="font-size: 0.7rem;">Correo Electrónico</small>
                        <span class="fw-medium text-dark">\${d.email}</span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3 text-secondary"><i class="bi bi-telephone fs-4"></i></div>
                    <div>
                        <small class="text-muted text-uppercase d-block" style="font-size: 0.7rem;">Teléfono</small>
                        <span class="fw-medium text-dark">\${d.telefono}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="me-3 text-secondary"><i class="bi bi-shield-lock fs-4"></i></div>
                    <div>
                        <small class="text-muted text-uppercase d-block" style="font-size: 0.7rem;">ID Sistema</small>
                        <span class="fw-medium text-dark">#\${d.id}</span>
                    </div>
                </div>
            </div>

            <div class="bg-light px-4 py-3 d-flex justify-content-between align-items-center border-top">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="Swal.close()">Cerrar</button>
                <a href="\${d.urlUpdate}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-pencil-square me-1"></i> Editar Perfil
                </a>
            </div>
        `;

        Swal.fire({
            html: contentHtml,
            showConfirmButton: false,
            showCloseButton: true,
            width: 450,
            customClass: { popup: 'swal-user-popup' },
            animation: true
        });
    });
JS;
$this->registerJs($js);
?>