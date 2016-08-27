<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use console\components\Chat\collections\Conversation;
use console\components\Chat\controllers\ChatServer;
use console\components\Chat\controllers\Chat;
use console\controllers\Daemon;

/* This method for only developing process, after finish developing process please delete */

function println ($outline)
{
    print("\033[32mLog: \033[0m".$outline."\n");
}

function errorln ($outline)
{
    print("\033[31mError: \033[0m".$outline."\n");
}

function newline()
{
    print("\n");
}


class TestChatController extends Controller
{
    private $chatServer;
    private $chat;
    private $conversation;

    private $daemon = null;

    private function createDaemon () {
        if (empty($this->daemon)) {
            $this->daemon = new Daemon();   
        }
    }

    public function actionChatServer () 
    {
        println('Start test ChatServer controller:');

        $this->chatServer = new ChatServer();
    }

    public function actionChatDaemon () 
    {
        println('Start test ChatServer controller:');
        
        $this->createDaemon();

        if ($this->daemon->runned()) {
            $this->daemon->destroy();
        }
        
        $this->daemon->run();

        $this->chatServer = new ChatServer();
    }

    public function actionStop() 
    {
        $this->createDaemon();

        if (!$this->daemon->runned()) {
            echo "Daemon not runned\n";
        }
        if ($this->daemon->destroy()) {
            echo "Daemon destroyed\n";
        } else {
            echo "Destroying failed\n";
        }
    }
    /**
     * Chat server status
     */
    public function actionStatus () 
    {
        $this->createDaemon();
        if (!$this->daemon->runned()) {
            echo "Daemon not runned\n";
            return;
        }
        echo "Daemon runned\n";
    }

    public function initChat ()
    {
        $this->chat = new Chat();
    }

    public function actionConversation () 
    {
        println('Start testing Conversation controller');

        $currentUserId = 3;
        $recepientUserId = 8;

        newline();
        $this->initConversation();
        newline();
        
        $addingDone = true;

         $this->conversationAdding ($currentUserId, $recepientUserId);  

        println('Conversation exists test: assert true');
        println(print_r($this->conversationExists($currentUserId, $recepientUserId), true));

        println('Conversation exists test: assert false');
        println(print_r($this->conversationExists($currentUserId, 999), true));
        
        newline();
        newline();
        println('Conversation Adding test done.');



        newline();
        newline();
        println('Conversation tested');
    }

    public function actionMessage () 
    {
        println('Begin test Message class:');

        $userId = 8;

        $chat = new Chat($userId);
        
        println('Created Chat instance with User ID: '.$userId);

        $conversation = $chat->addConversation(3);

        if ($conversation instanceof Conversation) {
            println('Status create conversation: created');
        } else {
            println('Status create conversation: exception');
            if (!($conversation['conversation'] instanceof Conversation)) {
                return errorln('Conversation not created.');
            }
            println('Conversation exsits');
            $conversation = $conversation['conversation'];
        }

        $conversation->addMessage($chat->user->id, 'Test message');
    }

    private function initConversation () 
    {
        println('Conversation init starting');

        if (!empty($this->conversation)) {
            println('Conversation not empty');
        }
        $this->conversation = new Conversation();

        println('Conversation init successful');
    }

    private function conversationAdding ($currentUserId, $recepientUserId) 
    {
        println('Testing do adding conversation document into MongoDB');
        
        $chat = new Chat(3);

        println('Status: '.var_export($chat->addConversation($recepientUserId), true));

        println('Saving conversation');
    }

    public function conversationExists ($currentUserId, $recepientUserId)
    {
        $conv = new Conversation();

        $res = $conv->existsPrivateConversationByUserIds($currentUserId, $recepientUserId);

        return $res; 
    }

    

    public function actionTestConversation ()
    {
        println($this->initConversation());

        println('Test adding conversation.');

        $this->testConversationAdd();
    }

    public function actionGetConversationsByCreatorId ()
    {
        $conversationsCreator = Conversation::findAllByCreatorId(3);

        var_dump(count($conversationsCreator));

        $conversationsUser = Conversation::findAllByUserId(2);

        var_dump(count($conversationsUser));
    }

    public function actionTestAggregate ()
    {
        $collection = Yii::$app->mongodb->getCollection('conversations');

        $result = $collection->aggregate([
            [
                '$match' => [
                    'participants' => 8,
                ]
            ],
            [
                '$match' => [
                    '_id' => '57bd10c2fe15360f1a5afce1'
                ]
            ]
        ]);

        var_dump(count($result));
    }

    public function actionTestAggregateGroup ()
    {
        $testedUserId = 8;
        $_ids = Conversation::getIdsWhereUserByIdParticipant($testedUserId);

        var_dump($_ids);

        echo "Test findAllByUserId\n";
        
        $conversations = Conversation::findAllByUserId($testedUserId);

        echo "\nIs private conversation: ";

        var_dump($conversations[0]->isPrivate(3));

        var_dump(count($conversations));
    }



    private function testConversationCollection () 
    {
        println($this->initConversation());

        println('Test adding conversation.');

        $this->testConversationAdd();

        $this->testFindConversationById($this->conversation->_id);

        $this->testDeleteConversationByCreatorId($this->conversation->creator_id);

    }



    

    private function testFindConversationById ($_id) 
    {
        $conversation = Conversation::findOne(['_id' => $_id]);

        if (empty($conversation->_id)) {
            throw new \Exception('Not found');
        } elseif ($conversation->_id != $_id) {
            throw new \Exception('_id not equal $conversation->_id = '.$conversation->_id.', $_id = '.$_id);
        }

        return println('Find one: test success');
    }

    private function testDeleteConversationByCreatorId ($user_id) 
    {
        Conversation::deleteAll(['creator_id' => $user_id]);

        return println('Find one: test success');
    }
}