<?php

class HomePage extends TPage
{
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		if(!$this->User->IsGuest)
			$this->Theme='';
	}

	public function testClick($sender,$param)
	{
		if($sender->BackColor==='')
			$sender->BackColor='blue';
		else
			$sender->BackColor='';
		$this->TextBox->focus();
	}

	public function clickImage($sender,$param)
	{
		$this->TextBox->Text="You Clicked (".$param->X.", ".$param->Y.")";
	}

	public function linkClicked($sender,$param)
	{
		$sender->Text="Hello World";
	}
}

?>