<a  v-for="item in chat.conversations" :is="item.messages" class="list-group-item" href="javascript:void(0)" data-toggle="show-chat">
  <div class="media">
    <div class="media-left">
      <div class="avatar avatar-sm avatar-away">
        <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="...">
        <i></i>
      </div>
    </div>
    <div class="media-body">
      <h4 class="media-heading">{{ item.messages[0].text }}</h4>
      <small>{{ item.messages[0].text }}</small>
    </div>
  </div>
</a>
