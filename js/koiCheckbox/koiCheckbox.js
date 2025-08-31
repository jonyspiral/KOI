$.fn.koiCheckbox = function() {
	return this.each(function (){
		var label1 = $('<label for="' + this.id + '"></label>');
		var label2 = $('body').find('[for="' + this.id + '"]').first();
		var div = $('<span class="koiCheckbox"></span>');
		div.insertAfter(this);
		$(this).appendTo(div);
		label1.appendTo(div);
		label2.appendTo(div);
	});
};
