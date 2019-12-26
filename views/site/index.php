<?php

/* @var $this yii\web\View */

use app\models\MobileUser;

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="jumbotron">
        <h1>Тестовый сервант по портам!</h1>
        <hr>
        <p style="text-align: left">login: <b>port</b></p>
        <p style="text-align: left">pass: <b>parol123</b></p>

        <?php
            echo mktime();
        ?>
    </div>
</div>
