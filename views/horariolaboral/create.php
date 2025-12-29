<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Horariolaboral $model */

$this->title = 'Create Horariolaboral';
$this->params['breadcrumbs'][] = ['label' => 'Horariolaborals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="horariolaboral-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
