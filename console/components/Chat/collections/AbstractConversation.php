<?php
namespace console\components\Chat\collections;

use Yii;
use yii\mongodb\Exception;
use yii\mongodb\ActiveRecord;

use console\components\Chat\interfaces\InMessage;
use console\components\Chat\interfaces\Conversational;

abstract class AbstractConversation extends ActiveRecord implements Conversational
{
    const MIN_PARTICIPANTS_COUNT = 2;
    const NUMBER_PARTICIPANTS_CONSIDERED_PRIVATE_CONVERSATION = 2;

    /*public $participantsData = null;*/

    public function save ($runValidation = true, $attributeNames = NULL) 
    {
        return parent::save($runValidation, $attributeNames);
    }

    public function delete () {
        return parent::delete();
    }

    abstract public function blocked ($args);   

    abstract public function addMessage($creatorId, $text);
 
    abstract public function getMessages ($args);
    abstract public function getRecentMessages ();
    abstract public function getAllMessages ();
    abstract public function getUnreadMessages ($args);
    abstract public function getMessagesByUserId ($userId);
    
    abstract public function getMessage ($message_id, $args);
    
    abstract public function getCreatedAt ();
    abstract public function getUpdatedAt ();
        
    abstract public function getDescription ();
    abstract public function getCustomData ();
    
    
    abstract public function isParticipantByUserId ($userId);
    abstract public function includeOfParticipant($userId);
    abstract public function excludeOfParticipant($userId);
    abstract public function getParticipants ();

    abstract public function getCreatorId ();

    abstract public function isPrivate ($participantId);
    
    public static function collectionName()
    {
        return 'conversations';
    }

    public function attributes()
    {
        return [
            '_id', 
            'creator_id', 
            'participants', 
            'description', 
            'created_at', 
            'updated_at', 
            'custom_data',

            'participantsData',
            'messages'
        ];
    }

    public function beforeValidate ()
    {
        if ($this->isNewRecord) {
            $this->includeOfParticipant($this->getCreatorId());
        }
        return true;
    }

    public function validateParticipants ($attributeName, $params) 
    {
        $attribute = $this->{$attributeName};
        
        if (!is_array($attribute) || count($attribute) < self::MIN_PARTICIPANTS_COUNT) {
            $this->addError($attributeName, 'The number of participants may not be less than '.self::MIN_PARTICIPANTS_COUNT);
        } else {
            $exists =$this->existsPrivateConversationByUserIds($attribute[0], $attribute[1]);
            if ($exists) {
                $this->addError($attributeName, 'Conversation exists');
            }
        }
    }

    public function rules () {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => time()],
            [['creator_id', 'participants', 'created_at', 'updated_at'], 'required'],
            ['participants', 'each', 'rule' => ['integer']],
            ['participants', 'validateParticipants']
        ];
    }
    
}

 