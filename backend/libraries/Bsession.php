<?php

namespace backend\libraries;

use Yii;

class Bsession 
{
	protected $session;

	function __construct() 
	{
		$this->session = Yii::$app->session;	
		$this->open();	
	}

	protected function open()
	{
		if (!$this->session->isActive) {
			$this->session->open();
		}		
	}

	public function close()
	{
		if($this->session->isActive) {
			$this->session->close();
		}
	}

	public function destroy()
	{
		$this->session->destroy();
	}

	public function set($key, $value)
	{
		$this->open();
		$this->session->set($key, $value);
	}

	public function get($key)
	{
		$this->open();
		return $this->session->get($key);
	}

	public function remove($key)
	{
		$this->open();
		$this->session->remove($key);
	}

}



