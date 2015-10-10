<?php

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use backend\libraries\Helper;
$this->title = 'Ledger';
$this->registerJsFile('@web/js/orders.js', ['depends' => [yii\web\JqueryAsset::className()]]);

?>
<div class="site-index">
    <table style="width:100%">
        <tr>
            <td>
                <?= Html::a('查看所有订单' , ['order/user-orders'], ['class' => 'btn  btn-success']) ?>
                <?= Html::a('月结总账管理' , ['order/ledger'], ['class' => 'btn  btn-danger']) ?>
            </td>
            <td style="width:30%" align="right">                
            </td>
        </tr>
    </table>    
     
    <div class="jumbotron">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if($model) { ?>
            <h3><?= Html::encode($year) ?> 年 <?= Html::encode($month) ?> 月</h3>
            <?= Html::a('导出该月excel文件' , ['order/create-excel', 'firstday' => $firstday, 'lastday' => $lastday], ['class' => 'btn  btn-danger', 'target' => '_blank']) ?>
            
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
                            'attribute' => 'fac_no'
                        ],              
                        'created_at:datetime',
                        [
                            'label'=>'操作',
                            'format'=>'raw',
                            'value' => function($data){                    
                                return Html::a('view', ['order/valider-order', 'order_id' => $data['order_id']], ['title' => '添加', 'target' => '_blank']); 
                            }
                        ],  
                    ],
            ]); ?>
        <?php } else { ?>
            <h3>选择日期</h3>
            <?= Html::dropDownList('year',  null , [  date('Y') =>  date('Y'), 2014 =>'2014', 2015 =>'2015', 2016 =>'2016', 2017 =>'2017', 2018 =>'2018'], ['class' => 'jsYear']); ?> 
            <?= Html::dropDownList('month',  null , [  date('m') =>  date('m'), '01' =>'01', '02' =>'02', '03' =>'03', '04' =>'04', '05' =>'05', '06' =>'06', '07' =>'07', '08' =>'08', '09' =>'09', '10' =>'10', '11' =>'11', '12' =>'12'],  ['class' => 'jsMonth']); ?>
            <?= Html::a('确定' , null, ['class' => 'jsSelectDate']) ?>
        <?php } ?>        
    </div>        
</div>
<script>
    var js_context = {};
    js_context.ledger_url = '<?= Url::toRoute('order/ledger');?>';
</script>
