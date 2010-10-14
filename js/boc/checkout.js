function billing_save() {
	var data = $j('#co-billing-form').serialize();
	$j.ajax({
		beforeSend: function(){ $j('#billing-please-wait').show(); },
		url: saveBilling,
		data: data,
		dataType: 'json',
		type: 'post',
		success: function(response){
			$j.ajax({
				url: subscribe,
				data: data,
				type: 'post',
				success: function(){
					var next_step = (response['goto_section']) == 'shipping_method' ? 2 : 1;
					$j('#billing-please-wait').hide();
					$j('#checkoutSteps').tabs('enable', next_step);
					$j('#checkoutSteps').tabs('select', next_step);
				}
			});
		}
	});
}

function shipping_save(saveShipping, shippingMethod) {
	var data = $j('#co-shipping-form').serialize();
	$j.ajax({
		beforeSend: function(){ $j('#shipping-please-wait').show(); },
		url: saveShipping,
		data: data,
		type: 'post',
		success: function(){
			$j.ajax({
				url: shippingMethod,
				data: data,
				dataType: 'json',
				type: 'post',
				success: function(response){
					var next_step = (response['goto_section']) == 'payment' ? 3 : 2;
					$j('#shipping-please-wait').hide();
					$j('#checkoutSteps').tabs('enable', next_step);
					$j('#checkoutSteps').tabs('select', next_step);
				}
			});
		}
	});
}

function review_save(){
	var data = $j('#co-payment-form').serialize();
	$j.ajax({
		beforeSend: function(){ $j('#review-please-wait').show(); },
		url: savePayment,
		data: data,
		dataType: 'json',
		type: 'post',
		success: function(response){
			var method = $j("input[name='payment[method]']").val();
			$j.ajax({
				url: saveMethod,
				data: 'method='+method,
				type: 'post',
				success: function(){
					var agree = $j('#checkout-agreements').serialize();
					$j.ajax({
						url: saveOrder,
						data: data+'&method='+method+'&'+agree,
						dataType: 'json',
						type: 'post',
						success: function(response){
							$j('#review-please-wait').hide();
							$j(document).ready(function() {
	  							$j(location).attr("href", response['redirect']);
							});
						}
					});
				}
			});
		}
	});
}