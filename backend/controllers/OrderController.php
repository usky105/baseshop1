<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\AdminLoginForm;
use yii\filters\VerbFilter;
use backend\models\SignupForm;
use backend\models\CustomerSearch;
use backend\models\GoodsSearch;
use backend\libraries\Sitesession;
use backend\libraries\Pdf;
use backend\models\Orders;
use backend\models\OrderGoods;
use backend\models\Customer;
use backend\models\Dictionary;
use backend\models\Goods;
use yii\data\ActiveDataProvider;
use backend\libraries\Helper;

/**
 * Site controller
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
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['create-excel', 'ledger','changer-order', 'user-orders','mpdf-bl','mpdf-avoir','mpdf-facture', 'valider-order', 'index', 'fix-user', 'sample', 'clear-order-session', 'details'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionChangerOrder()
    {
        $data = Yii::$app->request->get();
        if(!isset($data['order_id'])) {
            echo "缺少参数order_id";
            exit;
        }
        $order_id = $data['order_id'];

        $session = new Sitesession();
        $session->setOrderId($order_id); //设置order_id

        $order = Orders::findOne($order_id);       
        $user_id = $order->action_user;
        $session->storeUserId($user_id); //设置user_id

        $ordergoods = $order->ordergoods;
        $goods_arr = array();
        foreach ($ordergoods as $good) {
           $goods_arr[] = $good->goods_id;
        }
        $goods_arr = array_unique($goods_arr);

        $session->storeGoods($user_id, $goods_arr);
        $this->redirect(array('/order/index'));
    }

    public function actionUserOrders()
    {        
        $data = Yii::$app->request->get();
        if(isset($data['customer_id']) && $data['customer_id'] != 0) {
            $customer = Customer::findOne($data['customer_id']);
            //如何数据表内orders改变了，但此处没有任何改变，可能是缓存关系，先运行下unset,
            //unset($customer->orders);
            //$customer->orders 返回Order 对象数组
            $orders = $customer->getOrders();

            /**
             * 方法二
             * $customer = Customer::findOne($data['customer_id']);
             * $orders = Orders::find()->where(['action_user' => $data['customer_id']]);
             */
            //            
        } else {            
            $orders = new Orders();
            $orders = $orders->find();
            //$orders = Orders::find()->orderBy('order_id')->all();
        }  

        $dataProvider = new ActiveDataProvider([
            'query' => $orders,
            'pagination' => [
                    'pagesize' => '20',
            ]
        ]);

        return $this->render('user_orders', [
            'model' => $orders, 
            'dataProvider' => $dataProvider            
            ]);
    }

    public function actionIndex()
    {
        $session = new Sitesession();
        $user_id = $session->getUserId();

        $user = null;
        $goods = null;
        $count = 0;
        if($user_id) {
            $goods = $session->getGoods($user_id);
            $count = is_array($goods) ? count($goods) : 0;
            $user = Customer::findOne($user_id);
            //获取user_id用户信息
            /*$connection = \Yii::$app->db;
            $command = $connection->createCommand('SELECT * FROM customer WHERE id='.$user_id);
            $user = $command->queryOne();*/

            $searchModel = new GoodsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 6);
        }  else {
            $searchModel = new CustomerSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }  

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user,
            'goods' => $goods,
            'count' => $count
        ]);
    }

    public function actionFixUser($user_id)
    {
        $session = new Sitesession();
        $user_id = $session->storeUserId($user_id);
        $this->redirect(array('/order/index'));
    }

    public function actionClearOrderSession()
    {
        $session = new Sitesession();
        $session->clearGoodsSession();
        $session->clearUserSession();  
        $session->clearOrderSession();      
        //echo "撤销完成";
        $this->redirect(array('/order/index')); 
    }

    public function actionDetails()
    {
        $session = new Sitesession();
        $user_id = $session->getUserId();
        $goods = $session->getGoods($user_id);
        $order_id = $session->getOrderId();
        $order = null;
        if(!empty($order_id)) {
            $order = Orders::findOne($order_id);
        }

        $user = null;
        if($user_id) {
            //获取user_id用户信息
            /*$connection = \Yii::$app->db;
            $command = $connection->createCommand('SELECT * FROM customer WHERE id='.$user_id);
            $user = $command->queryOne();*/
            $user = Customer::findOne($user_id);
        }        

        $count = 0;
        if(is_array($goods) && !empty($goods)) {
            /*$str = '';
            foreach ($goods as $key => $value) {
                if(empty($str)) {
                    $str = $str." ".$value;
                } else {
                    $str = $str.", ".$value;
                }
            }  */ 
            $count = count($goods);

            //获取goods
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                // 所有的查询都在主服务器上执行
                //$goods = $connection->createCommand('SELECT * FROM goods WHERE goods_id in ('.$str.')')->queryAll();
                $goods = Goods::findAll($goods);
                $transaction->commit();
            } catch(\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            $goods = false;
        }        

        return $this->render('details', [
            'user' => $user,
            'goods' => $goods,
            'count' => $count,
            'order' => $order
        ]);
    }

    public function actionValiderOrder()
    {
        $data = Yii::$app->request->get();
        if(isset($data['order_id'])) {
            $order_id = $data['order_id'];
            $connection = \Yii::$app->db;
            $command = $connection->createCommand('SELECT * FROM orders WHERE order_id='.$order_id);
            $order = $command->queryOne();   
            $user = $connection->createCommand('SELECT * FROM customer WHERE id ='.$order['action_user'])->queryOne();        
            $ordergoods = $connection->createCommand('SELECT * FROM order_goods WHERE order_id ='.$order_id)->queryAll();
            return $this->render('orderlist', [
                'order' => $order,
                'ordergoods' => $ordergoods,
                'user' => $user
            ]); 
        } else {
            $datapost = Yii::$app->request->post();
            $count = $datapost['goods_count'];
            $type = $datapost['type'];
            $order_id = $datapost['order_id'];
            $remise = $datapost['remise'];
            $str = '';
            $goods_nums_arr = $goods_price_arr = array();

            for ($i=0; $i < $count  ; $i++) { 
                $good_id = $datapost['good_id'][$i];
                $good_num = $datapost['number'][$i];
                $good_price = $datapost['pprice'][$i];
                $str = empty($str) ? $str.$good_id : $str.",".$good_id;
                $goods_nums_arr[$good_id] = $good_num;
                $goods_price_arr[$good_id] = $good_price;
            }              

            $session = new Sitesession();
            $user_id = $session->getUserId();
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {               

                if($order_id != 0) {
                    $insert_order_id = $order_id;
                    //删除order_id原来对应的goods
                    OrderGoods::deleteAll('order_id = :order_id', [':order_id' => $order_id]);
                } else {
                    //添加orders
                    $insert_order_id = 0;
                    $orders = new Orders();
                    $orders->action_user = $user_id;
                    $orders->type = $type;
                    if($orders->save()) {
                        $insert_order_id = $orders->order_id;
                    }
                }

                // 所有的查询都在主服务器上执行
                $goods = $connection->createCommand('SELECT * FROM goods WHERE goods_id in ('.$str.')')->queryAll();
                //$connection->createCommand("UPDATE user SET username='demo' WHERE id=1")->execute();

                $total = 0;
                //添加goods
                foreach ($goods as $i => $good) {
                    //处理商品数量，1.包含'|'，前面是数量，后面是赠送。。 2.不包含'|'，就是数量
                    $num_str = $goods_nums_arr[$good['goods_id']];
                    $num_buy = 1;
                    $num_free = 0;
                    if(strstr($num_str, "|")) {
                        $num_arr = explode("|", $num_str);
                        $num_buy = isset($num_arr[0]) && is_numeric($num_arr[0]) && intval($num_arr[0]) >= 0  ? intval($num_arr[0]) : 1;
                        $num_free = isset($num_arr[1]) && is_numeric($num_arr[1]) && intval($num_arr[1]) >= 0 ? intval($num_arr[1]) : 0;
                    } else {
                        $num_buy = is_numeric($num_str) && intval($num_str) > 0 ? intval($num_str) : 1 ;
                    }                    

                    if($num_buy) {
                        $price = is_numeric($goods_price_arr[$good['goods_id']]) && $goods_price_arr[$good['goods_id']] != 0.00 ? $goods_price_arr[$good['goods_id']] : $good['shop_price']; 
                        $order_goods = new OrderGoods();
                        $order_goods->order_id = $insert_order_id;
                        $order_goods->goods_id = $good['goods_id'];
                        $order_goods->goods_name = $good['goods_name']; 
                        $order_goods->goods_sn = $good['goods_sn'];
                        $order_goods->goods_number = $num_buy;
                        $order_goods->market_price = $price;
                        $sum = $price * $num_buy;
                        $order_goods->sum_price = $sum;
                        $total += $sum;                       
                        $order_goods->save();  
                    }

                    if($num_free) {
                        $order_free_goods = new OrderGoods();
                        $order_free_goods->order_id = $insert_order_id;
                        $order_free_goods->goods_id = $good['goods_id'];
                        $order_free_goods->goods_name = $good['goods_name']; 
                        $order_free_goods->goods_sn = $good['goods_sn'];
                        $order_free_goods->goods_number = $num_free;
                        $order_free_goods->market_price = 0;
                        $order_free_goods->sum_price = 0;                       
                        $order_free_goods->save();
                    }
                }

                $connection->createCommand()->update('orders', ['sum_price' => $total, 'remise' => $remise], 'order_id = '.$insert_order_id)->execute();
                $transaction->commit();
                //情况session:一定要先清空goods再清空user
                $session->clearGoodsSession();
                $session->clearUserSession(); 
                $session->clearOrderSession(); 

                $this->redirect(array('/order/valider-order','order_id'=>$insert_order_id)); 

            } catch(\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    public function actionMpdfBl()
    {
        $this->createMpdf('mpdf_bl');
    }

    public function actionMpdfAvoir()
    {
        $this->createMpdf('mpdf_avoir', true);
    }

    public function actionMpdfFacture()
    {
        $this->createMpdf('mpdf_facture', true);
    }

    /**
     * start MPDF
     */
    protected function createMpdf($view, $isfac = false){
        $data = Yii::$app->request->get();
        if(isset($data['order_id'])) {
            $order_id = $data['order_id'];
            $connection = \Yii::$app->db;
            $command = $connection->createCommand('SELECT * FROM orders WHERE order_id='.$order_id);
            $order = $command->queryOne(); 
            $user = $connection->createCommand('SELECT * FROM customer WHERE id ='.$order['action_user'])->queryOne();        
            $ordergoods = $connection->createCommand('SELECT * FROM order_goods WHERE order_id ='.$order_id)->queryAll();
            
            if($isfac && $order['fac_no'] == 0) {
                Dictionary::handleData("system","factory_no");
                $dic = Dictionary::findOne("system:factory_no");
                $fac_no = $dic->auto;
                $order_model = Orders::findOne($order_id);
                $order_model->fac_no = $fac_no;
                $order_model->save();
                $order['fac_no'] = $fac_no;
            }

            $mpdf = new Pdf();            
            $header = $this->renderPartial('mpdf/header', ['title' => 'nagoya']);
            $footer = $this->renderPartial('mpdf/footer');

            $mpdf->setHeader($header); //设置PDF页眉内容
            $mpdf->setFooter($footer); //设置PDF页脚内容
            $content = $this->renderPartial($view, [
                    'order' => $order,
                    'ordergoods' => $ordergoods,
                    'user' => $user
                    ]);
            $mpdf->Output($content);
            exit;
        } else {
            echo "缺少参数";
            exit;
        }
    }

    public function actionLedger()
    {
        $data = Yii::$app->request->get();
        $year = isset($data['year']) ? $data['year'] : 0;
        $month = isset($data['month']) ? $data['month'] : 0;
        if($year != 0 && $month != 0) {
            $t = Helper::getFristAndLasttimeline($year, $month);

            $firstday = $t['firstday'];
            $lastday = $t['lastday'];
            var_dump($t);
            $orders = Orders::find()->where('created_at >= :start AND created_at < :end AND fac_no != 0 ', [':start' => $firstday, ':end' => $lastday]);
            $dataProvider = new ActiveDataProvider([
                'query' => $orders,
                'pagination' => [
                        'pagesize' => '20',
                ]
            ]);

            return $this->render('ledger', [
                'model' => $orders, 
                'dataProvider' => $dataProvider,
                'year' => $year,
                'month' => $month,
                'firstday' => $firstday,
                'lastday' => $lastday       
                ]);
        } else {
            return $this->render('ledger', [
                'model' => null,
                ]);
        }        
    }

    public function actionCreateExcel()
    {
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("PHPExcel Test Document")
                             ->setSubject("PHPExcel Test Document")
                             ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                             ->setKeywords("office PHPExcel php")
                             ->setCategory("Test result file");

        //设置当前的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        //设置工作簿默认的样式
        $objPHPExcel->getDefaultStyle()->getFont()->setName('宋体');
        //设置sheet的name
        $objPHPExcel->getActiveSheet()->setTitle('Nagoya');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15.75);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15.75);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15.4);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13.75);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(11.15);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14.13);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8.25);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8.5);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8.5);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8.5);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8.5);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8.5);    
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16.75);

        $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'IMPRESSION France');
        //$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16); 
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('D1:F1');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Chiffre d\'affaires en');
        //$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(14); 
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('G1:H1');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '10-2014');
        //$objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setSize(14); 
        $objPHPExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('A3:A4');
        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Date Facture');
        //$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(11);
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('B3:B4');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Nom de Client');
        //$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setSize(11);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('C3:C4');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Numéro Facture');
        //$objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setSize(9);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('D3:F3');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', 'HT €  ');
        $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->setCellValue('D4', 'Ventes hors UE');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Ventes UE');
        $objPHPExcel->getActiveSheet()->setCellValue('F4', 'Vente en Fr');
        //$objPHPExcel->getActiveSheet()->getStyle('D4')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('D4')->getFont()->setSize(8);
        //$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setSize(8);
        //$objPHPExcel->getActiveSheet()->getStyle('F4')->getFont()->setName('宋体');
        $objPHPExcel->getActiveSheet()->getStyle('F4')->getFont()->setSize(8);
        $objPHPExcel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('G3:G4');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', 'TVA €');
        $objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('H3:H4');
        $objPHPExcel->getActiveSheet()->setCellValue('H3', 'TTC €');
        $objPHPExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('I3:L3');
        $objPHPExcel->getActiveSheet()->setCellValue('I3', 'Mode de règlement');
        $objPHPExcel->getActiveSheet()->getStyle('I3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->setCellValue('I4', 'CHQ');
        $objPHPExcel->getActiveSheet()->setCellValue('J4', 'VER');
        $objPHPExcel->getActiveSheet()->setCellValue('K4', 'TRAIT');
        $objPHPExcel->getActiveSheet()->setCellValue('L4', 'ESP');
        $objPHPExcel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->mergeCells('M3:M4');
        $objPHPExcel->getActiveSheet()->setCellValue('M3', 'Date Règlement');
        $objPHPExcel->getActiveSheet()->getStyle('M3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('M3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);   
        

        //数据库数据
        $data = Yii::$app->request->get();
        $firstday = isset($data['firstday']) ? $data['firstday'] : 0;
        $lastday = isset($data['lastday']) ? $data['lastday'] : 0;
        if($firstday != 0 && $lastday != 0) {           
            $orders = Orders::find()->where('created_at >= :start AND created_at < :end AND fac_no != 0 ', [':start' => $firstday, ':end' => $lastday])->all();
        } else {
            echo "参数错误";
            exit;
        }

        foreach ($orders as $i => $order) {
            $j = $i+5;
            if($order->remise > 0) {
                $remise_html = "(remise-".$order->remise."%)";
                $price = $order->sum_price * (100 - $order->remise) / 100;
            } else {
                $remise_html = "";
                $price = $order->sum_price;
            }

            $price = $order->type == 1 ? $price : '-'.$price;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, Date('Y/m/d', $order->created_at));
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $order->customer->username);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $order->fac_no);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, '');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $j, $price.$remise_html );
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $j, Helper::getTVA($price).$remise_html);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $j, Helper::getTTC($price).$remise_html);

            /*$objPHPExcel->getActiveSheet()->getStyle('A' . $j)->getFont()->setName('宋体');
            $objPHPExcel->getActiveSheet()->getStyle('B' . $j)->getFont()->setName('宋体');
            $objPHPExcel->getActiveSheet()->getStyle('C' . $j)->getFont()->setName('宋体');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $j)->getFont()->setName('宋体');
            $objPHPExcel->getActiveSheet()->getStyle('E' . $j)->getFont()->setName('宋体');
            $objPHPExcel->getActiveSheet()->getStyle('F' . $j)->getFont()->setName('宋体');
            $objPHPExcel->getActiveSheet()->getStyle('G' . $j)->getFont()->setName('宋体');
            $objPHPExcel->getActiveSheet()->getStyle('H' . $j)->getFont()->setName('宋体');*/
        }

        /*$objWriteHTML = new \PHPExcel_Writer_HTML($objPHPExcel); //输出网页格式的对象
        $objWriteHTML->save("php://output");
        exit;*/    

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');        
        $objWriter->save(str_replace('.php', Date("Y",$firstday)."_".Date("m",$firstday).'.xlsx', __FILE__));
        echo "Success !<br>";
        echo "The EXCEL FILE is in :".dirname(__FILE__);
        exit;
    }



    


}
