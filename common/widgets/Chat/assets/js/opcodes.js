ChatClient.initEvents = function () {
  let that = this;

  ChatClient.events(function (eventName, requestData) {
    switch (eventName) {
      case 'onClose'.toLowerCase():
        that.opcodes.onClose(requestData);
        break;
      case 'onWaitConversations'.toLowerCase():
        that.opcodes.onWaitConversations(requestData);
        break;
      case 'onSuccessfulConnection'.toLowerCase():
        that.opcodes.onSuccessfulConnection(requestData);
        break;
      case 'onStatusCreateConversation'.toLowerCase():
        that.opcodes.onStatusCreateConversation(requestData);
        break;
      case 'onNewMessage'.toLowerCase():
        that.opcodes.onNewMessage(requestData);
        break;

      default: 
        that.opcodes.onDefault(requestData);
    }
  });
};

ChatClient.opcodes = {
  onWaitConversations: function (data) {
    ChatClient.log('Conversations: ');
    var isValidConversations = _.isObject(data) && _.isObject(data.data) && _.isArray(data.data.conversations);

    if (!isValidConversations) {
      return false;
    }

    Chat.Views.chat.conversations = data.data.conversations;

    ChatClient.log(data.data.conversations);

    Chat.initEvents();
  },
  showConversationById: function (id) {
    $(Chat.initViewData.showBtnSelector).click();
    Chat.Views.showConversationById(id);
  },
  onStatusCreateConversation: function (data) {
    self.createStatus = 0;
    ChatClient.log('New conversation status');
    if (_.isEmpty(data.data.status)) {
      Chat.Views.addConversation(data.data);
      this.showConversationById(data.data._id);
      ChatClient.log(data.data);
      return;
    }

    switch (data.data.status) {
      case ChatClient.RESPONSE_STATUSES.CONVERSATION_ALREADY_EXISTS:
        this.showConversationById(data.data.conversation._id);
        return ChatClient.errorLog('Conversation already exists.');
        break;
      case ChatClient.RESPONSE_STATUSES.USER_NOT_FOUND:
        return ChatClient.errorLog('Conversation not was created, because User not found.');
        break;
      default: 
        ChatClient.log(data.data.status);
    }    
  },

  onClose: function (data) {    ;
    ChatClient.disconnect();
  },
  onDefault: function (data) {
    ChatClient.log(data);
  },
  onSuccessfulConnection: function (data)
  {
    ChatClient.setConnectionState(ChatClient.CONNECTION_STATES.OPEN);
    ChatClient.log('Great, are you have successful set connection.')
  },
  onNewMessage: function (data) {
    Chat.Views.addMessageToConversation(data.data);
    ChatClient.log(data);
  }
};