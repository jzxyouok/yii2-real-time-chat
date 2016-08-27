<?php
namespace console\components\Chat\collections;

use Yii;
use yii\mongodb\Exception;
use yii\mongodb\Query;

use console\components\Chat\controllers\Period;

class Message extends AbstractMessage
{
	public function setValues($creatorId, $text, $conversationId)
	{
		$this->creator_id = $creatorId;
		$this->text = $text;
		$this->conversation_id = $conversationId;
		$this->save();
	}
    public function getText ()
    {
    	return $this->text;
    }
    
    public function getReadedParticipants ()
    {
        return $this->readed_participants;
    }
    
    public function setReadedParticipants ($value)
    {
        $this->readed_participants = $value;
    	return true;
    }

    public function isReadedParticipant ($userId)
    {
    	$isReadedParticipant = in_array($this->getReadedParticipants(), $userId);

    	return $isReadedParticipant;
    }
    public function getCreatorId ()
    {
    	return $this->creator_id;
    }
    public function getConversationId ()
    {
    	return $this->conversation_id;
    }

    public function addReadedParticipant ($userId)
    {
        $readedParticipants = $this->getReadedParticipants();

        if (is_array($readedParticipants) && in_array($userId, $readedParticipants)) {
            return true;
        }
        
        $readedParticipants[] = $userId;

    	$this->setReadedParticipants($readedParticipants);

    	return true;
    }
    
    public function isNewMessageForParticipant($userId)
    {

    }


    public static function findAllByConversationId($conversationId, $asArray = null)
    {
    	$messages = Message::find()->where(['conversation_id' => $conversationId]);


        if (!empty($asArray)) {
            $messages = $messages->asArray();
        }

        $result = $messages->all();

    	return $result;
    }

    public static function findRecentByConversationId ($conversationId, $period = null, $asArray = null)
    {
    	$period = self::validatePeriod($period);

    	$messages = Message::find()
    		->where(['conversation' => $conversationId])
    		->andWhere(['<=', 'created_at', $period->getStart()])
    		->andWhere(['>=', 'created_at', $period->getEnd()]);

    	if (!empty($asArray)) {
    		$messages = $messages->asArray();
    	}

    	return $messages;
    }
    private static function validatePeriod ($period)
    {
    	$period = new Period($period);

    	return $period;
    } 
}

 