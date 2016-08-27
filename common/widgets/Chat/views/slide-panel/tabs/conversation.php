<div id="conversation" class="conversation">
  <div class="conversation-header">
    <a class="conversation-return pull-left" href="javascript:void(0)" v-on:click="hideConversation">
      <i class="icon md-chevron-left" aria-hidden="true"></i>
    </a>
    <div class="conversation-title">
      {{ getRecepientsFromParticipants(chat.currentConversation.participantsData).first_name }}
      <a class="avatar margin-left-30" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="Robin Ahrens">
        <img src="https://randomuser.me/api/portraits/men/5.jpg" alt="...">
      </a>
    </div>
  </div>
  <div class="chats">
    <div class="" v-for="message in chat.currentConversation.messages">
      
      <div class="chat chat-right" v-if="User.id != message.creator_id">
        <div class="chat-body">
          <div class="chat-content" data-toggle="tooltip" title="" data-original-title="8:35 am">
            <p>
              {{ message.text }}
            </p>
          </div>
        </div>
      </div>
      <div class="chat" v-else>
        <div class="chat-body">
          <div class="chat-content" data-toggle="tooltip" title="" data-original-title="8:30 am">
            <p>
              {{ message.text }}
            </p>
          </div>
        </div>
      </div>

    </div>
    
  </div>
  <div class="conversation-reply">
    <div class="input-group">
      <span class="input-group-btn">
        <a href="javascript: void(0)" class="btn btn-pure btn-default icon md-plus"></a>
      </span>
      <input class="form-control" type="text" placeholder="Say something">
      <span class="input-group-btn">
        <a href="javascript: void(0)" class="btn btn-pure btn-default icon md-image"></a>
      </span>
    </div>
  </div>
</div>