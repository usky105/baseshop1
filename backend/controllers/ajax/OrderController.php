<?php
namespace backend\controllers\ajax;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\libraries\Sitesession;

/**
 * Order controller
 */
class OrderController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['add-order', 'del-good'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionAddOrder()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $goods_id = $data['goods_id'];
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $session = new Sitesession();
            $user_id = $session->getUserId();

            if(!$user_id) {
                return [
                    'goods_id' => 0
                ];
            }

            $goods = $session->getGoods($user_id);
            $shopping_car = $goods ? $goods : array();   
            $actionadd = 0;
            if(in_array($goods_id, $shopping_car)) {
                foreach ($shopping_car as $key => $gid) {
                    if($gid == $goods_id) {
                        unset($shopping_car[$key]);
                    }
                }
            } else {
                $actionadd = 1;
                $shopping_car[] = $goods_id;
                $shopping_car = array_unique($shopping_car);
            }

            $session->storeGoods($user_id, $shopping_car);
            $count = count($shopping_car);
            return [
                'goods_id' => $goods_id,
                'shopping_car' => $shopping_car,
                'actionadd' => $actionadd, 
                'count' => $count
            ];
        }
    }

    public function actionDelGood()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $goods_id = $data['goods_id'];
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $session = new Sitesession();
            $user_id = $session->getUserId();

            if(!$user_id) {
                return [
                    'goods_id' => 0,
                    'count' => 0
                ];
            }

            $goods = $session->getGoods($user_id);
            $shopping_car = $goods ? $goods : array();   
            
            if(in_array($goods_id, $shopping_car)) {
                foreach ($shopping_car as $key => $gid) {
                    if($gid == $goods_id) {
                        unset($shopping_car[$key]);
                    }
                }
            } 

            $session->storeGoods($user_id, $shopping_car);
            $count = count($shopping_car);

            return [
                'goods_id' => $goods_id,
                'shopping_car' => $shopping_car,
                'count' => $count
            ];

        }
    }  
}
