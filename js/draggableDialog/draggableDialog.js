/*
 * El plugin se encarga de crear los divs adicionales.
 * Se debe crear una estructura como la siguiente:
 * <div id='OBLIGATORIO' class='draggableDialog'>
 * 		<div>
 * 			BLA BLA BLA
 * 		</div>
 * </div>
 */

$.fn.draggableDialog = function(options) {
	if (typeof options === 'undefined')
		options = {close: true};
	return this.each(function (){
		if (options.close)
			$(this).prepend('<div style="text-align: right;"><span id="closeDialog_' + $(this).attr('id') + '" class="draggableCloseButton" onclick="$(\'#' + $(this).attr('id') + '\').draggableDialogHide()">X</span></div>');
	});
};
$.fn.draggableDialogShow = function(triggerSelector) {
	if (typeof triggerSelector === 'undefined')
		triggerSelector = $('#btnBuscar');
	$.draggableDialogShow($(this).selector, triggerSelector);
};
$.fn.draggableDialogHide = function() {
	$.draggableDialogHide($(this).selector);
};
$.extend({
	draggableDialogHide: function (dialogSelector) {
		$(dialogSelector).slideUp('fast').addClass('hidden');
	},
	draggableDialogShow: function(dialogSelector, triggerSelector) {
		if ($(triggerSelector).length) {
			var botonLeft = $(triggerSelector).position().left;
			var filtroWidth = $(dialogSelector).width();
			var espaciado = 20;
			$(dialogSelector).css('left', (botonLeft - filtroWidth - espaciado) + 'px');
			var botonTop = $(triggerSelector).position().top;
			var filtroHeight = $(dialogSelector).height();
			$(dialogSelector).css('top', (botonTop - filtroHeight) + 'px');
			$(dialogSelector).slideDown('fast').removeClass('hidden').draggable();
		}
	}
});
