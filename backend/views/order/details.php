<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Order-Detail';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/orders.js', ['depends' => [yii\web\JqueryAsset::className()]]);
?>
<div>
    <table style="width:100%">
        <tr>
            <td><h1><?= Html::encode($this->title) ?></h1></td>
            <td style="width:30%" align="right">                
                <?php if($user) { ?>
                    <?= Html::label( "客户:".$user['username'] , ['order/clear-order-session'], ['class' => 'btn btn-warning']) ?>
                <?php } else { ?>
                    <?= Html::a('请选择客户', ['order/index'], ['class' => 'btn btn-danger']) ?>
                <?php } ?>
                <?= Html::a('继续Orders', ['order/index'], ['class' => 'btn btn-warning']) ?>
            </td>
        </tr>
    </table>

    <?php if($goods) { ?>
        <?php $form = ActiveForm::begin([
            'id' => 'form-order-valider',
            'action' => ['order/valider-order'],
        ]); ?>
        <?= Html::input("hidden", "goods_count", $count, array("class"=>"jsGoodsCount")); ?>
        <table id="order_goods" class="table table-striped table-bordered">
            <tr><td>商品SN</td><td>商品名字</td><td>商品价格</td><td>商品数量</td><td>自设价格</td><td>操作</td></tr>
            <?php foreach ($goods as $good): ?>
                <tr>
                    <td><?= Html::encode("{$good['goods_sn']}"); ?><?= Html::input("hidden", "good_id[]", $good['goods_id']); ?></td>
                    <td><?= Html::encode("{$good['goods_name']}"); ?></td>
                    <td><?= $good['shop_price']; ?></td>
                    <td><?= Html::input("text", "number[]", "1"); ?></td>
                    <td><?= Html::input("text", "pprice[]", "0.00"); ?></td>
                    <td><span class="jsDelGood" goods_id="<?= Html::encode("{$good['goods_id']}"); ?>">delete</span><input type="hidden" name="goods_id" value="<?= Html::encode("{$good['goods_id']}"); ?>" /></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan='6'>
                    <?php if($order) { ?>
                        修改 <br>
                        Order_id : <?= Html::encode("{$order->order_id}"); ?> <br>
                        TYPE : <?php if($order->type == 1) {echo "FACTURE";} else if($order->type == 2) {echo "AVOIR";} ?>
                        <?= Html::input("hidden", "order_id", $order->order_id); ?> 
                        <?= Html::input("hidden", "type", $order->type, array("class"=>'jsOrderType')); ?>
                        <br>
                        REMISE : <?= Html::input("text", "remise", $order->remise); ?> 
                    <?php } else { ?>
                        新建 <br>
                        <?= Html::dropDownList('type', null, [ '0' => '请选择类型', 1 =>'Facture', 2 =>'Avoir'], ['class' => 'jsOrderType']); ?> 
                        <?= Html::input("hidden", "order_id", '0'); ?> 
                        <br>
                        REMISE : <?= Html::input("text", "remise", '0'); ?> 
                    <?php } ?>                    
                </td>
            </tr>
            <tr>
                <td colspan='6'>
                    <?= Html::Button('Valider', ['class' => 'btn btn-primary jsValideOrder ', 'name' => 'signup-button']) ?>
                </td>
            </tr>
        </table>
        <?php ActiveForm::end(); ?>
    <?php } else { ?> 
        <div class="jumbotron"><h1>购物车为空！</h1></div>
    <?php } ?>
</div>

<script>
    var js_context = {};
    js_context.ajax_del_good_url = '<?= Url::toRoute('ajax/order/del-good');?>';
</script>
