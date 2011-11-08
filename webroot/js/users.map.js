(function($) {$.fn.checkbox = function(settings) {
	settings = $.extend({
		loading: true,
		img: 'img/ajax.loader.gif'
	}, settings);   
	$(this).click(function() {
		var box = $(this);
		var flash = box.next('.flash');
		if (settings.loading) {
			box.hide();
			flash.show();
		}
		$.ajax({
			url: user_map_url(),
			data: {
				'data[UserMap][option]': $(this).val(),
				'data[UserMap][show]': $(this).attr('checked') ? '1' : '0'
			},
			type: 'POST',
			success: function(data, statusText) {
				if (settings.loading) {
					box.show();
					flash.hide();
				}
			},
			error: function(x, statusText) {
				if (x.status == 403) {
					alert('Sua sessão expirou.\n\nPor favor, faça o login novamente.');
					window.location.href = '/';
				}
			}
		});
	});
};})(jQuery);
