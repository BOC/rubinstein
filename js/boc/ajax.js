try {
	$('back-button').observe('click', function(event){
		var reloadurl = '/customer/address/book/';
		new Ajax.Request(reloadurl, {
			method: 'post',
			onSuccess: function(transport) {
				$('customer-address-book').innerHTML = '';
				$('customer-address-book').innerHTML = transport.responseText;
				new Ajax.Request('/customer/address/navigation', { method: 'post', evalScript: true });
			}
		});
	});
}
catch (e){}

try {
	$('add-address-button').observe('click', function(event){
    	var reloadurl = '/customer/address/new';
    	new Ajax.Request(reloadurl, {
    		method: 'post',
    		evalScripts : 'force',
    		onSuccess: function(transport) {
    			$('customer-address-book').innerHTML = '';
    			$('customer-address-book').innerHTML = transport.responseText;
    			var dataForm = new VarienForm('form-validate', true);
    			new Ajax.Request('/js/boc/ajax.js', { method: 'post' });
			}
    	});
    });
}
catch(e) {}