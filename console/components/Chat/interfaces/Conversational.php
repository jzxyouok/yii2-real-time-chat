<?php
namespace console\components\Chat\interfaces;

interface Conversational extends Collection
{
	public function addMessage($creatorId, $text);

	public function getMessages ($args);
	public function getRecentMessages ();
	public function getAllMessages ();
	public function getUnreadMessages ($args);
	public function getMessagesByUserId ($userId);
	
	public function getMessage ($message_id, $args);
	
	public function getCreatedAt ();
	public function getUpdatedAt ();
		
	public function getDescription ();
	public function getCustomData ();
	
	
	public function isParticipantByUserId ($userId);
	public function includeOfParticipant($userId);
	public function excludeOfParticipant($userId);
	public function getParticipants ();

	public function getCreatorId ();

	public function isPrivate ($participantId);
}