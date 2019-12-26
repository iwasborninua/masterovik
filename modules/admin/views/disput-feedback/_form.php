<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DisputFeedback */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="disput-feedback-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'rate_id')->textInput() ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
