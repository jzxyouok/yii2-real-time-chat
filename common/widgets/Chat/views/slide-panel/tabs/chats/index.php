
  <div class="tab-pane fade in active" id="sidebar-userlist" style="position: relative;">
    <div>
      <div>
        <h5 class="clearfix">Загородная недвижимость {{chat.unreadMessagesCount}}
        </h5>

        <!-- search-form -->
        
        <div class="list-group">
          <?=$this->render("chat-link")?>
          <component :is="test" ></component>
        </div>
      </div>
    </div>
</div>