<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Жалобы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
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
            [
                'label' => 'Заголовок жалобы',
                'attribute' => 'title',
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
                'label' => 'Время',
                'attribute' => 'create_at',
                'format' => ['date', 'php:d M Y'],
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => "{view}",
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<button type="button" class="btn btn-primary">Подробнее</button>', $url, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Подробнее'),
                            'style' => 'margin: 0 10px;',
                            'class' => ['text-success'],
                        ]);
                    },
                ],

                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url ='/admin/report/view?id=' . $model->id;
                        return $url;
                    }

                }
            ],
        ],
    ]); ?>


</div>
