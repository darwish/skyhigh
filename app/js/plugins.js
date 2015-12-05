// Avoid `console` errors in browsers that lack a console.
(function() {
	var method;
	var noop = function () {};
	var methods = [
		'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
		'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
		'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
		'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
	];
	var length = methods.length;
	var console = (window.console = window.console || {});

	while (length--) {
		method = methods[length];

		// Only stub undefined methods.
		if (!console[method]) {
			console[method] = noop;
		}
	}
}());

// Place any jQuery/helper plugins in here.
$(document).on('submit', 'form[data-toggle="ajax"]', function(e) {
	e.preventDefault();

	var form = $(this)
	var modal = form.closest('.modal');

	doAjax(form.prop('action'), form.prop('method'), form.serialize(), function(response) {
		modal.modal('hide');
		form.trigger('form-success', [response]);
	});
});

function doAjax(url, method, data, success, error) {
  $.ajax(url, {
	  method: method,
	  data: data,
	  success: function(response) {
		var result = success && success(response);

		if (result !== false) {
		  if ($.type(response) === 'string') {
			$.growl({ title: "Success!", message: response });
		  } else if (response && (response.message)) {
			var title = response.title || "Success!";
			$.growl({ title: title, message: response.message });
		  }
		}
	  },
	  error: function(xhr, textStatus, errorThrown) {
		var result = error && error(xhr, textStatus, errorThrown);

		if (result !== false) {
		  var response = null;
		  try {
			  response = JSON.parse(xhr.responseText);
		  } catch (e) {
		  }

		  if (response && response.message) {
			  var message = response.message;
		  } else {
			  var message = xhr.status === 404 ? "Not Found" : (xhr.status === 403 ? "Forbidden" : xhr.responseText);
		  }

		  $.growl.error({ title: "Error!", message: message || errorThrown, duration: 10000 });
		}
	  }
  });
}

$(document).on('form-success', '[data-form="signin"]', function(e, response) {
	location = "index.php";
});
$(document).on('form-success', '[data-form="register"]', function(e, response) {
	location = "index.php";
});

function formatMoney(number) {
	var decimals = 2;
	var decimalSeparator = '.';
	var thousandsSeparator = ',';
	var signIndicator = '-';
	var dollarSign = '$';

	var signLeft = number < 0 ? (signIndicator === 'brackets' ? '(' : '-') : '',
		signRight = number < 0 && signIndicator === 'brackets' ? ')' : '',
		leftSide = signIndicator === 'brackets' ? signLeft + dollarSign : dollarSign + signLeft, // Show ($100) but -$100
		i = parseInt(number = Math.abs(+number || 0).toFixed(decimals)) + '',
		j = (j = i.length) > 3 ? j % 3 : 0;

	return leftSide + (j ? i.substr(0, j) + thousandsSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousandsSeparator) + (decimals ? decimalSeparator + Math.abs(number - i).toFixed(decimals).slice(2) : "") + signRight;
}

Handlebars.registerHelper('formatMoney', function(number) {
	return formatMoney(number);
});

function calculateDiscount(discountPrice, originalPrice) {
	return Math.round(100 * (discountPrice - originalPrice) / originalPrice);
}