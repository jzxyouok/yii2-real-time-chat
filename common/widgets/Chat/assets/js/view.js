(function () {    
    Chat.initViewData = {
        User: User,
        conversationBlockSelector: '#conversation',
        chatSelector: '#chat-slidepanel',
        showBtnSelector: '#i-chat',
        chatTemplateSelector: '#chat-template',
        chat: {
            unreadMessagesCount: 15,
            conversations: [],
            currentConversation: null
        },
        messageText: ''
    };
    
    Chat.initViews = function () {
      Chat.eventLoadSlidePanel(function () {
        if (!_.isObject(ChatClient.connection) || ChatClient.connection.readyState != ChatClient.CONNECTION_STATES.OPEN) {
          ChatClient.reconnect(function () {
            Chat.initVue(function () {
              ChatClient.log('Connect');
              Chat.initEvents();
            });
          });
        } else {
          Chat.initVue(function () {
            ChatClient.log('Connect');
            Chat.initEvents();
          });
        }
           
      });
    };

    Chat.eventLoadSlidePanel = function (cb) {
      var interval = setInterval( function () {
          if ($('.tab-pane').length) {
            cb();
            clearInterval(interval);
          }
      },100);
    };

    Chat.initVue = function (cb) {
      cb = !_.isFunction(cb) ? function () {} : cb;

      Chat.Views = new Vue({
        el: this.initViewData.chatSelector,
        data: Chat.initViewData,
        methods: Chat.vueMethods
      });

      cb();
    };
    
    Chat.initEvents = function () {
      Chat.initElementsEvents();
      Chat.initScrollable();
      Chat.show();
    };

    Chat.show = function () {
      var that = this;
      var animateSpeed = 200;
      $('#chat-slidpanel-loader').animate({
        opacity: 0
      }, animateSpeed, function () {
        $(this).hide(0);
        $(that.initViewData.chatSelector).animate({
          opacity: 1
        }, animateSpeed);
      });
    };

    Chat.vueMethods = {
      getRecepientFromParticipants: function (participantsData) {
        var isValidArray = _.isArray(participantsData) && participantsData.length ?  true : false;

        if (!isValidArray) {
          return false;
        }

        var length = participantsData.length;
        for (var i = 0; i < length; ++i) {
          var participantId = participantsData[i].id;

          if (participantId != User.id) {
            return participantsData[i];
          }
        }
        return false;
      },
      setCurrentConversation: function (conversationIndex, cb) {
        this.chat.currentConversation = this.chat.conversations[conversationIndex];
        var firstNameRecepient = this.getRecepientFromParticipants(this.chat.currentConversation.participantsData).first_name;
        this.chat.currentConversation.first_name = firstNameRecepient;
        cb();
      },
      showConversation: function (showConversationIndex) {
        var that = this;
        this.setCurrentConversation(showConversationIndex, function () {
          $(that.conversationBlockSelector).addClass('active');
        });
      },
      getConversationIndexById: function (conversationId, cb) {
        cb = _.isFunction (cb) ? cb : function () {};

        var convs= this.chat.conversations;

        for (var i = 0; i < convs.length; ++i) {
          if (conversationId === convs[i]._id) {
            cb(i);
            return i;
          }
        }
        cb(null);
        return null;
      },
      showConversationById: function (id) {
        var that = this;
        var conversationIndex = this.getConversationIndexById(id);
        this.showConversation(conversationIndex);
      },
      hideConversation: function () {
        $(this.conversationBlockSelector).removeClass('active');
      },
      addConversation: function (conversation) {
        this.chat.conversations.push(conversation);
      },
      sendMessage: function (conversationId) {
        ChatClient.Message.send(conversationId, this.messageText);
        this.messageText = '';
      },
      addMessageToConversation: function (message) {
        var conversationIndex = this.getConversationIndexById(message.conversation_id);

        this.chat.conversations[conversationIndex].messages.push(message);
      }
    };

    Chat.initScrollable = function () {
        $('#sidebar-userlist').asScrollable({
          "namespace": "scrollable",
          "direction": "vertical",
          "contentSelector": ">",
          "containerSelector": ">"
        });
    }
    
    Chat.initElementsEvents  = function () {
        $('#btn-chat-connect').on('click', function (event) {
          $('#btn-chat-disconnect').show(500);
          $('#btn-chat-connect').hide(500);
          Chat.eventLoadSlidePanel(function () {
            ChatClient.reconnect(function () {
              Chat.initVue(function () {
                ChatClient.log('Connect');
                Chat.initEvents();
              });
            }); 
          });
        });
        $('#btn-chat-disconnect').on('click', function (event) {
          $('#btn-chat-disconnect').hide(500);
          $('#btn-chat-connect').show(500);
          ChatClient.disconnect();
        });
        $('[run-chat]').on('click', function (event) {
          event.preventDefault();
          ChatClient.Conversation.create($(this).attr('run-chat'));
        });
    }    
}());