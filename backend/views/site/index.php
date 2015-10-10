<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = 'NAGOYA 后台管理';
$this->registerJsFile('@web/js/my.js', ['depends' => [yii\web\JqueryAsset::className()]]);

?>
<div class="site-index">
    <div class="jumbotron">
        <h1>NAGOYA 后台管理</h1>
        <p class="lead">页面比较丑，凑合用吧！</p>
        <p><a class="btn btn-lg btn-success" href="<?= Url::toRoute('customer/index');?>">用户管理</a></p>
        <p><a class="btn btn-lg btn-success" href="<?= Url::toRoute('goods/index');?>">商品管理</a></p> 
        <p><a class="btn btn-lg btn-success" href="<?= Url::toRoute('order/index');?>">订货开单</a></p>
        <p>
            <a class="btn btn-lg btn-success" href="<?= Url::toRoute('order/user-orders');?>">成交记录</a><br>
        </p>
        <p><?= Html::a('月结总账管理' , ['order/ledger'], ['class' => 'btn  btn-danger']) ?></p>        
        <p>要美图，找静静 -- 我想静静，别打扰我</p>
    </div>
</div>