loadjscssfile("https://api.waxuapp.com/api/plug-waxu/css/uikit.css","css");
loadjscssfile("https://api.waxuapp.com/api/plug-waxu/css/uikit.min.css","css");
loadjscssfile("https://api.waxuapp.com/api/plug-waxu/js/uikit.min.js","js");
loadjscssfile("https://api.waxuapp.com/api/plug-waxu/js/uikit-icons.min.js","js");

function openwaxu(billNo,amount,currency,service,waxuapikey){
	
	
	var width = screen.width;
   var height =screen.height
  
	if(width>=400){
		width=400;
	}
	if(height>=500){
		height=500;
	}
	height=height*0.98
	height_rest=height*0.02
	
	var left = height_rest/2 ;
	var top = height_rest/2 ;

	console.log(width +" "+ height +" "+ left +" "+ top);
	let waxu_modal= `
			<div id="modal-media-youtube" class="uk-flex-top" uk-modal>
				<div  class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
					<button class="uk-modal-close-outside" id="uk-close" type="button" uk-close></button>
					<iframe style="width:`+width+`px;height:`+height+`px;" id="iframe-waxu" src="#"  top="`+top+`" left="`+left+`" frameborder="0" uk-video uk-responsive2></iframe>
				</div>
			</div>`;

	var iDiv = document.createElement('div');
	iDiv.id = 'div-waxu';
	iDiv.className = 'div-waxu';
	iDiv.innerHTML = waxu_modal;
	document.getElementsByTagName('body')[0].appendChild(iDiv);

	var url='https://api.waxuapp.com/api?billNo='+billNo+'&amount='+amount+'&currency='+currency+'&service='+service+'&waxuapikey='+waxuapikey;
	//console.log(url);
	// var url='https://api.waxuapp.com/api/waxu-test/PaymentForm.html'
	// var url='https://api.waxuapp.com/api/index-test.php?billNo=559&amount=5&currency=XOF&service=2DC7F3&waxuapikey=0dfa771ce16e779caf95991f82ac4fbd05d0c14e0b0c29ef5912d1358cee573ce95bbb4ef0599f5ba7d87a3e0754c79e3a247ababa2e60143141177471d94637'
	document.getElementById("iframe-waxu").src =url;
	setTimeout(()=>{ 
		modal_media=document.getElementById("modal-media-youtube");
		UIkit.modal(modal_media).show();
	}, 500);
	
}

function openwaxuURL(urlpay){
	
	
	
	var width = screen.width;
   var height =screen.height
  
	if(width>=400){
		width=400;
	}
	if(height>=600){
		height=600;
	}
	height=height*0.98
	height_rest=height*0.02
	
	var left = height_rest/2 ;
	var top = height_rest/2 ;

	console.log(width +" "+ height +" "+ left +" "+ top);
	let waxu_modal= `
			<div id="modal-media-youtube" class="uk-flex-top" uk-modal>
				<div  class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
					<button class="uk-modal-close-outside" id="uk-close" type="button" uk-close></button>
					<iframe style="width:`+width+`px;height:`+height+`px;" id="iframe-waxu" src="#"  top="`+top+`" left="`+left+`" frameborder="0" uk-video uk-responsive2></iframe>
				</div>
			</div>`;

	var iDiv = document.createElement('div');
	iDiv.id = 'div-waxu';
	iDiv.className = 'div-waxu';
	iDiv.innerHTML = waxu_modal;
	document.getElementsByTagName('body')[0].appendChild(iDiv);

	var url = urlpay;
	//console.log(url);
	// var url='https://api.waxuapp.com/api/waxu-test/PaymentForm.html'
	// var url='https://api.waxuapp.com/api/index-test.php?billNo=559&amount=5&currency=XOF&service=2DC7F3&waxuapikey=0dfa771ce16e779caf95991f82ac4fbd05d0c14e0b0c29ef5912d1358cee573ce95bbb4ef0599f5ba7d87a3e0754c79e3a247ababa2e60143141177471d94637'
	document.getElementById("iframe-waxu").src =url;
	setTimeout(()=>{ 
		modal_media=document.getElementById("modal-media-youtube");
		UIkit.modal(modal_media).show();
	}, 500);
	
}


function loadjscssfile(filename, filetype){
    if (filetype=="js"){ //if filename is a external JavaScript file
        var fileref=document.createElement('script')
        fileref.setAttribute("type","text/javascript")
        fileref.setAttribute("src", filename)
    }
    else if (filetype=="css"){ //if filename is an external CSS file
        var fileref=document.createElement("link")
        fileref.setAttribute("rel", "stylesheet")
        fileref.setAttribute("type", "text/css")
        fileref.setAttribute("href", filename)
    }
    if (typeof fileref!="undefined")
        document.getElementsByTagName("head")[0].appendChild(fileref)
}






class Modal extends HTMLElement {
	
	set waxuapikey(value){
        this.setAttribute('waxuapikey', value);
    }
    get waxuapikey() { return this._waxuapikey};
	
	set currency(value){
        this.setAttribute('currency', value);
    }
    get currency() { return this._currency};
	
	
	set service(value){
        this.setAttribute('service', value);
    }
    get service() { return this._service};
	
	
	set amount(value){
        this.setAttribute('amount', value);
    }
    get amount() { return this._amount};
	
	set billNo(value){
        this.setAttribute('billNo', value);
    }
    get billNo() { return this._billNo};
	
	
    constructor() {
        super();
        this._modalVisible = false;
        this._modal;
        this.attachShadow({ mode: 'open' });
		
        this.shadowRoot.innerHTML = `
		<link rel="stylesheet" href="https://api.waxuapp.com/api/plug-waxu/css/uikit.css">
		<link rel="stylesheet" href="https://api.waxuapp.com/api/plug-waxu/css/uikit.min.css">
        <style>
            /* The Modal (background) */
			.waxu-modal-issue {
				display: none; /* Hidden by default */
				position: fixed; /* Stay in place */
				z-index: 999; /* Sit on top */
				left: 0;
				top: 0;
				width: 100%; /* Full width */
				height: 100%; /* Full height */
				 /*overflow: auto; /* Enable scroll if needed */
				background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
			}

			/* The Close Button */
			.close {
				color: #c1ddad;
				float: right;
				font-size: 50px;
				font-weight: bold;
			}

			.close:hover,
			.close:focus {
				color: #a8818b;
				text-decoration: none;
				cursor: pointer;
					-webkit-transition: all 200ms ease-in;
				transition: all 200ms ease-in;
			}

			/* Modal Header */
			.waxu-modal-header {
				padding: 2px 16px;
				/*background-color: #5cb85c;*/
				color: white;
			}

			/* Modal Body */
			.waxu-modal-body {
				padding: 2px 16px;
				height: 100%;
			
			}

			/* Modal Footer 
			.waxu-modal-footer {
				padding: 2px 16px;
				background-color: #5cb85c;
				color: white;
			}*/

			/* Modal Content */
			.waxu-modal-content {
				position: relative;
				background-color: transparent;
				margin: 0;
				padding: 0;
				width: 90%;
				height: 100%;
				-webkit-animation-name: animatetop;
				-webkit-animation-duration: 0.4s;
				animation-name: animatetop;
				animation-duration: 0.4s
			}

			/* Add Animation */
			@-webkit-keyframes animatetop {
				from {top: -300px; opacity: 0} 
				to {top: 0; opacity: 1}
			}

			@keyframes animatetop {
				from {top: -300px; opacity: 0}
				to {top: 0; opacity: 1}
			}
			
			
			.btn {
				font-size: 14px;
				padding: 6px 12px;
				margin-bottom: 0;

				display: inline-block;
				text-decoration: none;
				text-align: center;
				white-space: nowrap;
				vertical-align: middle;
				-ms-touch-action: manipulation;
				touch-action: manipulation;
				cursor: pointer;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
				background-image: none;
				border: 1px solid transparent;
				background-color: -internal-light-dark(rgb(239, 239, 239), rgb(59, 59, 59));
				background-color: #1a6ca7;
				color: azure;

			}
			.btn:focus,
			.btn:active:focus {
				outline: thin dotted;
				outline: 5px auto -webkit-focus-ring-color;
				outline-offset: -2px;
			}
			.btn:hover,
			.btn:focus {
				color: #333;
				text-decoration: none;
			}
			.btn:active {
				background-image: none;
				outline: 0;
				-webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
				box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
			}

        </style>
        <div class="btn">Payer avec Waxu</div>
		<a class="uk-button uk-button-default" href="#modal-media-youtube" uk-toggle>YouTube</a>
		<div id="waxu-modal-form" class="waxu-modal-issue">
        <div class="waxu-modal-content">
			<div class="waxu-modal-header">
				<span class="close">&times;</span>

			</div>
			<div class="waxu-modal-body">
				<iframe id="waxu-frame" class="airtable-embed" src="{this._url}" frameborder="0" onmousewheel="" width="100%" height="90%" style="background:#3a648600;"></iframe>
			</div><!-- .waxu-modal-body -->
		</div><!-- .waxu-modal-content -->
		</div>
		
		<div id="modal-media-vimeo" class="uk-flex-top" uk-modal>
			<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">
				<button class="uk-modal-close-outside" type="button" uk-close></button>
				<iframe src="" width="90%" height="720" frameborder="0" uk-video uk-responsive></iframe>
			</div>
		</div>
		<script src="https://api.waxuapp.com/api/plug-waxu/js/uikit.min.js"></script>
		<script src="https://api.waxuapp.com/api/plug-waxu/js/uikit-icons.min.js"></script>
        `
    }
    connectedCallback() {
		
		this._billNo = this.getAttribute('billNo');
		this._amount = this.getAttribute('amount');
		this._service = this.getAttribute('service');
		this._waxuapikey = this.getAttribute('waxuapikey');
		this._currency = this.getAttribute('currency');
		this._url = "";
		
        this._modal = this.shadowRoot.querySelector(".waxu-modal-issue");
		this.shadowRoot.querySelector(".btn").addEventListener('click', ()=>{
			
				
				if(this._billNo!='' && this._amount!='' && this._service!='' && this._currency!='' &&  this._waxuapikey!=''){
					
					
					var billNo=this._billNo;
					var amount=this._amount;
					var service=this._service;
					var currency=this._currency;
					var waxuapikey=this._waxuapikey;
					// var url='https://api.waxuapp.com/waxuwebpay?&billNo='+billNo+'&amount='+amount+'&currency='+currency+'&service='+service+'&waxuapikey='+waxuapikey;
					var url='https://api.waxuapp.com/api?billNo='+billNo+'&amount='+amount+'&currency='+currency+'&service='+service+'&waxuapikey='+waxuapikey;
					console.log(url);
					this._url=url;
					this.shadowRoot.querySelector('#waxu-frame').src = this._url;
					setTimeout(()=>{ 
						this._modalVisible = true;
						this._modal.style.display = 'block';
					}, 1000);
					
					
					

					
					 
					
					
					
				}
		}
		);
        this.shadowRoot.querySelector(".close").addEventListener('click', this._hideModal.bind(this));
    }
    disconnectedCallback() {
        this.shadowRoot.querySelector(".btn").removeEventListener('click', this._showModal);
        this.shadowRoot.querySelector(".close").removeEventListener('click', this._hideModal);
    }
    _showModal() {
        this._modalVisible = true;
        this._modal.style.display = 'block';
    }
    _hideModal() {
        this._modalVisible = false;
        this._modal.style.display = 'none';
    }
	_render(){
        this.shadowRoot.querySelector('#waxu-frame').src = this._url;
    }
	
	static get observedAttributes(){
        return ['billNo','amount', 'currency','service', 'waxuapikey'];
    }

    attributeChangedCallback(name, oldVal, newVal){
        if(name == 'billNo'){
            this._billNo = newVal;
        } else if(name == 'amount'){
            this._amount = newVal;
        } else if(name == 'currency'){
            this._currency = newVal;
        } else if(name == 'service'){
            this._service = newVal;
        } else if(name == 'waxuapikey'){
            this._waxuapikey = newVal;
        }

        this._render();

	
	}
	
	
}
customElements.define('widget-waxu',Modal);



