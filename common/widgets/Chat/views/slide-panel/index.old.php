<?php
	use common\widgets\Chat\ChatAsset;
	use yii\helpers\Json;
	ChatAsset::register($this);
?>
<script id="chat-template" type="text/x-handlebars-template">
  <div class="entry">
    <div class="body">
      <?=$this->render('body')?>
    </div>
  </div>
</script>

<?php if (!Yii::$app->user->isGuest) { ?>
<script>
	var User = {
		id: <?=Yii::$app->user->id?>,
		role: {
			level: 1,
			name: 'Первыйчный'
		},
		_csrf: '<?=Yii::$app->request->getCsrfToken()?>',
		configs: {}
	};
</script>
<?php } ?>