<?php

namespace console\components\Chat\interfaces;

interface InMessage extends Collection
{
	public function getText();
	public function getReadedParticipants();
	public function getCreatorId();
	public function getConversationId();

	public function addReadedParticipant($user_id);
	
	public function isNewMessageForParticipant($user_id);
}