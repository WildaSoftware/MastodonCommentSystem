var currentLocale = 'en_US';

$(document).ready(function() {
	currentLocale = $('meta[property="og:locale"]').attr('content');
});