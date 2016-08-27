<?php
namespace console\components\Chat\collections;

use Yii;
use yii\mongodb\Exception;
use yii\mongodb\ActiveRecord;

class UserSecure extends ActiveRecord
{
    const ACCESS_TOKEN_LENGTH = 15;
    const WATER_TOKEN = 'just random test';
    const NOT_ALLOWED_RECONNECT_TIME = 2;

    public static $CONNECTION_STATUSES = [
        0 => ['value' => 'offline'],
        1 => ['value' => 'online']
    ];

    public function getUserId () {
        return $this->user_id;
    }
    public function getAccessToken ()
    {
        //echo "\n".$this->access_token."\n";
        if (strlen($this->access_token) !== self::ACCESS_TOKEN_LENGTH) {
            $this->access_token = $this->generateAccessToken();
            $this->save();
        }

        return $this->access_token;
    }

    public static function getAccessTokenByUserId ($userId) 
    {
        $userId = intval($userId);
           
        $userSecure = self::findDoc(['user_id' => intval($userId)]);
        return $userSecure->getAccessToken();
    }

    public static function getByUserId ($userId)
    {
        $userId = intval($userId);

        $userSecure = self::findDoc(['user_id' => intval($userId)]);

        if (empty($userSecure)) {
            throw new \Exception('Record with user_id = "'.$userId.'"not found.');
        }

        return $userSecure;
    }

    public static function updateLastConnection ($userId)
    {
        $userId = intval($userId);

        $userSecure = self::getByUserId($userId);

        $userSecure->last_connection = time();

        $userSecure->save();

        return true;
    }


    public static function allowedToReconnect ($userId)
    {
        $userId = intval($userId);

        $userSecure = self::getByUserId($userId);

        $allowedReconnectTime = $userSecure->getLastConnection() + self::NOT_ALLOWED_RECONNECT_TIME;

        $currentTime =  time();

        if ($allowedReconnectTime > $currentTime) {
            return false;
        }
        return true;
    }

    public function getLastConnection ()
    {
        return $this->last_connection;
    }

    public function isOnline ()
    {
        $isOnline = $this->getConnectionStatus() === 1;
        return $isOnline;
    }

    public function getConnectionStatus (boolean $asString = null)
    {
        if (!$asString) {
            return $this->connection_status;
        } else {
            return self::$CONNECTION_STATUSES[$this->connection_status];
        }
    }

    public function setConnectionStatus ($connectionCode)
    {
        $connectionCode = intval($connectionCode);

        if (!array_key_exists($connectionCode, self::$CONNECTION_STATUSES)) {
            throw new \Exception('Not valid connection code.');
        }

        $this->connection_status = $connectionCode;

        $this->save();
    }
    
    public static function collectionName()
    {
        return 'user-secure';
    }

    public function attributes()
    {
        return [
            '_id', 
            'user_id',
            'access_token',
            'connection_status',
            'last_connection',
            'created_at',
            'updated_at'
        ];
    }


    public function rules () 
    {
        return [
            [['created_at', 'updated_at', 'last_connection'], 'default', 'value' => time()],
            [['connection_status'], 'default', 'value' => 0],
            [['access_token','user_id','created_at', 'updated_at', 'connection_status'], 'required'],
        ];
    }

    public static function saveTokenByUserId ($user_id) {
        $user_id = intval($user_id);
        
        $userSecure = self::findDoc(['user_id' => $user_id]);
        $currentTime = time();

        $isOldToken = ($userSecure->updated_at > ($currentTime - 5)) && $userSecure->getAccessToken();

        if ($isOldToken) {
            return $userSecure;
        }

        $accessToken = self::generateAccessToken();

        $userSecure->user_id = $user_id;
        $userSecure->access_token = $accessToken;

        $userSecure->updated_at = time();

        $userSecure->save();

        return $userSecure;
    }

    public static function generateAccessToken ($length = null) 
    {
        if (empty($length)) {
            $length = self::ACCESS_TOKEN_LENGTH;
        }

        $length = intval($length);

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $accessToken = '';
        for ($i = 0; $i < $length; $i++) {
            $accessToken .= $characters[rand(0, $charactersLength - 1)];
        }

        return $accessToken;
    }
    public function unsetAccessTokenByUserId ($user_id)
    {
        $user_id = intval($user_id);
        
        $userSecures = self::findAll([]);

        foreach ($userSecures as $userSecure) {
            $userSecure->unsetAccessToken();
            return true;
        }
        return false;
    }
    
    public function unsetAccessToken()
    {
        $this->access_token = self::WATER_TOKEN;
        $this->save();
    }
    
    public static function findDoc ($params)
    {
        $userSecure = UserSecure::findOne($params);

        if (empty($userSecure)) {
            $userSecure = new UserSecure();
        }

        return $userSecure;
    }
    public static function findAll ($params, $toArray = null)
    {
        $userSecure = UserSecure::find()->where($params);

        if (!empty($toArray)) {
            $userSecure = $userSecure->asArray();
        }

        return $userSecure->all();
    }
}

 