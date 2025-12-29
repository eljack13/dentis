<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Horariolaboral $model */

$this->title = 'Update Horariolaboral: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Horariolaborals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="horariolaboral-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
