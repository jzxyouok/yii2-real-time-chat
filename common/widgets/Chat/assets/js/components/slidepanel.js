var autoSizeEl = function ( el, cb) {
  var el = $(el);
  var offset = el.offset();
  var docHeight = document.documentElement.clientHeight;
  
  var elHeight = docHeight - ($('[role="navigation"]').height() + 66);
  
  el.css("height", elHeight);

  var styleTag = $('<style type="text/css">.tab-pane { height: ' + elHeight + 'px!important; }</style>');
  $('html > head').append(styleTag);

  cb();
};
var isShowSlidePanel = function () {
  var showData = 'site-sidebar';

  var isShow = $(Chat.initViewData.showBtnSelector).data('toggle') === showData ? true : false;

  return isShow;
};
$(document).ready(function(){
    $(Chat.initViewData.showBtnSelector).on('click', function(event){
        var that = this;
        event.preventDefault();

        if (isShowSlidePanel()) {
          $(this).data('toggle', '');
          $.slidePanel.hide();
          return;
        }

        $(this).data('toggle', 'site-sidebar');
        var html = $(Chat.initViewData.chatTemplateSelector).html();
        $.slidePanel.show({
            content: html
        }, {
            direction: 'right',
            classes: {
              show: 'slidePanel-show site-sidebar'
            },
            closeSelector: '.slidePanel-close',
            mouseDragHandler: '.slidePanel-handler',
            loading: {
              template: function(options) {
                return '<div class="' + options.classes.loading + '">' +
                  '<div class="loader loader-default"></div>' +
                  '</div>';
              },
              showCallback: function(options) {
                var that = this;
                        
                that.$el.addClass(options.classes.loading + '-show');
              },
              hideCallback: function(options) {
                this.$el.removeClass(options.classes.loading + '-show');
              }
            },
            beforeHide: function (event) {
              $(that).data('toggle', '');
            },
            beforeShow: function () {
              autoSizeEl(this.$el, function () {});
            },
            afterShow: function () {
              Chat.initViews('#chat-slidepanel');
            }
        });
    });
});