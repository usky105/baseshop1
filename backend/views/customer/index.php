<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Customer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            'username',
            'tel',
            //'email:email',
            //'role',
            'title',
            'address',
            // 'postal',
            // 'city',
            // 'created_at',
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
            [
                'label'=>'操作',
                'format'=>'raw',
                'value' => function($data){
                    $url = "index.php?r=order/user-orders&customer_id=".$data->id;
                    return Html::a('查看订单', $url, ['title' => '查看订单']); 
                }
            ],  
        ],
    ]); ?>

</div>
