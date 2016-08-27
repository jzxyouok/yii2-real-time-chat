<?php
namespace common\widgets\Chat;

use yii\web\AssetBundle;

/**
 * Class ChatAsset
 * @package jones\wschat
 */
class ChatAsset extends AssetBundle
{
    public $css = [
        'css/style.css',
        'css/bootstrap-extend.css',
        'vendor/asscrollable/asScrollable.css',
        'vendor/slidepanel/slidePanel.css',
        'vendor/switchery/switchery.css',
        'fonts/material-design/material-design.css'
    ];
    
    public $js = [
        'vendor/vuejs/vue.min.js',
        'js/helper.js',
        'js/chat.js',
        'js/view.js',
        'js/config.js',
        'js/chat-client.js',
        'js/EmitsForOpcodes.js',
        'js/opcodes.js',
        'js/core.js',
        'vendor/slidepanel/jquery-slidePanel.js',
        'js/components/config-colors.js',
        'vendor/asscrollable/jquery.asScrollable.all.js',
        'js/components/slidepanel.js',
        'vendor/switchery/switchery.min.js',
        'js/components/switchery.js',
        'js/components/asscrollable.js',
        'js/components/animsition.js',
        'js/components/matchheight.js',
        'js/components/tabs.js',
        'js/main.js',
    ];

    public $depends = [
        'jones\wschat\ChatLibAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__.'/assets/';
        //set minimized version of js scripts for non debug version
        if (!YII_DEBUG) {
            $this->js = ['js/chat.min.js'];
        }
        parent::init();
    }
}
