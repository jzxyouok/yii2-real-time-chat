<?php
	use common\widgets\Chat\ChatAsset;
	use yii\helpers\Json;
	use console\components\Chat\collections\UserSecure;
	ChatAsset::register($this);
?>
<script id="chat-template" type="text/x-handlebars-template">

  <?=$this->render('loader');?>

  <div id="chat-slidepanel" class="entry" style="opacity: 0;">
    <div class="body">      
		<div id="slidePanel-content" class="slidePanel-content site-sidebar-content">

			<ul class="site-sidebar-nav nav nav-tabs nav-justified nav-tabs-line" data-plugin="nav-tabs" role="tablist">
			  <li class="active" role="presentation">
			    <a data-toggle="tab" href="#sidebar-userlist" role="tab">
			      <i class="icon md-comment" aria-hidden="true"></i> Чаты
			    </a>
			  </li>
			  <li role="presentation">
			    <a data-toggle="tab" href="#sidebar-setting" role="tab">
			      <i class="icon md-settings" aria-hidden="true"></i> Настройки
			    </a>
			  </li>
			  <li class="">
			    <a  class="slidePanel-close" href="#">
			      <i class="icon md-close" aria-hidden="true"></i>
			    </a>
			  </li>
			<li class="nav-tabs-autoline" style="transition-duration: 0.5s, 1s; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1), cubic-bezier(0.4, 0, 0.2, 1); left: 0px; width: 101px;"></li>
			</ul>

				<div class="site-sidebar-tab-content tab-content">
					
				  <div class="tab-pane fade in active" id="sidebar-userlist" style="position: relative;">
				    <div>
				      <div>
				        <h5 class="clearfix">Загородная недвижимость {{chat.unreadMessagesCount}}
				        </h5>

				        <!-- search-form -->
				        
				        <div class="list-group">
				          <a  
				          	v-for="(index, item) in chat.conversations"
				          	class="list-group-item" 
				          	href="javascript:void(0)" 
				          	data-toggle="show-chat"
				          	v-on:click="showConversation(index)"
				          >
							  <div class="media">
							    <div class="media-left">
							      <div class="avatar avatar-sm avatar-away">
							        <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="...">
							        <i></i>
							      </div>
							    </div>
							    <div class="media-body">
							      <h4 class="media-heading">{{ getRecepientFromParticipants(item.participantsData).first_name }}</h4>
							      <small>{{ item.messages[0].text }}</small>
							    </div>
							  </div>
							</a>
				        </div>
				      </div>
				    </div>
				</div>
				<div class="tab-pane fade scrollable is-enabled scrollable-vertical" id="sidebar-setting" >
				    <div class="scrollable-container">
				      <div class="scrollable-content">
				        <h5>GENERAL SETTINGS</h5>
				        <ul class="list-group">
				          <li class="list-group-item">
				            <div class="pull-right margin-top-5">
				              <input type="checkbox" 
				              		class="js-switch-small" 
				              		data-plugin="switchery" 
				              		data-size="small" 
				              		checked="" 
				              		data-switchery="true"
				              >
				            </div>
				            <h5>Notifications</h5>
				            <p>Our very own image-less pure CSS and retina compatible check box.</p>
				            <div class="btn btn-primary" id="btn-chat-connect" style="display: none;">Подключить</div>
				            <div class="btn btn-primary" id="btn-chat-disconnect" >Отключить</div>
				          </li>
				        </ul>
				      </div>
				    </div>
				  <div class="scrollable-bar scrollable-bar-vertical scrollable-bar-hide is-disabled" draggable="false">
				  	<div class="scrollable-bar-handle"></div>
				  </div>
				 </div>
				</div>
				
				<div id="conversation" class="conversation">
				  <div class="conversation-header">
				    <a class="conversation-return pull-left" href="javascript:void(0)" v-on:click="hideConversation">
				      <i class="icon md-chevron-left" aria-hidden="true"></i>
				    </a>
				    <div class="conversation-title">
				    	{{ chat.currentConversation.first_name.substring(0, 20) }}
				      <a class="avatar margin-left-30" data-toggle="tooltip" href="#" data-placement="left" title="" :data-original-title="currentConversation.first_name">
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
				      <input class="form-control" type="text" placeholder="Напишите собеседнику" v-model="messageText" v-on:keyup.enter="sendMessage(chat.currentConversation._id);">
				      <span class="input-group-btn">
				        <a href="javascript: void(0)" class="btn btn-pure btn-default icon md-mail-send"  v-on:click="sendMessage(chat.currentConversation._id);"></a>
				      </span>
				    </div>
				  </div>
				</div>


			</div>
		</div>
    </div>
  </div>
</script>