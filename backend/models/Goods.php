<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "goods".
 *
 * @property string $goods_id
 * @property string $goods_sn
 * @property string $goods_name
 * @property string $shop_price
 * @property string $promote_price
 * @property integer $created_at
 * @property integer $updated_at
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
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
            [['shop_price', 'promote_price'], 'number'],
            [['goods_sn', 'goods_name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['promote_price'], 'default','value' => 0],
            ['goods_sn', 'unique', 'message' => 'Goods_sn is unique.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'goods_sn' => 'Goods Sn',
            'goods_name' => 'Goods Name',
            'shop_price' => 'Shop Price',
            'promote_price' => 'Promote Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /*public function beforeValidate() {}
    public function afterValidate() {}*/

}
