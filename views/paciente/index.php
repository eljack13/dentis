<?php

use app\models\Paciente;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PacienteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Pacientes';
$this->params['breadcrumbs'][] = $this->title;

// Registramos el JS para el filtrado en tiempo real de la tabla
$this->registerJs('
    const searchInput = document.getElementById("table-search-input");
    const tableRows = document.querySelectorAll(".grid-view tbody tr");
    const emptyState = document.getElementById("table-empty-state");
    const isMobile = window.innerWidth <= 768;

    // Crear cartas en móvil
    function createMobileCards() {
        const tableBody = document.querySelector(".grid-view tbody");
        if (!tableBody) return;

        const cardsContainer = document.createElement("div");
        cardsContainer.className = "paciente-cards";
        cardsContainer.id = "paciente-cards-container";

        tableRows.forEach((row, index) => {
            const cells = row.querySelectorAll("td");
            if (cells.length === 0) return;

            // Extraer datos de la tabla
            const id = cells[1]?.innerText.trim() || "";
            const nombre = cells[2]?.innerText.trim() || "";
            const apellidos = cells[3]?.innerText.trim() || "";
            const telefono = cells[4]?.innerText.trim() || "";
            const email = cells[5]?.innerText.trim() || "";
            const fecha = cells[6]?.innerText.trim() || "";
            const statusHtml = cells[7]?.innerHTML || "";

            // Crear tarjeta
            const card = document.createElement("div");
            card.className = "paciente-card visible";
            card.dataset.rowIndex = index;
            
            // Extraer botones de acción
            const viewLink = row.querySelector("a[title=\"Ver\"]")?.cloneNode(true) || "";
            const editLink = row.querySelector("a[title=\"Editar\"]")?.cloneNode(true) || "";
            const deleteLink = row.querySelector("a[title=\"Eliminar\"]")?.cloneNode(true) || "";

            card.innerHTML = "<div class=\"card-header\"><div><div class=\"card-name\">" + nombre + " " + apellidos + "</div></div><div class=\"card-id\">#" + id + "</div></div>" +
                "<div class=\"card-row\"><span class=\"card-label\">Teléfono</span><span class=\"card-value phone\">" + (telefono || "N/A") + "</span></div>" +
                "<div class=\"card-row\"><span class=\"card-label\">Email</span><span class=\"card-value email\">" + (email || "N/A") + "</span></div>" +
                "<div class=\"card-row\"><span class=\"card-label\">Nacimiento</span><span class=\"card-value\">" + (fecha || "N/A") + "</span></div>" +
                "<div class=\"card-row\"><span class=\"card-label\">Estado</span><div class=\"card-value\">" + statusHtml + "</div></div>" +
                "<div class=\"card-actions\"></div>";

            // Agregar botones de acción
            const actionsDiv = card.querySelector(".card-actions");
            if (viewLink) {
                const viewBtn = document.createElement("a");
                viewBtn.className = "card-action-btn view";
                viewBtn.href = viewLink.href;
                viewBtn.title = "Ver";
                viewBtn.innerHTML = "<svg fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"/><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z\"/></svg> Ver";
                actionsDiv.appendChild(viewBtn);
            }
            if (editLink) {
                const editBtn = document.createElement("a");
                editBtn.className = "card-action-btn edit";
                editBtn.href = editLink.href;
                editBtn.title = "Editar";
                editBtn.innerHTML = "<svg fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\"/></svg> Editar";
                actionsDiv.appendChild(editBtn);
            }
            if (deleteLink) {
                const deleteBtn = document.createElement("a");
                deleteBtn.className = "card-action-btn delete";
                deleteBtn.href = deleteLink.href;
                deleteBtn.title = "Eliminar";
                deleteBtn.dataset.confirm = "¿Eliminar paciente?";
                deleteBtn.dataset.method = "post";
                deleteBtn.innerHTML = "<svg fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"/></svg> Eliminar";
                actionsDiv.appendChild(deleteBtn);
            }

            cardsContainer.appendChild(card);
        });

        // Insertar cartas después de la tabla
        const gridView = document.querySelector(".grid-view");
        if (gridView) {
            gridView.appendChild(cardsContainer);
        }
    }

    // Crear cartas si es móvil
    if (isMobile) {
        setTimeout(createMobileCards, 100);
    }

    // Búsqueda funciona en ambas vistas
    searchInput.addEventListener("keyup", function(e) {
        const term = e.target.value.toLowerCase();
        let visibleCount = 0;

        if (isMobile) {
            // Buscar en cartas
            const cards = document.querySelectorAll(".paciente-card");
            cards.forEach(card => {
                const textContent = card.innerText.toLowerCase();
                
                if (textContent.includes(term)) {
                    card.classList.add("visible");
                    card.style.display = "block";
                    visibleCount++;
                } else {
                    card.classList.remove("visible");
                    card.style.display = "none";
                }
            });
        } else {
            // Buscar en tabla
            tableRows.forEach(row => {
                const textContent = row.innerText.toLowerCase();
                
                if (textContent.includes(term)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });
        }

        // Mostrar mensaje si no hay resultados
        if (visibleCount === 0) {
            emptyState.style.display = "block";
        } else {
            emptyState.style.display = "none";
        }
    });

    // Recrear cartas cuando resize (si cambia tamaño)
    window.addEventListener("resize", function() {
        const newIsMobile = window.innerWidth <= 768;
        if (newIsMobile !== isMobile) {
            location.reload();
        }
    });
');

?>

<style>
    :root {
        --primary: #2563eb;
        --primary-hover: #1d4ed8;
        --danger: #dc2626;
        --success: #16a34a;
        --gray-50: #f9fafb;
        --gray-200: #e5e7eb;
        --gray-600: #4b5563;
        --gray-900: #111827;
        --border-radius: 12px;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    .paciente-index {
        padding: 24px;
        max-width: 1400px;
        margin: 0 auto;
        font-family: 'Segoe UI', system-ui, sans-serif;
        color: var(--gray-900);
    }

    /* Header Section */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    /* Botón Crear */
    .btn-create {
        background-color: var(--success);
        color: white;
        padding: 8px 16px;
        border-radius: var(--border-radius);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        font-size: 14px;
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
    }

    .btn-create:hover {
        background-color: #15803d;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
        color: white;
    }

    /* === BUSCADOR GLOBAL ESTILO SPOTLIGHT === */
    .search-container {
        position: relative;
        margin-bottom: 25px;
        max-width: 600px;
    }

    .search-input {
        width: 100%;
        padding: 15px 20px 15px 50px;
        border: 1px solid transparent;
        border-radius: 15px;
        background: white;
        font-size: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        width: 20px;
        height: 20px;
    }

    /* Grid View Styling */
    .grid-view {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid var(--gray-200);
    }

    .grid-view table {
        width: 100%;
        border-collapse: collapse;
    }

    .grid-view thead {
        background: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .grid-view th {
        padding: 18px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--gray-600);
        letter-spacing: 0.5px;
    }

    .grid-view th a {
        color: inherit;
        text-decoration: none;
    }

    .grid-view td {
        padding: 16px 18px;
        vertical-align: middle;
        border-bottom: 1px solid var(--gray-50);
        color: #374151;
    }

    .grid-view tbody tr:hover {
        background-color: #f8fafc;
    }

    /* === VISTA MÓVIL - CARTAS === */
    @media (max-width: 768px) {

        /* Ocultar tabla */
        .grid-view table {
            display: none;
        }

        /* Mostrar como cartas */
        .grid-view {
            background: transparent;
            box-shadow: none;
            border: none;
            overflow: visible;
        }

        /* Container de cartas */
        .paciente-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 0;
        }

        /* Tarjeta individual */
        .paciente-card {
            background: white;
            border-radius: 10px;
            padding: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f1f3;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: none;
        }

        .paciente-card.visible {
            display: block;
        }

        .paciente-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
            border-color: #e5e7eb;
        }

        .paciente-card:active {
            transform: translateY(0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Información en cartas */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f3f4f6;
        }

        .card-name {
            font-weight: 600;
            font-size: 15px;
            color: #111827;
            flex: 1;
        }

        .card-id {
            background: #f3f4f6;
            color: #6b7280;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            margin-left: 8px;
            border: 1px solid #e5e7eb;
        }

        .card-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 0;
            border-bottom: 1px solid #f9fafb;
            font-size: 13px;
        }

        .card-row:last-child {
            border-bottom: none;
        }

        .card-label {
            color: #9ca3af;
            font-weight: 500;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .card-value {
            color: #374151;
            font-weight: 500;
            text-align: right;
            flex: 1;
            margin-left: 12px;
        }

        .card-value.email {
            color: #1e40af;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 12px;
        }

        .card-value.phone {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 12px;
        }

        .card-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-status.active {
            background: #dcfce7;
            color: #166534;
        }

        .card-status.inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Actions en cartas */
        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }

        .card-action-btn {
            flex: 1;
            padding: 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            color: #374151 !important;
            background: #f9fafb;
        }

        .card-action-btn:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
            transform: translateY(-1px);
        }

        .card-action-btn:active {
            transform: translateY(0);
        }

        .card-action-btn.view {
            border-color: #bfdbfe;
            color: #1e40af !important;
            background: #eff6ff;
        }

        .card-action-btn.view:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .card-action-btn.edit {
            border-color: #bbf7d0;
            color: #15803d !important;
            background: #f0fdf4;
        }

        .card-action-btn.edit:hover {
            background: #dcfce7;
            border-color: #86efac;
        }

        .card-action-btn.delete {
            border-color: #fecaca;
            color: #7f1d1d !important;
            background: #fef2f2;
        }

        .card-action-btn.delete:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .card-action-btn svg {
            width: 15px;
            height: 15px;
            stroke-width: 2;
        }

        /* Mensaje de no encontrado */
        #table-empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .summary {
            display: none;
        }

        .pagination {
            display: none;
        }

        /* Buscar sigue visible */
        .search-container {
            max-width: 100%;
        }
    }

    /* Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Action Buttons */
    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: var(--gray-600);
        transition: all 0.2s;
        margin-right: 4px;
        background: #f3f4f6;
    }

    .view-btn:hover {
        background: var(--primary);
        color: white;
    }

    .edit-btn:hover {
        background: var(--success);
        color: white;
    }

    .delete-btn:hover {
        background: var(--danger);
        color: white;
    }

    /* Pagination */
    .pagination {
        display: flex;
        list-style: none;
        gap: 5px;
        padding: 20px;
        justify-content: center;
        margin: 0;
    }

    .pagination li a {
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        color: var(--gray-600);
        text-decoration: none;
        font-size: 14px;
    }

    .pagination li.active a {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .pagination li.disabled a {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .summary {
        padding: 15px 20px;
        color: #6b7280;
        font-size: 0.9rem;
        border-bottom: 1px solid #eee;
    }

    /* Mensaje de no encontrado */
    #table-empty-state {
        display: none;
        text-align: center;
        padding: 40px;
        color: #6b7280;
    }
</style>

<div class="paciente-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            '<svg style="width:20px; height:20px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Nuevo Paciente',
            ['create'],
            ['class' => 'btn-create']
        ) ?>
    </div>

    <div class="search-container">
        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input type="text" id="table-search-input" class="search-input" placeholder="Buscar paciente por nombre, teléfono, email..." autocomplete="off">
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // IMPORTANTE: 'filterModel' => null elimina los inputs individuales
        'filterModel' => null,
        'tableOptions' => ['class' => 'table'],
        'options' => ['class' => 'grid-view'],
        'summaryOptions' => ['class' => 'summary'],
        'pager' => [
            'options' => ['class' => 'pagination'],
            'linkOptions' => ['class' => 'page-link'],
            'activePageCssClass' => 'active',
            'disabledPageCssClass' => 'disabled',
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'width:50px; text-align:center'],
                'contentOptions' => ['style' => 'text-align:center; color:#9ca3af'],
            ],
            [
                'attribute' => 'id',
                'headerOptions' => ['style' => 'width: 80px;'],
                'contentOptions' => ['style' => 'font-weight: bold; color: #6b7280;'],
            ],
            [
                'attribute' => 'nombre',
                'headerOptions' => ['style' => 'min-width: 120px;'],
                'contentOptions' => ['style' => 'font-weight: 600; color: #111827;'],
            ],
            [
                'attribute' => 'apellidos',
            ],
            [
                'attribute' => 'telefono',
                'contentOptions' => ['style' => 'font-family: monospace; font-size: 0.95rem;'],
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'contentOptions' => ['style' => 'color: var(--primary);'],
            ],
            [
                'attribute' => 'fecha_nacimiento',
                'format' => 'date',
            ],
            [
                'attribute' => 'status',
                'headerOptions' => ['style' => 'width: 120px; text-align: center'],
                'contentOptions' => ['style' => 'text-align: center'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->status == 1
                        ? '<span class="status-badge active">Activo</span>'
                        : '<span class="status-badge inactive">Inactivo</span>';
                },
            ],
            [
                'class' => ActionColumn::class,
                'header' => 'Acciones',
                'headerOptions' => ['style' => 'width: 150px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center; white-space: nowrap;'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('<svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>', $url, ['class' => 'action-btn view-btn', 'title' => 'Ver']);
                    },
                    'update' => function ($url) {
                        return Html::a('<svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>', $url, ['class' => 'action-btn edit-btn', 'title' => 'Editar']);
                    },
                    'delete' => function ($url) {
                        return Html::a('<svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>', $url, [
                            'class' => 'action-btn delete-btn',
                            'title' => 'Eliminar',
                            'data-confirm' => '¿Eliminar paciente?',
                            'data-method' => 'post'
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <div id="table-empty-state">
        <p>No se encontraron pacientes que coincidan con tu búsqueda.</p>
    </div>

</div>