<?php

namespace backend\libraries;

use Yii;
use backend\libraries\Bsession;

class Sitesession 
{
	protected $session;

	function __construct()
	{
		$this->session = new Bsession();
	}

	public function storeUserId($uid)
	{		
		$this->session->set("user_id", $uid);
	}

	public function getUserId()
	{
		return $this->session->get("user_id");
	}

	public function storeGoods($uid, $goods)
	{
		$key = "goods_id_".$uid;
		$this->session->set($key, $goods);
	}

	public function getGoods($uid)
	{		
		if($uid) {
			$key = "goods_id_".$uid;
			return $this->session->get($key);
		}
		return null;
	}	

	public function setOrderId($order_id)
	{
		$this->session->set("order_id", $order_id);
	}

	public function getOrderId()
	{
		return $this->session->get("order_id");
	}

	public function clearUserSession()
	{
		$this->session->remove("user_id");
	}

	public function clearGoodsSession()
	{
		$user_id = $this->getUserId();
		if(!empty($user_id)) {
			$this->session->remove("goods_id_".$user_id);
		}		
	}

	public function clearOrderSession()
	{
		$this->session->remove("order_id");
	}

}



