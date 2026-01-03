<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\Paciente $model */

$this->title = 'Paciente: ' . $model->nombre . ' ' . $model->apellidos;
$this->params['breadcrumbs'][] = ['label' => 'Pacientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);

// URLs para AJAX
$fotosUrl      = Url::to(['paciente/cita-fotos-ajax']);
$uploadFotoUrl = Url::to(['paciente/cita-foto-upload-ajax']);
$deleteFotoUrl = Url::to(['paciente/cita-foto-delete-ajax']);
$getSesionUrl  = Url::to(['paciente/cita-foto-sesion-ajax']);
$csrfToken     = Yii::$app->request->getCsrfToken();

// Librerías externas
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => View::POS_END]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', ['position' => View::POS_END]);

$this->registerCss(<<<CSS
    /* Variables globales */
    :root { 
        --color-primary: #3b82f6;
        --color-danger: #ef4444;
        --color-text: #1f2937;
        --color-text-light: #6b7280;
        --color-border: #e5e7eb;
        --color-bg: #f9fafb;
        --radius: 8px;
    }
    
    /* Contenedor principal */
    .patient-container { 
        max-width: 1200px; 
        margin: 0 auto; 
        padding: 24px; 
        background: #fff;
    }
    
    .patient-header { 
        margin-bottom: 32px; 
        padding-bottom: 16px;
        border-bottom: 1px solid var(--color-border);
    }
    
    .patient-header h2 { 
        margin: 0; 
        font-size: 24px; 
        font-weight: 700; 
        color: var(--color-text);
    }
    
    /* Información del paciente */
    .patient-info { 
        margin-bottom: 40px; 
        padding: 20px;
        background: var(--color-bg);
        border-radius: var(--radius);
    }
    
    /* Sección de citas */
    .appointments-section h3 { 
        font-size: 18px; 
        font-weight: 600; 
        color: var(--color-text);
        margin: 0 0 20px 0;
    }
    
    /* Grid de citas - diseño minimalista */
    .appointments-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
        gap: 16px;
    }
    
    /* Tarjeta de cita */
    .appointment-card { 
        background: #fff; 
        border: 1px solid var(--color-border);
        border-radius: var(--radius); 
        padding: 20px;
        transition: all 0.2s;
    }
    
    .appointment-card:hover { 
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: var(--color-primary);
    }
    
    /* Fecha destacada */
    .appointment-date { 
        font-size: 20px; 
        font-weight: 700; 
        color: var(--color-text);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* Información de horarios */
    .appointment-time { 
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 16px;
        padding: 12px;
        background: var(--color-bg);
        border-radius: 6px;
    }
    
    .appointment-time span { 
        font-size: 14px; 
        color: var(--color-text-light);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .appointment-time strong { 
        color: var(--color-text);
        font-weight: 600;
    }
    
    /* Botones de acción */
    .appointment-actions { 
        display: flex; 
        gap: 8px;
        padding-top: 16px;
        border-top: 1px solid var(--color-border);
    }
    
    .btn { 
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-primary { 
        background: var(--color-primary);
        color: white;
    }
    
    .btn-primary:hover { 
        background: #2563eb;
        transform: translateY(-1px);
    }
    
    .btn-secondary { 
        background: var(--color-bg);
        color: var(--color-text);
    }
    
    .btn-secondary:hover { 
        background: #f3f4f6;
    }
    
    /* Iconos SVG simples */
    .icon { 
        width: 16px; 
        height: 16px;
        display: inline-block;
    }
    
    /* Modal de fotos */
    .swal2-popup { 
        border-radius: var(--radius) !important;
    }
    
    /* Zona de subida */
    .upload-area { 
        background: var(--color-bg);
        border: 2px dashed var(--color-border);
        border-radius: var(--radius);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .upload-controls { 
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    
    .upload-field { 
        flex: 1;
        min-width: 200px;
    }
    
    .upload-field label { 
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 6px;
    }
    
    .upload-field input { 
        width: 100%;
        padding: 8px;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        font-size: 14px;
    }
    
    /* Grid de fotos */
    .photos-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); 
        gap: 12px;
    }
    
    .photo-item { 
        position: relative;
        aspect-ratio: 1;
        border-radius: 6px;
        overflow: hidden;
        background: var(--color-bg);
        border: 1px solid var(--color-border);
    }
    
    .photo-item:hover { 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .photo-item img { 
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
    }
    
    .photo-delete { 
        position: absolute;
        top: 6px;
        right: 6px;
        width: 28px;
        height: 28px;
        background: var(--color-danger);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        line-height: 1;
        z-index: 10;
    }
    
    .photo-item:hover .photo-delete { 
        opacity: 1;
    }
    
    .photo-delete:hover { 
        background: #dc2626;
        transform: scale(1.1);
    }
    
    @media (max-width: 768px) {
        .photo-delete {
            opacity: 1;
        }
    }
    
    /* Estados vacíos */
    .empty-state { 
        text-align: center;
        padding: 40px 20px;
        color: var(--color-text-light);
        background: var(--color-bg);
        border-radius: var(--radius);
        border: 1px dashed var(--color-border);
    }
    
    /* Loader */
    .loading { 
        text-align: center;
        padding: 20px;
        color: var(--color-text-light);
    }
CSS);

$this->registerJs(<<<JS
// ===== UTILIDADES =====
const escapeHtml = str => String(str ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));

// Fetch con manejo de errores
const fetchData = async (url) => {
    const res = await fetch(url, { 
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) throw new Error('Error en la conexión');
    const data = await res.json();
    if (!data.success) throw new Error(data.message || 'Error desconocido');
    return data;
};

// Post con JSON
const postData = async (url, payload, csrf) => {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': csrf 
        },
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.message || 'Error al procesar');
    return data;
};

// ===== ICONOS SVG =====
const icons = {
    calendar: '<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    clock: '<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    image: '<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    edit: '<svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>'
};

// ===== RENDERIZAR FOTOS =====
function renderPhotosGrid(fotos, citaId) {
    if (!fotos || !fotos.length) {
        return '<div class="empty-state">No hay fotos en esta cita</div>';
    }

    return '<div class="photos-grid">' +
        fotos.map(f => `
            <div class="photo-item">
                <a href="\${escapeHtml(f.url)}" 
                   class="glightbox" 
                   data-gallery="cita-\${citaId}">
                    <img src="\${escapeHtml(f.url)}" alt="Foto">
                </a>
                <button class="photo-delete" data-id="\${f.id}">×</button>
            </div>
        `).join('') +
    '</div>';
}

// ===== MODAL DE FOTOS =====
async function openPhotosModal(citaId, dateLabel) {
    let lightbox = null;

    // Cargar fotos
    const loadPhotos = async () => {
        const container = document.getElementById('photos-container');
        
        // Validar que el contenedor existe
        if (!container) {
            console.error('Container photos-container not found');
            return;
        }
        
        container.innerHTML = '<div class="loading">Cargando fotos...</div>';
        
        try {
            const res = await fetchData('{$fotosUrl}?cita_id=' + citaId);
            const fotos = res.fotos || [];
            
            // Validar nuevamente antes de actualizar
            if (!document.getElementById('photos-container')) {
                return;
            }
            
            container.innerHTML = renderPhotosGrid(fotos, citaId);

            // Inicializar carrusel
            if (lightbox) lightbox.destroy();
            lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                touchFollowAxis: true,
                loop: true,
                zoomable: true,
                draggable: true,
                preload: true
            });

            // Eventos de eliminar
            container.querySelectorAll('.photo-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.id;
                    
                    const confirm = await Swal.fire({
                        title: '¿Eliminar esta foto?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#ef4444'
                    });

                    if (confirm.isConfirmed) {
                        try {
                            await postData('{$deleteFotoUrl}', { id }, '{$csrfToken}');
                            await loadPhotos();
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Foto eliminada',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } catch (err) {
                            Swal.fire('Error', err.message, 'error');
                        }
                    }
                });
            });

        } catch (err) {
            container.innerHTML = '<div class="empty-state" style="color:#ef4444;">Error al cargar fotos</div>';
        }
    };

    // Pre-cargar sesión
    fetchData('{$getSesionUrl}?cita_id=' + citaId);

    // Abrir modal
    await Swal.fire({
        title: 'Fotos de la cita',
        html: dateLabel,
        width: 800,
        showConfirmButton: false,
        showCloseButton: true,
        html: `
            <div style="text-align:left;font-size:14px;color:#6b7280;margin-bottom:20px;">
                \${dateLabel}
            </div>
            
            <div class="upload-area">
                <div class="upload-controls">
                    <div class="upload-field">
                        <label>Seleccionar foto(s)</label>
                        <input id="photo-input" type="file" accept="image/*" multiple>
                    </div>
                    <div class="upload-field" style="max-width:200px;">
                        <label>Etiqueta (opcional)</label>
                        <input id="photo-label" type="text" placeholder="Ej: Rayos X">
                    </div>
                    <button id="upload-btn" class="btn btn-primary" style="height:38px;">
                        Subir
                    </button>
                </div>
            </div>
            
            <div id="photos-container"></div>
        `,
        didOpen: async () => {
            try {
                await loadPhotos();
            } catch (err) {
                console.error('Error loading photos:', err);
            }

            // Subir fotos
            document.getElementById('upload-btn').addEventListener('click', async () => {
                const input = document.getElementById('photo-input');
                const label = document.getElementById('photo-label');
                const btn = document.getElementById('upload-btn');
                
                // Validar elementos
                if (!input || !label || !btn) {
                    console.error('Form elements not found');
                    return Swal.showValidationMessage('Error: elementos del formulario no encontrados');
                }
                
                if (!input.files.length) {
                    return Swal.showValidationMessage('Selecciona al menos una foto');
                }

                const formData = new FormData();
                formData.append('cita_id', citaId);
                if (label.value) formData.append('etiqueta', label.value);
                Array.from(input.files).forEach(f => formData.append('fotos[]', f));

                btn.textContent = 'Subiendo...';
                btn.disabled = true;

                try {
                    const res = await fetch('{$uploadFotoUrl}', {
                        method: 'POST',
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-Token': '{$csrfToken}'
                        },
                        body: formData
                    });
                    const data = await res.json();
                    
                    if (!data.success) throw new Error(data.message);

                    // Limpiar inputs con validación
                    if (input) input.value = '';
                    if (label) label.value = '';
                    
                    await loadPhotos();
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Foto subida correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (err) {
                    Swal.fire('Error', err.message, 'error');
                } finally {
                    if (btn) {
                        btn.textContent = 'Subir';
                        btn.disabled = false;
                    }
                }
            });
        },
        willClose: () => {
            if (lightbox) lightbox.destroy();
        }
    });
}

// ===== LISTENER GLOBAL =====
document.addEventListener('click', e => {
    const btn = e.target.closest('[data-photos]');
    if (btn) {
        const id = btn.dataset.citaId;
        const label = btn.dataset.citaLabel;
        openPhotosModal(id, label);
    }
});
JS, View::POS_END);
?>

<!-- HTML más limpio y semántico -->
<div class="patient-container">

    <div class="patient-header">
        <h2><?= Html::encode($this->title) ?></h2>
    </div>

    <div class="patient-info">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-bordered', 'style' => 'margin:0;'],
            'attributes' => [
                'nombre',
                'apellidos',
                'telefono',
                'email:email',
                [
                    'attribute' => 'fecha_nacimiento',
                    'value' => $model->fecha_nacimiento ? Yii::$app->formatter->asDate($model->fecha_nacimiento, 'long') : null
                ],
                'notas:ntext',
            ],
        ]) ?>
    </div>

    <div class="appointments-section">
        <h3>Historial de Citas</h3>

        <?php
        $citas = \app\models\Cita::find()
            ->where(['paciente_id' => (int)$model->id])
            ->orderBy(['inicio' => SORT_DESC])
            ->all();
        ?>

        <?php if (empty($citas)): ?>
            <div class="empty-state">
                Este paciente no tiene citas registradas
            </div>
        <?php else: ?>
            <div class="appointments-grid">
                <?php foreach ($citas as $cita): ?>
                    <?php
                    $fecha = $cita->inicio ? Yii::$app->formatter->asDate($cita->inicio, 'php:d/m/Y') : 'Sin fecha';
                    $horaInicio = $cita->inicio ? Yii::$app->formatter->asTime($cita->inicio, 'php:H:i') : '?';
                    $horaFin = $cita->fin ? Yii::$app->formatter->asTime($cita->fin, 'php:H:i') : '?';
                    $fechaCompleta = $cita->inicio ? Yii::$app->formatter->asDatetime($cita->inicio, 'php:d M Y, H:i') : 'Sin fecha';
                    ?>

                    <div class="appointment-card">
                        <div class="appointment-date">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <?= $fecha ?>
                        </div>

                        <div class="appointment-time">
                            <span>
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <strong>Inicio:</strong> <?= $horaInicio ?>
                            </span>
                            <span>
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <strong>Fin:</strong> <?= $horaFin ?>
                            </span>
                        </div>

                        <div class="appointment-actions">
                            <button type="button"
                                class="btn btn-primary"
                                data-photos
                                data-cita-id="<?= $cita->id ?>"
                                data-cita-label="<?= $fechaCompleta ?>">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Fotos
                            </button>

                            <a href="<?= Url::to(['cita/update', 'id' => $cita->id]) ?>" class="btn btn-secondary">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>