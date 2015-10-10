<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "dictionary".
 *
 * @property string $dic_id
 * @property string $category
 * @property string $val
 * @property integer $auto
 * @property integer $created_at
 * @property integer $updated_at
 */
class Dictionary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary';
    }

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
            [['dic_id', 'category', 'val'], 'required'],
            [['val'], 'string'],
            [['auto', 'created_at', 'updated_at'], 'integer'],
            [['dic_id', 'category'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dic_id' => 'Dic ID',
            'category' => 'Category',
            'val' => 'Val',
            'auto' => 'Auto',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 参数必须严格按照格式要求输入，不然不插入数据而且还不报错
     * @param  string  $category        [description]
     * @param  string  $reference       [description]
     * @param  string  $val             [description]
     * @param  boolean $update_plus_one [description]
     * @return [type]                   [description]
     */
    public static function handleData($category, $reference, $val = 'SYSTEM')
    {
        $dic_id = $category.":".$reference;
        $dictionary = Dictionary::findOne($dic_id);

        if(is_null($dictionary)) {
            self::insertLine($category, $reference, $val);
        } else {
            if($val == 'SYSTEM') {
                $connection = \Yii::$app->db;
                $command = $connection->createCommand('UPDATE '.self::tableName().' SET auto= auto + 1 WHERE dic_id = "'.$dic_id.'"');
                $command->execute();
            } else if($dictionary->val != 'SYSTEM') {
                $dictionary->val = $val;
                $dictionary->save();
            }
        }
    }

    public static function insertLine($category, $reference, $val)
    {        
        $model = new Dictionary();
        $model->dic_id = $category.":".$reference;
        $model->category = $category;
        $model->val = $val;
        $model->save();                
    }
}
