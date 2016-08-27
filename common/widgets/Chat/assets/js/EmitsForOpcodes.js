(function () {
	if (_.isEmpty(window.ChatClient)) {
		return console.error('ChatClient not defined.');
	}

	/*ChatClient.send = function (data, eventName, success, error);*/
	
	ChatClient.Conversation = {
		createStatus: 0,
		create: function (participantId) {
			if (self.createStatus) {
				return ChatClient.errorLog('Conversation already creating.');
			}
			ChatClient.log('Start create Conversation');
			self.createStatus = 1;
			ChatClient.send({
				participantId: participantId
			}, 'createConversation');
		},
		sendMessage: function () {

		}
	};

	ChatClient.Message = {
		sendStatus: 0,
		send: function (conversationId, text) {
			if (self.createStatus) {
				return ChatClient.errorLog('Message already sending.');
			}
			ChatClient.log('Start create Conversation');
			self.createStatus = 1;
			ChatClient.send({
				conversationId: conversationId,
				text: text
			}, 'sendMessage');
		}
	};

}());
