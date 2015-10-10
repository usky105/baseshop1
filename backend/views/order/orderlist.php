<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use backend\libraries\Helper;

$this->title = 'Order-Detail';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>
    <div style="margin-left:15px; padding:10px; width:30%; float:left; height:140px; ">
		NAGOYA<br>
		120 bd de montparnass<br>
		75015 paris
	</div>

	<div style="width:40%; float:right; height:140px; padding:10px;">
		<?php echo "对方公司名字(".$user['title'].")"; ?><br>
		<?php echo "telephone : ".$user['tel']; ?><br>
		<?php echo $user['address']; ?><br>
		<?php echo "postal : ".$user['postal']; ?><br>
		<?php echo "city : ".$user['city']; ?><br>
	</div>

	<div style="width:100%; clear:both">
		<table style="width:100%" class="table table-striped table-bordered">
			<tr>
				<td>goods_sn</td><td>goods_name</td><td>goods_number</td><td>price</td><td>sum_price</td>
			</tr>
			<?php foreach ($ordergoods as $key => $good) { ?>
			<tr>
				<td><?php echo $good['goods_sn']; ?></td>
				<td><?php echo $good['goods_name'].($good['market_price'] == 0 ? "(free)" : ''); ?></td>
				<td><?php echo $good['goods_number']; ?></td>
				<td><?php echo $good['market_price'] ?></td>
				<td><?php echo $good['sum_price'] ?></td>
			</tr>
			<?php } ?>
			<?php if($order['remise'] != 0) { $sumprice = $order['sum_price'] * (100 - $order['remise']) / 100; ?>
				<tr><td colspan="3"></td><td>原始价格</td><td><?php echo  $order['sum_price'] ?></td></tr>
				<tr><td colspan="3"></td><td>Remise</td><td><?php echo  $order['remise']."%" ?></td></tr>
			<?php } else { $sumprice = $order['sum_price']; } ?>
			<tr><td colspan="3"></td><td>MONTANT H.T</td><td><?php echo $sumprice;  ?>  </td></tr>
			<tr><td colspan="3"></td><td>T.V.A (20%)</td><td><?php echo Helper::getTVA($sumprice) ; ?></td></tr>
			<tr><td colspan="3"></td><td>PRIX TOTAL T.T.C</td><td><?php echo  Helper::getTTC($sumprice); ?></td></tr>
		</table>
	</div>
	<p>
		<a class="btn btn-lg btn-success" href="<?= Url::toRoute('order/mpdf-bl');?>&order_id=<?php echo $order['order_id'] ?>" target="_blank">bon de livraison</a>
	   	<?php if($order['type'] == 2) { ?>
	   		<a class="btn btn-lg btn-success" href="<?= Url::toRoute('order/mpdf-avoir');?>&order_id=<?php echo $order['order_id'] ?>" target="_blank">avoir</a>
		<?php } else if($order['type'] == 1) { ?>
			<a class="btn btn-lg btn-success" href="<?= Url::toRoute('order/mpdf-facture');?>&order_id=<?php echo $order['order_id'] ?>" target="_blank">facture</a>
		<?php } ?>
		<a class="btn btn-lg btn-danger" href="<?= Url::toRoute('order/changer-order');?>&order_id=<?php echo $order['order_id'] ?>" >修改</a>
	</p>
</div>
