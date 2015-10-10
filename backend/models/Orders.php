<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "orders".
 *
 * @property string $order_id
 * @property string $action_user
 * @property string $sum_price
 * @property integer $created_at
 * @property integer $updated_at
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
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
            [['sum_price'], 'number'],
            [['created_at', 'updated_at', 'fac_no','remise'], 'integer'],
            ['type', 'in', 'range' => [1, 2]],
            [['action_user'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'action_user' => 'Action User',
            'sum_price' => 'Sum Price',
            'fac_no' => 'Facture No',
            'remise' => 'Remise',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'action_user']);
    }


    public function getOrdergoods()
    {
        return $this->hasMany(OrderGoods::className(), ['order_id' => 'order_id']);
    }
}
