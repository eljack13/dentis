<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;

AppAsset::register($this);

$isLoggedIn = !Yii::$app->user->isGuest;

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/asd.ico')]);

/* ===== ICONS ===== */
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css');

/* ===== LIBRERÍAS EXTERNAS ===== */
$this->registerCssFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/locales-all.global.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js', ['position' => \yii\web\View::POS_END]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-width-collapsed: 80px;
            --header-height-mobile: 60px;
            /* Altura de la barra superior móvil */
            --sidebar-bg: #1e293b;
            --sidebar-color: #94a3b8;
            --sidebar-active-bg: #3b82f6;
            --body-bg: #f8fafc;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--body-bg);
            overflow-x: hidden;
        }

        <?php if ($isLoggedIn): ?>

        /* === 1. SIDEBAR (BASE) === */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-color);
            display: flex;
            flex-direction: column;
            z-index: 1050;
            /* Z-Index alto para estar sobre todo en móvil */
            padding: 1rem;
            white-space: nowrap;
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05);
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* === 2. MOBILE HEADER (SOLO VISIBLE EN MÓVIL) === */
        .mobile-header {
            display: none;
            /* Oculto en escritorio */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height-mobile);
            background: white;
            z-index: 1040;
            padding: 0 1rem;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* === 3. OVERLAY (FONDO OSCURO AL ABRIR EN MÓVIL) === */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1045;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* === 4. RESPONSIVE LOGIC (MEDIA QUERIES) === */

        /* --- ESCRITORIO (>= 768px) --- */
        @media (min-width: 768px) {

            /* El botón toggle de escritorio */
            #desktopToggle {
                display: flex;
            }

            /* Lógica de colapsado mini-sidebar */
            .app-sidebar.collapsed {
                width: var(--sidebar-width-collapsed);
                padding: 1rem 0.5rem;
            }

            .app-main {
                margin-left: var(--sidebar-width);
                padding: 2rem;
                transition: margin-left var(--transition-speed);
            }

            .app-main.expanded {
                margin-left: var(--sidebar-width-collapsed);
            }

            /* Elementos a ocultar en mini-sidebar */
            .app-sidebar.collapsed .brand-text,
            .app-sidebar.collapsed .nav-text,
            .app-sidebar.collapsed .user-info-text,
            .app-sidebar.collapsed .btn-logout-text {
                opacity: 0;
                display: none;
            }

            .app-sidebar.collapsed .app-brand {
                justify-content: center;
                padding-left: 0;
            }

            .app-sidebar.collapsed .nav-link {
                justify-content: center;
            }

            .app-sidebar.collapsed .user-footer {
                text-align: center;
            }
        }

        /* --- MÓVIL (< 768px) --- */
        @media (max-width: 767.98px) {

            /* Mostrar Header Móvil */
            .mobile-header {
                display: flex;
            }

            /* Ocultar botón de escritorio */
            #desktopToggle {
                display: none !important;
            }

            /* Sidebar oculta por defecto (fuera de pantalla) */
            .app-sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
                /* Siempre ancho completo al abrir */
            }

            /* Sidebar abierta en móvil */
            .app-sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.2);
            }

            /* Contenido principal baja para dejar espacio al header */
            .app-main {
                margin-left: 0 !important;
                padding: 1rem;
                padding-top: calc(var(--header-height-mobile) + 1rem);
                min-height: 100vh;
            }

            /* Ajustes visuales móvil */
            .app-brand {
                display: none;
            }

            /* Ocultamos logo dentro del sidebar porque ya está en el header */
            .nav-pills {
                margin-top: 1rem;
            }
        }

        /* === ESTILOS GENERALES COMPARTIDOS === */
        .app-main {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Estilos de Navegación */
        .nav-pills .nav-link {
            color: var(--sidebar-color);
            padding: 0.9rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            height: 50px;
            transition: all 0.2s;
        }

        .nav-pills .nav-link i {
            font-size: 1.35rem;
            min-width: 30px;
            display: flex;
            justify-content: center;
        }

        .nav-text {
            margin-left: 12px;
        }

        .nav-pills .nav-link.active {
            background-color: var(--sidebar-active-bg);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Footer Usuario */
        .user-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Botón Toggle Escritorio */
        #desktopToggle {
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Botón Hamburguesa Móvil */
        .mobile-toggle {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #334155;
            padding: 0.5rem;
        }

        /* Logo Móvil */
        .mobile-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .mobile-brand i {
            color: var(--sidebar-active-bg);
            margin-right: 8px;
        }

        /* Anti-flicker */
        html.preload-collapsed .app-sidebar {
            width: var(--sidebar-width-collapsed) !important;
            transition: none !important;
        }

        html.preload-collapsed .app-main {
            margin-left: var(--sidebar-width-collapsed) !important;
            transition: none !important;
        }

        <?php else: ?>.app-main {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #cbd5e1 100%);
        }

        <?php endif; ?>.card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #64748b;
        }
    </style>
</head>

<body>
    <?php $this->beginBody() ?>

    <?php if ($isLoggedIn): ?>

        <script>
            if (window.innerWidth >= 768 && localStorage.getItem('sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('preload-collapsed');
            }
        </script>

        <header class="mobile-header">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="bi bi-list"></i>
            </button>

            <a href="<?= Yii::$app->homeUrl ?>" class="mobile-brand">
                <i class="bi bi-heart-pulse-fill"></i> Theeth Care
            </a>

            <div style="width: 40px;"></div>
        </header>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <aside class="app-sidebar" id="sidebar">
            <a href="<?= Yii::$app->homeUrl ?>" class="app-brand d-none d-md-flex">
                <i class="bi bi-heart-pulse-fill"></i>
                <span class="brand-text">Theeth Care<span class="fw-light opacity-75"></span></span>
            </a>

            <div class="d-md-none d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-outline-light border-0" id="mobileCloseSidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <?php
            $menuItems = [
                ['label' => 'Resumen', 'icon' => 'bi-grid-1x2-fill', 'url' => ['/site/index'], 'c' => 'site'],
                ['label' => 'Agenda', 'icon' => 'bi-calendar-week-fill', 'url' => ['/cita/index'], 'c' => 'cita'],
                ['label' => 'Pacientes', 'icon' => 'bi-person-vcard-fill', 'url' => ['/paciente/index'], 'c' => 'paciente'],
                ['label' => 'Servicios', 'icon' => 'bi-clipboard2-pulse-fill', 'url' => ['/servicio/index'], 'c' => 'servicio'],
                ['hr' => true],
                ['label' => 'Usuarios', 'icon' => 'bi-shield-lock-fill', 'url' => ['/usuario/index'], 'c' => 'usuario'],
            ];
            ?>

            <ul class="nav nav-pills flex-column mb-auto">
                <?php foreach ($menuItems as $item): ?>
                    <?php if (isset($item['hr'])): ?>
                        <hr class="my-3 border-light opacity-25">
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?= \yii\helpers\Url::to($item['url']) ?>"
                                class="nav-link <?= Yii::$app->controller->id == $item['c'] ? 'active' : '' ?>"
                                title="<?= $item['label'] ?>">
                                <i class="bi <?= $item['icon'] ?>"></i>
                                <span class="nav-text"><?= $item['label'] ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <div class="user-footer">
                <div class="d-flex align-items-center mb-3 px-1 user-info-wrapper">
                    <div class="rounded-circle bg-white text-primary d-flex justify-content-center align-items-center" style="width: 38px; height: 38px; min-width: 38px;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <div class="user-info-text ms-3 overflow-hidden">
                        <small class="d-block text-white-50" style="font-size: 0.75rem;">Sesión</small>
                        <div class="text-truncate text-white fw-bold" style="max-width: 130px;">
                            <?= Html::encode(Yii::$app->user->identity->email ?? 'Usuario') ?>
                        </div>
                    </div>
                </div>

                <?= Html::beginForm(['/site/logout'], 'post') ?>
                <button type="submit" class="btn btn-outline-light w-100 btn-sm rounded-pill d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="btn-logout-text ms-2">Salir</span>
                </button>
                <?= Html::endForm() ?>
            </div>
        </aside>
    <?php endif; ?>

    <main class="app-main" id="main-content">
        <div class="<?php echo $isLoggedIn ? 'container-fluid' : 'container'; ?>">
            <?php if ($isLoggedIn): ?>

                <div class="d-flex align-items-center mb-3">
                    <button id="desktopToggle" class="border-0 shadow-sm d-none d-md-flex">
                        <i class="bi bi-list fs-4"></i>
                    </button>

                    <div class="ms-md-3 flex-grow-1">
                        <?php if (!empty($this->params['breadcrumbs'])): ?>
                            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'], 'options' => ['class' => 'breadcrumb']]) ?>
                        <?php endif ?>
                    </div>
                </div>

            <?php endif ?>

            <?= Alert::widget() ?>
            <?= $content ?>
        </div>

        <?php if ($isLoggedIn): ?>
            <footer class="mt-auto py-4 text-center text-muted small">
                &copy; Dentis <?= date('Y') ?>
            </footer>
        <?php endif; ?>
    </main>

    <?php $this->endBody() ?>

    <?php if ($isLoggedIn): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Elementos
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('main-content');
                const overlay = document.getElementById('sidebarOverlay');
                const html = document.documentElement;

                // Botones
                const desktopToggle = document.getElementById('desktopToggle');
                const mobileToggle = document.getElementById('mobileToggle');
                const mobileClose = document.getElementById('mobileCloseSidebar');

                // --- LÓGICA ESCRITORIO ---
                const isDesktop = window.innerWidth >= 768;
                if (isDesktop) {
                    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('expanded');
                    }
                    setTimeout(() => html.classList.remove('preload-collapsed'), 100);
                }

                // Evento Toggle Escritorio
                if (desktopToggle) {
                    desktopToggle.addEventListener('click', function() {
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('expanded');
                        localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                    });
                }

                // --- LÓGICA MÓVIL ---

                function openMobileMenu() {
                    sidebar.classList.add('mobile-open');
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden'; // Evitar scroll del fondo
                }

                function closeMobileMenu() {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                }

                if (mobileToggle) mobileToggle.addEventListener('click', openMobileMenu);
                if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
                if (overlay) overlay.addEventListener('click', closeMobileMenu);

                // Cerrar menú móvil al hacer clic en un link (UX)
                sidebar.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 768) closeMobileMenu();
                    });
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>
<?php $this->endPage() ?>