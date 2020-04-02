// get the date today
var d = new Date();
var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

function formatDate(date) {
	var d = new Date(date),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

	if (month.length < 2) month = '0' + month;
	if (day.length < 2) day = '0' + day;

	return [year, month, day].join('-');
}

toastBgColor = {
	info: "#5cb85c",
	error: "#f0ad4e"
}

// reuseable toast call function for easeness and shorter code
function toastMessage(heading, text, icon) {

	$.toast({
		heading: heading,
		text: text,
		icon: icon,
		loader: false,  
		stack: false,
		position: 'top-center', 
		allowToastClose: false,
		bgColor: toastBgColor[icon],
		textColor: 'white'  
	});
}

//allowing numeric with decimal 
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
	$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//allowing numeric without decimal 
$(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});