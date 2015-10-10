<?php
namespace backend\libraries;

use mPDF;

class Pdf 
{
	protected $header = '';
	protected $footer = '';	

	public function setHeader($header)
	{
		$this->header = $header;
	}

	public function setFooter($footer)
	{
		$this->footer = $footer;
	}

	public function Output($html_body = '')
	{
		//解决中文乱码问题
        $mpdf = new mPDF('zh-CN','A4','',''); 
        //$mpdf=new mPDF('utf-8','A4','','宋体',0,0,20,10);

        //设置PDF页眉内容
        $header= $this->header;
         
        //设置PDF页脚内容
        $footer= $this->footer;
         
        //添加页眉和页脚到pdf中
        $mpdf->SetHTMLHeader($header);
        $mpdf->SetHTMLFooter($footer);            
        $mpdf->useAdobeCJK = true;
        $mpdf->WriteHTML($html_body);
        $mpdf->Output();
	}

}



