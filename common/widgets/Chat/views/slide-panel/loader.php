<STYLE>
	.loader{
	  margin: 0 0 2em;
	  height: 100px;
	  width: 60%;
	  text-align: center;
	  padding: 1em;
	  margin: 0 auto 1em;
	  display: inline-block;
	  vertical-align: top;
    position: absolute;
    top: 50px;
    left: 20%;
	}
	svg path,
	svg rect{
	  fill: #FF6700;
	}
</STYLE>
<div id="chat-slidpanel-loader" class="loader" title="Chat loader">
  <h5>Устанавливается соединение</h5>
  <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
     width="24px" height="30px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve">
    <rect x="0" y="0" width="4" height="10" fill="#333">
      <animateTransform attributeType="xml"
        attributeName="transform" type="translate"
        values="0 0; 0 20; 0 0"
        begin="0" dur="0.6s" repeatCount="indefinite" />
    </rect>
    <rect x="10" y="0" width="4" height="10" fill="#333">
      <animateTransform attributeType="xml"
        attributeName="transform" type="translate"
        values="0 0; 0 20; 0 0"
        begin="0.2s" dur="0.6s" repeatCount="indefinite" />
    </rect>
    <rect x="20" y="0" width="4" height="10" fill="#333">
      <animateTransform attributeType="xml"
        attributeName="transform" type="translate"
        values="0 0; 0 20; 0 0"
        begin="0.4s" dur="0.6s" repeatCount="indefinite" />
    </rect>
  </svg>
</div>