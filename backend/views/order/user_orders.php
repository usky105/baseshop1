<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\Customer;
use yii\grid\GridView;
use backend\libraries\Helper;
$this->title = 'Orders List';
//$this->registerJsFile('@web/js/my.js', ['depends' => [yii\web\JqueryAsset::className()]]);

?>
<div class="site-index">
    <table style="width:100%">
        <tr>
            <td>
                <?= Html::a('查看客户列表' , ['customer/index'], ['class' => 'btn  btn-success']) ?>
                <?= Html::a('所有订单' , ['order/user-orders'], ['class' => 'btn  btn-success']) ?>
                <?= Html::a('月结总账管理' , ['order/ledger'], ['class' => 'btn  btn-danger']) ?>
            </td>           
        </tr>
    </table>    
     
    <div class="jumbotron">
        <h1><?= Html::encode($this->title) ?></h1>  
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                    [
                        'label' => '订单编号', // 可选 复写
                        'attribute' => 'order_id'
                    ],
                    [
                        'label' => '客户名', // 可选 复写
                        'attribute' => 'action_user',
                        'format'=>'raw',
                        'value' => function($data){            
                            return Html::a($data->customer->username, ['order/user-orders', 'customer_id'=> $data['action_user']]);
                       }
                    ],
                    [
                        'label' => '原始总价', // 可选 复写
                        'attribute' => 'sum_price'
                    ],
                    [
                        'label' => 'Remise', // 可选 复写
                        'value' => function($data){                    
                            return $data['remise'] == 0 ? '-' : $data['remise'] . "%"; 
                        }
                    ], 
                    [
                        'label' => 'H.T', // 可选 复写
                        'value' => function($data){  
                            return number_format($data['sum_price'] * (1 - $data['remise']/100),2,",",".");
                        }
                    ], 
                    [
                        'label' => 'T.V.A', // 可选 复写
                        'value' => function($data){                    
                            return Helper::getTVA($data['sum_price'] * (1 - $data['remise']/100)); 
                        }
                    ],
                    [
                        'label' => 'T.T.C', // 可选 复写
                        'value' => function($data){       
                            return Helper::getTTC($data['sum_price'] * (1 - $data['remise']/100));
                        }
                    ],
                    [
                        'label' => '发票编号', // 可选 复写
                        'value' => function($data){                    
                            return $data['fac_no'] == 0 ? '-' : $data['fac_no']; 
                        }
                    ],              
                    //'created_at:datetime',
                    [
                        'attribute' => 'created_at',
                        'format' => ['date', 'php:Y/m/d H:i']
                    ],
                    [
                        'label'=>'操作',
                        'format'=>'raw',
                        'value' => function($data){                    
                            return Html::a('view', ['order/valider-order', 'order_id' => $data['order_id']], ['title' => '添加', 'target' => '_blank']); 
                        }
                    ],  
                ],
        ]); ?>
    </div>        
</div>
