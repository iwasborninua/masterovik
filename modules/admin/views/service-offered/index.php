<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ServiceOfferedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Service Offereds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-offered-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Service Offered', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            'description',
            'reason',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{accept}{delete}',
                'buttons' => [
                    'accept' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Принять'),
                            'style' => 'margin: 0 10px;',
                            'class' => ['text-success'],
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Удалить'),
                            'class' => 'text-danger'
                        ]);
                    }

                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        $url ='/admin/service-offered/delete?id=' . $model->id;
                        return $url;
                    }

                    if ($action === 'accept') {
                        $url ='/admin/service-offered/accept?id=' . $model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]);

    $this->registerJs("
    $('.text-danger').on('click', function () {
        var that = this;
        $.post(this.href, function () {
            $(that).parents('tr').fadeOut(500);
        });

        return false;
    });");

    $this->registerJs("
    $('.text-success').on('click', function () {
        var that = this;
        $.post(this.href, function () {
            $(that).parents('tr').fadeOut(500);
        });

        return false;
    });");

    ?>


</div>
