<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

/* ===== META ===== */
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
$this->registerLinkTag([
    'rel' => 'icon',
    'type' => 'image/x-icon',
    'href' => Yii::getAlias('@web/favicon.ico')
]);

/* ===== FULLCALENDAR (GLOBAL) ===== */
$this->registerCssFile(
    'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.css',
    ['position' => \yii\web\View::POS_HEAD]
);
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js',
    ['position' => \yii\web\View::POS_END]
);
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/locales-all.global.min.js',
    ['position' => \yii\web\View::POS_END]
);

/* ===== SWEETALERT2 ===== */
$this->registerCssFile(
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css',
    ['position' => \yii\web\View::POS_HEAD]
);
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
    ['position' => \yii\web\View::POS_END]
);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>
        <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top']
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto'],
            'items' => [
                ['label' => 'Inicio', 'url' => ['/site/index']],
                ['label' => 'Agenda', 'url' => ['/cita/index']],
                ['label' => 'Pacientes', 'url' => ['/paciente/index']],
                ['label' => 'Servicios', 'url' => ['/servicio/index']],
                ['label' => 'Horario', 'url' => ['/horariolaboral/index']],
                ['label' => 'Bloqueos', 'url' => ['/bloqueoagenda/index']],
                ['label' => 'Fotos', 'url' => ['/fotosesion/index']],
                ['label' => 'Usuarios', 'url' => ['/usuario/index']],
            ]
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto'],
            'items' => [
                Yii::$app->user->isGuest
                    ? ['label' => 'Login', 'url' => ['/site/login']]
                    : '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
                    . Html::submitButton(
                        'Salir (' . Html::encode(Yii::$app->user->identity->email) . ')',
                        ['class' => 'btn btn-link nav-link logout']
                    )
                    . Html::endForm()
                    . '</li>',
            ]
        ]);

        NavBar::end();
        ?>
    </header>

    <main class="flex-shrink-0">
        <div class="container">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
            <?php endif ?>

            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="mt-auto py-3 bg-light border-top">
        <div class="container text-muted d-flex justify-content-between">
            <div>&copy; Dentis <?= date('Y') ?></div>
            <div><?= Yii::powered() ?></div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>