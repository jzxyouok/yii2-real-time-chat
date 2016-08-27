<?php

namespace console\components\Chat\interfaces;

interface Chat
{
	public function getAllConversations ();
	public function getConversationsByUserId ($user_id);
	
	public function getCountMessagesByUserId ($user_id);
	public function getCountNewMessagesByUserId ($user_id);
	
	public function getConversationById($converation_id);

	public function isParticipantOfConversation($user_id, $conversation_id);
}