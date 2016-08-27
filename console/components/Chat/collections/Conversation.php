<?php
namespace console\components\Chat\collections;

use Yii;
use yii\mongodb\Exception;
use yii\mongodb\ActiveRecord;

use common\models\User;

class Conversation extends AbstractConversation
{
    public static $participantsFieldsList = ['id', /*'username',*/ 'first_name'];

    public function blocked ($args) {}

    public function addMessage($creatorId, $text)
    {
        $message = new Message();

        $message->setValues($creatorId, $text, $this->_id);

        return $message;
    }
 
    public function getMessages ($args)
    {
        $this->loadMessages();
        return $this->messages;
    }

    public function getRecentMessages ()
    {
        $this->loadMessages(true);
        return true;
    }

    private function loadMessages ($recent = null)
    {
        if (empty($this->messages)) {
            if (!empty($recent)) {
                $this->obtainRecentMessages();
                return true;
            }
            $this->obtainMessages();
        }
        return true;
    }

    public function obtainOtherData ()
    {
        $this->obtainMessages();
        $this->obtainParticipantsData();
    }

    private function obtainMessages()
    {
        $this->messages = Message::findAllByConversationId($this->_id);
    }
    private function obtainRecentMessages()
    {
        $this->messages = Message::findRecentByConversationId($this->_id);
    }

    public function getAllMessages ()
    {
        $this->loadMessages();
        return $this->messages;
    }
    public function getUnreadMessages ($args)
    {

    }
    public function getMessagesByUserId ($userId)
    {

    }    
    public function getMessage ($message_id, $args)
    {

    }

    public function getCreatedAt ()
    {
        return $this->created_at;
    }
    public function getUpdatedAt ()
    {
        return $this->updated_at;
    }        
    public function getDescription ()
    {
        return $this->description;
    }
    public function getCustomData ()
    {
        return $this->custom_data;
    }
    public function getParticipants ()
    {
        return $this->participants;
    }

    public function getCreatorId ()
    {
        return $this->creator_id;
    }
    
    public function getObjectId ()
    {
        return $this->_id;
    }
    
    public function isParticipantByUserId ($userId)
    {
        $userId = intval($userId);

        $collection = Yii::$app->mongodb->getCollection('conversations');

        $result = $collection->aggregate([
            [
                '$match' => [
                    'participants' => $userId,
                ]
            ],
            [
                '$match' => [
                    '_id' => $this->getObjectId()
                ]
            ]
        ]);

        if (count($result) === 0) {
            return false;
        }

        return true;
    }

    public function existsPrivateConversationByUserIds ($firstUserId, $secondUserId)
    {
        $firstUserId = intval($firstUserId);
        $secondUserId = intval($secondUserId);
        $_ids = self::getIdsMatchParticipants([
            [
                '$match' => [
                    '$or' => [
                        [
                            'participants' => [$firstUserId, $secondUserId]
                        ],
                        [
                            'participants' => [$secondUserId, $firstUserId],
                        ]
                    ]
                ]
            ]
        ]);

        if ($_ids) {
            return $_ids;
        }
        return false;
    }

    public function includeOfParticipant($userId)
    {
        $userId = intval($userId);

        $isNewRecord = $this->isNewRecord;

        if ($isNewRecord) {
            $participants = $this->getParticipants();
            array_push($participants, $userId);
            $this->participants = $participants;
            return true;
        }

        if ($this->isParticipantByUserId($userId)) {
            return false;
        }

        array_push($this->participants, $userId);
        $this->save();

        return true;
    }

    public function excludeOfParticipant($userId)
    {
        $userId = intval($userId);

        foreach ($this->participants as $key => $participant) {
            if ($participant === $userId) {
                unset($this->participants[$key]);
                $this->save();
                return true;
            }
        }
        
        return false;
    }



    public function isPrivate ($participantId = null)
    {
        $participants = $this->getParticipants();

        $isParticipantUser = $this->isParticipantByUserId($participantId);

        if (count($participants) === self::NUMBER_PARTICIPANTS_CONSIDERED_PRIVATE_CONVERSATION && $isParticipantUser) {
            return true;
        }
        return false;
    }

    public static function findAllByCreatorId($creatorId) {
        $conversations = Conversation::find()->where(['creator_id' => $creatorId])->all();
        
        $conversations = self::getParticipantsDataOfConversations($conversations);

        foreach ($conversations as $key => $conversation) {
            $conversations[$key]->obtainMessages();
        }

        return $conversations;
    }

    public static function findAllByUserId($userId)
    {
        $userId = intval($userId);

        $_ids = self::getIdsWhereUserByIdParticipant($userId);
        $conversations = self::findAllByUserIds($_ids);
        $result = self::loadMessagesToConversations($conversations);
        $result = self::getParticipantsDataOfConversations($result);

        return $result;
    }

    public static function getIdsWhereUserByIdParticipant ($userId)
    {
        $userId = intval($userId);

        $_ids = self::getIdsMatchParticipants([
            [
                '$match' => [
                    'participants' => $userId,
                ]
            ],
            [
                '$group' => [
                    '_id' => '$_id'
                ]
            ]
        ]);

        return $_ids;
    }

    public static function getIdsMatchParticipants ($match)
    {
        $collection = Yii::$app->mongodb->getCollection('conversations');

        $result = $collection->aggregate($match);

        $_ids = [];

        foreach ($result as $row) {
            $_ids[] = $row['_id'];
        }

        return $_ids;
    }

    public static function findAllByUserIds ($_ids)
    {
        $conversations = Conversation::find()->where([
            'in', '_id', $_ids
        ])->all();

        return $conversations;
    }

    public static function loadMessagesToConversations ($conversations)
    {
        $result = [];
        foreach ($conversations as $conversation) {
            $conversation->obtainMessages();
            $result[] = $conversation;
        }
        return $result;
    }

    public static function getParticipantsDataOfConversations ($conversations)
    {
        foreach ($conversations as $key => $conversation) {
            $conversations[$key]->participantsData = self::getParticipantsData($conversation);
        }
        return $conversations;
    }

    public function obtainParticipantsData ()
    {
        $this->participantsData = self::getParticipantsData($this);
    }

    public static function getParticipantsData (Conversation $conversation)
    {
        if (empty($conversation)) {
            return false;
        }

        $condition = [];

        $users = User::find()->select(self::$participantsFieldsList);

        foreach ($conversation->participants as $participant) {
            $users = $users->orWhere(['id' => $participant]);
        }

        $usersData = $users->asArray()->all();

        return $usersData;
    }

    public static function getModel($conversationId)
    {
        $conversation = Conversation::findOne($conversationId);

        if (empty($conversation)) {
            return false;
        }

        $conversation->obtainOtherData();
        
        return $conversation;
    }
}

 