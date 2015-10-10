<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use backend\libraries\Sitesession;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/orders.js', ['depends' => [yii\web\JqueryAsset::className()]]);
?>
<div class="goods-index">
	<table style="width:100%">
		<tr>
			<td><h1><?= Html::encode($this->title) ?></h1></td>
			<td style="width:30%" align="right">
				<?php if($user) { ?>
			    	<?= Html::a('切换用户:'. $user['username'], ['order/clear-order-session'], ['class' => 'btn btn-warning']) ?>
			    <?php } ?>
			    <?php $count_html =  $count > 0 ? "(".$count.")" : ""; ?>
				<?= Html::a('Shopping Car'.$count_html , ['order/details'], ['class' => 'btn btn-warning jsCount']) ?>
			    <?= Html::a('Clear!', ['order/clear-order-session'], [
			        'class' => 'btn btn-danger',
			        'data' => [
			            'confirm' => 'Are you sure you want to clear session ?',
			            'method' => 'post',
			        ],
			    ]) ?>
			</td>
		</tr>
	</table>
	
    <?php if(is_null($user) || !$user) { ?>
	    <?= GridView::widget([
	        'dataProvider' => $dataProvider,
	        'filterModel' => $searchModel,
	        'columns' => [
	            ['class' => 'yii\grid\SerialColumn'],
	            'id',
	            'username',
	            //'email:email',
	            //'role',
	            'title',
	            'address',
	            // 'postal',
	            // 'city',
	            // 'created_at',
	            // 'updated_at',
				[
	                'label'=>'选择',
	                'format'=>'raw',
	                'value' => function($data){
	                    $url = "index.php?r=order/fix-user&user_id=".$data->id;
	                    return Html::a('选择用户', $url, ['title' => '选择']); 
	                }
	            ],  
	        ],
	    ]); ?>

	<?php } else { ?>
		
		<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,        
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'goods_id',
            'goods_sn',
            'goods_name',
            'shop_price',
            'promote_price',
            // 'created_at',
            // 'updated_at',
            [
                'label'=>'添加到购物列表',
                'format'=>'raw',
                'value' => function($data){
                	$session = new Sitesession();
                	$user_id = $session->getUserId();
                	$goods= $session->getGoods($user_id);

                	if(!empty($goods) && in_array($data->goods_id, $goods)) {
                		$text = "已添加";
                		$actionadd = 0;
                	} else {
                		$text = "添加";
                		$actionadd = 1;
                	}                	                 
                   	return Html::a($text, null, ['title' => '添加', 'goods_id' => $data->goods_id, 'actionad' => $actionadd, 'class' => 'jsAddGoods']); 
                }
            ],  
        ],
    ]); ?>
	<?php } ?>
</div>
<script>
	var js_context = {};
	js_context.ajax_add_order_url = '<?= Url::toRoute('ajax/order/add-order');?>';
</script>
