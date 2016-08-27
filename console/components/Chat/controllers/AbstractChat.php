<?php

namespace console\components\Chat\controllers;

use console\controllers\Daemon;
use console\components\Chat\interfaces\Chat;

use common\models\User;

abstract class AbstractChat implements Chat
{
	public $user;

	function __construct ($userId)
	{
		$this->loadUser($userId);
	}

	public function getUserId ()
	{
		return $this->user->id;
	}

	private function loadUser ($userId)
	{
		$userId = intval($userId);

		$this->user = User::findOne(['id' => $userId]);

		if (empty($this->user)) {
			throw new \Exception('User with identity key[id]: "'.$userId.'" not found.');
		}
		return true;
	}

	abstract public function getAllConversations () ;
	abstract public function getConversationsByUserId ($user_id);
	
	abstract public function getCountMessagesByUserId ($user_id);
	abstract public function getCountNewMessagesByUserId ($user_id);
	
	abstract public function getConversationById($converation_id);

	abstract public function isParticipantOfConversation($user_id, $conversation_id);
}