<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id
 * @property string $username
 * @property string $tel
 * @property string $email
 * @property integer $role
 * @property string $title
 * @property string $address
 * @property string $postal
 * @property string $city
 * @property integer $created_at
 * @property integer $updated_at
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
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

    public function getOrders()
    {
        /**
        * 第一个参数为要关联的字表模型类名称，
        *第二个参数指定 通过子表的 customer_id 去关联主表的 id 字段
        */
        //return $this->hasMany(Orders::className(), ['action_user' => 'id']);
        return $this->hasMany(Orders::className(), ['action_user' => 'id'])->inverseOf('customer');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'city', 'title', 'address', 'postal'], 'required'],
            [['role', 'created_at', 'updated_at', 'tel'], 'integer'],
            [['username', 'email', 'title', 'address'], 'string', 'max' => 255],
            [['tel'], 'default','value' => 1],
            [['postal', 'city'], 'string', 'max' => 80],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'tel' => 'Telephone',
            'email' => 'Email',
            'role' => 'Role',
            'title' => 'Title',
            'address' => 'Address',
            'postal' => 'Postal',
            'city' => 'City',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
