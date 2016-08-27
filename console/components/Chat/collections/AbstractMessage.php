<?php
namespace console\components\Chat\collections;

use Yii;
use yii\mongodb\Exception;
use yii\mongodb\ActiveRecord;

use console\components\Chat\interfaces\InMessage;

abstract class AbstractMessage extends ActiveRecord implements InMessage
{
    public function save ($runValidation = true, $attributeNames = NULL) 
    {
        return parent::save($runValidation, $attributeNames);
    }

    public function delete () {
        return parent::delete();
    }

    public function blocked ($args)
    {
        
    }

    abstract public function getText();
    abstract public function getReadedParticipants();
    abstract public function getCreatorId();
    abstract public function getConversationId();

    abstract public function addReadedParticipant($user_id);
    
    abstract public function isNewMessageForParticipant($user_id);
   
    public static function collectionName()
    {
        return 'messages';
    }

    public function beforeValidate ()
    {
        if ($this->isNewRecord) {
            $this->addReadedParticipant($this->getCreatorId());
        }
        return true;
    }

    public function validateReadedParticipants ($attributeName, $params) 
    {
        $attribute = $this->{$attributeName};
        
        if (!is_array($attribute) || count($attribute) == 0) {
            $this->addError($attributeName, 'Current user not added in "Readed participants" ');
        }
    }

    public function attributes()
    {
        return [
            '_id', 
            
            'text',

            'readed_participants',
            'conversation_id',
            
            'creator_id',
            
            'created_at', 
            'updated_at',
            
            'custom_data'
        ];
    }

    public function rules () {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => time()],
            [['creator_id', 'text', 'created_at', 'updated_at', 'conversation_id'], 'required'],
            ['readed_participants', 'each', 'rule' => ['integer']],
            ['readed_participants', 'validateReadedParticipants']
        ];
    }
    
}

 