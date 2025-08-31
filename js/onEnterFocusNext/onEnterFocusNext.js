$.fn.onEnterFocusNext = function() {
	return this.each(function (){
		$(this).keypress(function(e){
			if (e.which == 13 && !e.shiftKey){
				var inputs = $('body').find('a:visible, input:visible:enabled, select:visible:enabled, textarea:visible:enabled, button:visible:enabled').filter(function() {
					return !($(this).css('visibility') == 'hidden' || $(this).css('display') == 'none');
				});
				inputs.eq(inputs.index(this) + 1).focus();
				e.preventDefault();
			}
		});
	});
};