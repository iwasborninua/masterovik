<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DisputFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Спор об отзыве';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disput-feedback-index">

<!--    <h1>--><?//= Html::encode($this->title) ?><!--</h1>-->

<!--    <p>-->
<!--        --><?//= Html::a('Create Disput Feedback', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'Рейтинг',
                'value' => 'rate.rate',
                'contentOptions' => ['class' => 'col-md-1'],
                'headerOptions' => ['style' => 'color:#337ab7'],
                ],

            [
                'attribute' => 'Отзыв',
                'value' => 'rate.review',
                'contentOptions' => ['class' => 'col-md-4'],
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],

            [
                'attribute' => 'Жалоба',
                'value' => 'text',
                'contentOptions' => ['class' => 'col-md-4'],
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],

//            'text:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => "{accept} {reject}",
                'buttons' => [
                    'accept' => function ($url, $model) {
                        return Html::a('<button type="button" class="btn btn-success">Удовлетворить</button>', $url, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Принять'),
                            'style' => 'margin: 0 10px;',
                            'class' => ['text-success'],
                        ]);
                    },

                    'reject' => function ($url, $model) {
                        return Html::a('<button type="button" class="btn btn-danger">Отказать</button>', $url, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Удалить'),
                            'class' => 'text-danger'
                        ]);
                    },

                ],

                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'reject') {
                        $url ='/admin/disput-feedback/delete?id=' . $model->id;
                        return $url;
                    }

                    if ($action === 'accept') {
                        $url ='/admin/disput-feedback/accept?id=' . $model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>


</div>
