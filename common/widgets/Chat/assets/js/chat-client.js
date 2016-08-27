(function() {
    ChatClient.connect = function (cb) {
      cb = !_.isFunction(cb) ? function () {} : cb;
      this.doConnect();      
      cb();
    };

    ChatClient.reconnect = function (cb) {
      cb = _.isFunction(cb) ? cb : function () {};
      var that = this;
      var reconnectInterval = setInterval ( function () {
        if (that.getConnectionState() === that.CONNECTION_STATES.CONNECTING) {
          return clearInterval(reconnectInterval);;
        }
        ChatClient.setConnectionState(that.CONNECTION_STATES.CONNECTING);
        that.log('---   ---   Begining connect   ---   ---');
        that.doConnect(function () {
          if (that.connectionAttempts >= that.connectionAttemptsMax) {
            clearInterval(reconnectInterval);
            return cb();
          }
          if (that.connection.readyState == that.CONNECTION_STATES.CONNECTING) {
            ++that.connectionAttempts;
            clearInterval(reconnectInterval);
            return cb();
          }
          that.log('Reconnect wait...');  
        });
      }, this.RECONNECT_INTERVAL_SPEED);
    };

    ChatClient.setConnectionState = function (state) {
      this.CONNECTION_STATE = state;
    };
    ChatClient.getConnectionState = function () {
      return this.CONNECTION_STATE;
    };

    ChatClient.doConnect = function (cb) {
      cb = _.isFunction(cb) ? cb : function () {};
      var that = this;

      if (!this.connection) {
        that.createSocketInstance(function () {
          cb();
        });
      } else {
        this.disconnect();
        this.getNewToken(function(data) {
          that.createSocketInstance(function () {
            that.log('New token: ' + data);
            cb();
          });
        });
      }
    };

    ChatClient.createSocketInstance = function (cb) {
      cb = _.isFunction(cb) ? cb : function () {};

      var that = this;

      this.initConnectionData(function () {
        that.log('Websoket Url: ' + that.url);
        
        that.connection = _.isEmpty(that.connection) ? {} : function () {
          that.connection.close();
          return {}; 
        }();

        delete that.connection;
        
        ChatClient.log('Test url: ' + that.url);

        that.connection = new WebSocket(that.url);
        
        that.defaultEvents();
        cb();  
      });
    };

    ChatClient.initConnectionData = function (cb) {
      var that = this;
      that.urlTokenGenerate = that.generateTokenProtocol + '://' + that.domain + '/chat/generate-token';
      this.getNewToken(function (newToken) {
        that.url = that.protocol + '://' + that.domain + ':' + that.port + '/websocket/?user_id=' + User.id + '&access_token=' + newToken;
        cb();
      });
    };
    
    ChatClient.getNewToken = function (cb) {
      cb = _.isFunction(cb) ? cb : function (data) {};
      var that = this;
      $.post({
        crossDomain: true,
        url: that.generateTokenProtocol + '://' + that.domain + '/chat/generate-token?user_id=' + User.id + '&old_token=' + User.accessToken, 
        data: {'_csrf': User.csrf}
      }).done(function (data) {
          that.accessToken = data;
          cb(data);
      });
    };

    ChatClient.defaultEvents = function () {
      let that = this;

      this.connection.onopen = function(e) {
        that.connectionAttempts = 0;
        that.log("Connection established, started doing authorizing on server.");
      };
      this.connection.onclose = function(data) {
        if (data.wasClean) {
          that.log('Default closed connection');
        } else {
          that.log('Dropped connection'); // например, "убит" процесс сервера
        }
        that.log('Code: ' + data.code + ' Cause: ' + data.reason);
        that.log('Oups! Connection closed.');
        that.setConnectionState(that.CONNECTION_STATES.CLOSED)
      };
      this.connection.onerror = function(data) {
        that.log(this.readyState);
      };
      that.initEvents();
    };

    ChatClient.events = function (cb) {
      var that = this;

      this.connection.onmessage = function(e) {
        var requestData = JSON.parse(e.data);
        var eventName = requestData.eventName;
        eventName = _.isString(eventName) ? that.EVENT_PREFIX + eventName.toLowerCase() : that.EVENT_PREFIX + that.DEFAULT_EVENT_NAME;
        cb(eventName, requestData);
      };
    }

    ChatClient.send = function (data, eventName, success, error) {
        let answerData = this.getAnswerFormatedData(data, eventName, success, error);
        this.connection.send(answerData);
    };

    ChatClient.getAnswerFormatedData = function (data, eventName, success, error) {
        let answerSkeleton = {
            success: _.isBoolean(success) ? success : true,
            data: data,
            eventName: _.isString(eventName) ? eventName : 'default',
            error: _.isEmpty(error) ? null : error      
        };
        let stringData = JSON.stringify(answerSkeleton);
        return stringData;
    };


    ChatClient.log = function (outline) {
        console.log(outline);
    }
    ChatClient.errorLog = function (outline) {
        console.error(outline);
    }

    ChatClient.disconnect = function () {
      if (ChatClient.connection) {
        ChatClient.connection.close();        
      }
      //delete window.ChatClient.connection;
    };
}());

