<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Fotosesion $model */

$this->title = 'Create Fotosesion';
$this->params['breadcrumbs'][] = ['label' => 'Fotosesions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fotosesion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
