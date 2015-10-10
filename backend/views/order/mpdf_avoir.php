<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */
use backend\libraries\Helper;
$this->title = 'NAGOYA';

?>
<style type="text/css">
.table1 {
  width: 100%;
  max-width: 100%;
  margin-bottom: 20px;
}
.table-bordered {
	border: 1px solid #ddd;
}
.table1 tr {
  display: table-row;
  vertical-align: inherit;
  border-color: inherit;
}
.table1 > thead > tr > th, .table1 > tbody > tr > th, .table1 > tfoot > tr > th, .table1 > thead > tr > td, .table1 > tbody > tr > td, .table1 > tfoot > tr > td {
  padding: 8px;
  line-height: 1.42857143;
  vertical-align: top;
  border-top: 1px solid #ddd;
}
.table-striped > tbody > tr:nth-of-type(odd) {
  background-color: #f9f9f9;
}
.table1 td, th {
  display: table-cell;
  vertical-align: inherit;
  border: 1px solid #ddd;
}
</style>

<div>
	<div style="width:100%">
		<div style="clear:both;width:60%; margin-left:auto; maigin-right:auto">
			<h1>AVOIR</h1>
		</div>
		<div style="float:left;width:30%;">
			DATE : <?php echo  Date("Y-m-d", $order['created_at']) ?>		
		</div>
		<div style="float:right;width:30%">AVOIR N : <?php echo $order['fac_no'] ?></div>
	</div>

	<div style="margin-left:15px; padding:10px; width:30%; float:left; height:140px; ">
		IMPRESSION FRANCE <br>
		24 Rue de Champ Chardon<br>
		92100 D'ISSY LES MOULINEAUX <BR>
		DES NANTERRE 799 342 985
	</div>

	<div style="width:40%; float:right; height:140px; padding:10px;">
		<?php echo "对方公司名字(".$user['title'].")"; ?><br>
		<?php echo "telephone : ".$user['tel']; ?><br>
		<?php echo $user['address']; ?><br>
		<?php echo "postal : ".$user['postal']; ?><br>
		<?php echo "city : ".$user['city']; ?><br>
	</div>

	<div style="width:100%; clear:both">
		<table class="table1 table-striped table-bordered">
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
				<tr><td colspan="3"></td><td>Remise</td><td><?php echo  $order['remise'] ?></td></tr>
			<?php } else { $sumprice = $order['sum_price']; } ?>
			<tr><td colspan="3"></td><td>MONTANT H.T</td><td><?php echo $sumprice;  ?>  </td></tr>
			<tr><td colspan="3"></td><td>T.V.A (20%)</td><td><?php echo Helper::getTVA($sumprice) ; ?></td></tr>
			<tr><td colspan="3"></td><td>PRIX TOTAL T.T.C</td><td><?php echo  Helper::getTTC($sumprice); ?></td></tr>
		</table>
	</div>
</div>