/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Advancednewsletter
 * @copyright  Copyright (c) 2003-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

var ANDetect = navigator.userAgent.toLowerCase();
var anOS,anBrowser,anVersion,anTotal,anThestring;

function anGetBrowserInfo() {
	if (anCheckIt('konqueror')) {
		anBrowser = "Konqueror";
		anOS = "Linux";
	}
	else if (anCheckIt('safari')) anBrowser 	= "Safari"
	else if (anCheckIt('omniweb')) anBrowser 	= "OmniWeb"
	else if (anCheckIt('opera')) anBrowser 	= "Opera"
	else if (anCheckIt('webtv')) anBrowser 	= "WebTV";
	else if (anCheckIt('icab')) anBrowser 	= "iCab"
	else if (anCheckIt('msie')) anBrowser 	= "Internet Explorer"
	else if (!anCheckIt('compatible')) {
		anBrowser = "Netscape Navigator"
		anVersion = ANDetect.charAt(8);
	}
	else anBrowser = "An unknown browser";

	if (!anVersion) anVersion = ANDetect.charAt(place + anThestring.length);

	if (!anOS) {
		if (anCheckIt('linux')) anOS 		= "Linux";
		else if (anCheckIt('x11')) anOS 	= "Unix";
		else if (anCheckIt('mac')) anOS 	= "Mac"
		else if (anCheckIt('win')) anOS 	= "Windows"
		else anOS 								= "an unknown operating system";
	}
}

function anCheckIt(string) {
	place =  ANDetect.indexOf(string) + 1;
	anThestring = string;
	return place;
}

/*-----------------------------------------------------------------------------------------------*/

Event.observe(window, 'load', anGetBrowserInfo, false);

var Advancednewsletter = Class.create();
Advancednewsletter.prototype = {
	yPos : 0,
	xPos : 0,
    isLoaded : false,

	initialize: function(ctrl, url) {
        if (url){
			this.content = url;
		} else {
			this.content = ctrl.href;
		}
        ctrl.observe('click', function(event){this.activate();Event.stop(event);}.bind(this));
        $('advancednewsletter').hide().observe('click', (function(event) {if (event.element().id == 'advancednewsletter-cancel') this.deactivate(); }).bind(this));
	},

	activate: function(){
        if (anBrowser == 'Internet Explorer'){
            this.getScroll();
			this.prepareIE('100%', 'hidden');
			this.setScroll(0,0);
			this.hideSelects('hidden');
		}
		this.displayProductupdates("block");
	},

	prepareIE: function(height, overflow){
		bod = document.getElementsByTagName('body')[0];
		bod.style.height = height;
		bod.style.overflow = overflow;

		htm = document.getElementsByTagName('html')[0];
		htm.style.height = height;
		htm.style.overflow = overflow;
	},

	hideSelects: function(visibility){
		selects = document.getElementsByTagName('select');
		for(i = 0; i < selects.length; i++) {
			selects[i].style.visibility = visibility;
		}
	},

	getScroll: function(){
		if (self.pageYOffset) {
			this.yPos = self.pageYOffset;
		} else if (document.documentElement && document.documentElement.scrollTop){
			this.yPos = document.documentElement.scrollTop;
		} else if (document.body) {
			this.yPos = document.body.scrollTop;
		}
	},

	setScroll: function(x, y){
		window.scrollTo(x, y);
	},

	displayProductupdates: function(display){
        $('advancednewsletter-overlay').style.display = display;
        $('advancednewsletter').style.display = display;
		if(display != 'none') this.loadInfo();
	},

	loadInfo: function() {
        $('advancednewsletter').className = "loading";
		var myAjax = new Ajax.Request(
            this.content,
            {method: 'post', parameters: "", onComplete: this.processInfo.bindAsEventListener(this)}
		);

	},

	processInfo: function(response){
        $('anContent').update(response.responseText);
		$('advancednewsletter').className = "done";
        this.isLoaded = true;
	},

	deactivate: function(){
		if (anBrowser == "Internet Explorer"){
			this.setScroll(0,this.yPos);
			this.prepareIE("auto", "auto");
			this.hideSelects("visible");
		}

		this.displayProductupdates("none");
	}
}

/*-----------------------------------------------------------------------------------------------*/


function addAdvancednewsletterMarkup() {
	bod 				= document.getElementsByTagName('body')[0];
	overlay 			= document.createElement('div');
	overlay.id		= 'advancednewsletter-overlay';
	an					= document.createElement('div');
	an.id				= 'advancednewsletter';
	an.className 	= 'loading';
	an.innerHTML	= '<div id="anLoadMessage">' +
						  '<p>Loading</p>' +
						  '</div>';
	bod.appendChild(overlay);
	bod.appendChild(an);
}

var AdvancednewsletterForm = Class.create();
AdvancednewsletterForm.prototype = {
    initialize: function(form){
        this.form = form;
        if ($(this.form)) {
            this.sendUrl = $(this.form).action;
            $(this.form).observe('submit', function(event){this.send();Event.stop(event);}.bind(this));
        }
        this.loadWaiting = false;
        this.validator = new Validation(this.form);
        this.onSuccess = this.success.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
        this.onFailure = this.resetLoadWaiting.bindAsEventListener(this);
        var container = $('subscribe-login-container');
        if (container && container.style.display == 'none'){
            this._disableEnableAll(container, true);
        }
    },

    send: function(){
        if(!this.validator.validate()) {
            return false;
        }
        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            this.sendUrl,
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.onSuccess,
                onFailure: this.onFailure,
                parameters: Form.serialize(this.form)
            }
        );
    },

    success: function(transport) {
        this.resetLoadWaiting();
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }
        if (response.error){
            if (response.error_type == 'no_login'){
                var container = $('subscribe-login-container');
                if (container){
                    container.show();
                    this._disableEnableAll(container, false);
                }
            }
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                alert(response.message.join("\n"));
            }
            return false;
        }
        $('anContent').update(transport.responseText);
    },

    _disableEnableAll: function(element, isDisabled) {
        var descendants = element.descendants();
        for (var k in descendants) {
            descendants[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    },

    setLoadWaiting: function(isDisabled) {
        var container = $('subscribe-button-container');
        if (isDisabled){
            container.setStyle({opacity:.5});
            this._disableEnableAll(container, true);
            Element.show('subscribe-please-wait');
            this.loadWaiting = true;
        } else {
            container.setStyle({opacity:1});
            this._disableEnableAll(container, false);
            Element.hide('subscribe-please-wait');
            this.loadWaiting = false;
        }
    },

    resetLoadWaiting: function(transport){
        this.setLoadWaiting(false);
    }
}
