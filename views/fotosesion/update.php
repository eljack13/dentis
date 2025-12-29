<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Fotosesion $model */

$this->title = 'Update Fotosesion: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fotosesions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fotosesion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
