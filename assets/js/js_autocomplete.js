var options, a;
		jQuery(function() {
			options = {
				serviceUrl : domain_name+'/member/search',
				minChars : 2,
				onSelect : function(value, data) {
					window.location= domain_name+"/member/person/"+data+"/";
				}
			};
			a = $('#search_box').autocomplete(options);
		});