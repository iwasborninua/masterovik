<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Report */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="report-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Закрыть заказ', ['close-order', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отменить заказ', ['cansel-order', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'ID заказа',
                'attribute' => 'order_id',
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
                'label' => 'Отправил',
                'value' => function ($model) {
                    $result = \app\models\MobileUser::findOne(['id' => $model->user_id]);
                    return $result->first_name . " " . $result->last_name;
                },
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            'title',
            'description:ntext',
            [
                'label' => 'Время',
                'attribute' => 'create_at',
                'format' => ['date', 'php:d M Y'],
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
                'label' => 'Контакты заказчика',
                'value' => function ($model) {
                    $result = \app\models\Order::findOne(['id' => $model->order_id]);
                    return "email: " . $result->customer->email . " " . "phone: " . $result->customer->phone;
                },
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],

            [
                'label' => 'Контакты исполнителя',
                'value' => function ($model) {
                    $result = \app\models\Order::findOne(['id' => $model->order_id]);
                    return "email: " . $result->executor->email . " " . "phone: " . $result->executor->phone;
                },
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],

        ],
    ]) ?>

</div>
