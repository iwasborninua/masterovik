<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServiceOffered */

$this->title = 'Create Service Offered';
$this->params['breadcrumbs'][] = ['label' => 'Service Offereds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-offered-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
