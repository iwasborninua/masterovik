<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DisputFeedback */

$this->title = 'Create Disput Feedback';
$this->params['breadcrumbs'][] = ['label' => 'Disput Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disput-feedback-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
