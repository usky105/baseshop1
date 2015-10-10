<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "order_goods".
 *
 * @property string $rec_id
 * @property string $order_id
 * @property string $goods_id
 * @property string $goods_name
 * @property string $goods_sn
 * @property integer $goods_number
 * @property string $market_price
 * @property string $sum_price
 * @property integer $created_at
 * @property integer $updated_at
 */
class OrderGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_id', 'goods_number', 'created_at', 'updated_at'], 'integer'],
            [['market_price', 'sum_price'], 'number'],
            [['goods_id', 'goods_number'], 'required'],
            [['goods_name'], 'string', 'max' => 120],
            [['goods_sn'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rec_id' => 'Rec ID',
            'order_id' => 'Order ID',
            'goods_id' => 'Goods ID',
            'goods_name' => 'Goods Name',
            'goods_sn' => 'Goods Sn',
            'goods_number' => 'Goods Number',
            'market_price' => 'Market Price',
            'sum_price' => 'Sum Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
