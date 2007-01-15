<?php

class ChatBufferRecord extends TActiveRecord
{
	public $id;
	public $for_user;
	public $from_user;
	public $message;
	private $_created_on;

	public static $_tablename='chat_buffer';

	public function getCreated_On()
	{
		if($this->_created_on === null)
			$this->_created_on = time();
		return $this->_created_on;
	}

	public function setCreated_On($value)
	{
		$this->_created_on = $value;
	}

	public static function finder()
	{
		return parent::getRecordFinder('ChatBufferRecord');
	}

	public function saveMessage()
	{
		foreach(ChatUserRecord::finder()->findAll() as $user)
		{
			$message = new self;
			$message->for_user = $user->username;
			$message->from_user = $this->from_user;
			$message->message = $this->message;
			$message->save();
			if($user->username == $this->from_user)
			{
				$user->last_activity = time(); //update the last activity;
				$user->save();
			}
		}
	}

	public function getUserMessages($user)
	{
		$content = '';
		foreach($this->findAll('for_user = ?', $user) as $message)
			$content .= $this->formatMessage($message);
		$this->deleteAll('for_user = ? OR created_on < ?', $user, time() - 300); //5 min inactivity
		return $content;
	}

	protected function formatMessage($message)
	{
		$user = htmlspecialchars($message->from_user);
		$content = htmlspecialchars($message->message);
		return "<div class=\"message\"><strong>{$user}:</strong> <span>{$content}</span></div>";
	}
}

?>