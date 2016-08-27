<?php

namespace console\components\Chat\controllers;

use console\components\Chat\collections\Conversation;
use console\components\Chat\collections\Message;
use common\models\User;

class Chat extends AbstractChat 
{
	public static $RESPONSE_STATUSES = [
		'USER_NOT_FOUND' => 'USER_NOT_FOUND',
		'CONVERSATION_ALREADY_EXISTS' => 'CONVERSATION_ALREADY_EXISTS'
	];
	public function addConversation($participantId)
	{	
		$participantId = intval($participantId);
		$conversation = new Conversation();

		$isValidParticipant = User::findOne(['id' => $participantId, 'status' => User::STATUS_ACTIVE]);

		if (empty($isValidParticipant)) {
			return [
				'status' => self::$RESPONSE_STATUSES['USER_NOT_FOUND']
			];
		}

		if ($_id = $conversation->existsPrivateConversationByUserIds($this->user->id, $participantId)) {
			$conversation = Conversation::getModel($_id);
			if ($conversation) {
				$conversation->obtainOtherData();
			}
			return [
				'status' => self::$RESPONSE_STATUSES['CONVERSATION_ALREADY_EXISTS'],
				'conversation' => $conversation
			];
		}

		$conversation->creator_id = $this->user->id;
        $conversation->participants = [$participantId];
        
        $conversation->save();

        $conversation->obtainOtherData();

        return $conversation;
	}

	public function getAllConversations ()
	{
		$conversations = Conversation::findAllByUserId($this->getUserId());
		return $conversations;	
	}

	public function getConversationsByUserId ($user_id)
	{
		$conversations = Conversation::findAllByUserId($user_id);
		return $conversations;
	}
	public function getConversationsByCreatorId ($user_id) {
		$conversations = Conversation::findAllByCreatorId($user_id);
		return $conversations;
	}
	
	public function getCountMessagesByUserId ($user_id) {}
	public function getCountNewMessagesByUserId ($user_id) {}
	
	public function getConversationById($converation_id) {}

	public function isParticipantOfConversation($user_id, $conversation_id) {}
}