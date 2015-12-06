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
	location = location;
});
$(document).on('form-success', '[data-form="register"]', function(e, response) {
	location = location;
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

// From http://stackoverflow.com/a/27943/539097
function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
	var R = 6371; // Radius of the earth in km
	var dLat = deg2rad(lat2-lat1);  // deg2rad below
	var dLon = deg2rad(lon2-lon1);
	var a =
		Math.sin(dLat/2) * Math.sin(dLat/2) +
		Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
		Math.sin(dLon/2) * Math.sin(dLon/2)
		;
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	var d = R * c; // Distance in km
	return km2mi(d);
}

function deg2rad(deg) {
	return deg * (Math.PI/180)
}

function km2mi(km) {
	return km / 1.60934;
}

function getLocation(callback) {
	navigator.geolocation.getCurrentPosition(function(position) {
		window.user_position = position;
		callback && callback();
	});
}

window.calculate_distance_queue = [];
function calculateDistance(lat, lon, callback) {
	if (!window.user_position) {
		if (!window.calculating_user_position) {
			window.calculating_user_position = true;
			getLocation(_calculateDistances);
		}

		window.calculate_distance_queue.push({
			lat: lat,
			lon: lon,
			callback: callback
		});
	} else {
		_calculateDistance(callback);
	}

	function _calculateDistances() {
		for (var i = 0; i < window.calculate_distance_queue.length; i++) {
			var q = window.calculate_distance_queue[i];
			_calculateDistance(q.lat, q.lon, q.callback);
		}
		window.calculate_distance_queue = [];
	}

	function _calculateDistance(_lat, _lon, _callback) {
		var coords = window.user_position.coords;
		var distance = getDistanceFromLatLonInKm(_lat, _lon, coords.latitude, coords.longitude);
		_callback(distance);
	}
}

function niceRound(number) {
	var decimals = 0;
	if (number < 1) {
		decimals = 2;
	} else if (number < 10) {
		decimals = 1;
	}

	var multiplier = Math.pow(10, decimals);
	return Math.round(number * multiplier) / multiplier;
}

$(document).on('shown.bs.modal', function() {
	$(this).find('[autofocus]').focus();
});