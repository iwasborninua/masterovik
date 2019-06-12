<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Service', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'service_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
