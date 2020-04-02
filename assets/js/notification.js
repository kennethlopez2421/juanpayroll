
function notificationSuccess(header,msg) {
	$.toast({
	    heading: header,
	    text: msg,
	    icon: 'success',
	    loader: false,
	    stack: false,
	    position: 'top-center',
		allowToastClose: false,
		bgColor: 'green',
		textColor: 'white'
	});
}

function notificationError(header,msg) {
	$.toast({
	    heading: header,
	    text: msg,
	    icon: 'error',
	    loader: false,
	    stack: false,
	    position: 'top-center',
		allowToastClose: false,
		bgColor: '#f0ad4e',
		textColor: 'white'
	});
}
