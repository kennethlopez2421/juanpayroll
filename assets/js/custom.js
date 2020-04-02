$("#menu-toggle").click(function(e){
	e.preventDefault();
	$("#sideNav").toggleClass("sidebar-toggle");
	$("#overlay").toggleClass("overlay-toggle");
});

$("#overlay").click(function(){
	$("#sideNav").toggleClass("sidebar-toggle");
	$("#overlay").toggleClass("overlay-toggle");
});

$(function(){
	// id="pageActive" data-num="2" data-subnum="0"
	// pageNavigation
	// class="active"
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var pageNumberActive = $("#pageActive").data('num');
	var collapseActive = $("#pageActive").data('namecollapse');
	var labelname = $("#pageActive").data('labelname');

	$(".pageNavigation").find("li").each(function(){
		var activePage = $(this).data("num");
		if (pageNumberActive == activePage) {
			$(this).addClass("active");
			$(collapseActive).attr("aria-expanded","true");
			$(collapseActive).closest('li').find('.select-collapse').addClass("show");
			$(collapseActive).closest('li').find('a').each(function(){
				var subnavname = $(this).text();
				if(labelname == subnavname){
					$(this).css("background","#2b90d9");
					$(this).css("color","#fff");
					$(this).css("border-left","4px solid #1c669c");
				}
			});
		}
	});

    // Added by Rick
	$('#qr-info').focus();

	$('#qr-info').focusout(function(){
		$('#qr-info').focus();
	});

	$('#qr-info').on('change',function(){

		var data = {
			qrData: $(this).val(),
			token:$('#token').val()
		};

        $.ajax({
            url: base_url+'Main_QR/processQR',
            type:'POST',
            data:data,
            beforeSend:function(){
	            $('body').LoadingOverlay("show");
            },
            success:function(result){
            	$('body').LoadingOverlay("hide");
            	if(result == "error") {
					$('#no-result').removeClass('hide-elements');
					$('#no-result').addClass('show-elements');
					$('#qr-info').val('');
            	}else {
					window.location = ""+result+"";
				}
            }
        });
	});


	$(".select2").select2({
		width: '100%',
		dropdownAutoWidth: true
	});

	$('.multi-select').select2({
		width: '100%',
		dropdownAutoWidth: true,
		allowClear: true
	});

	$('.multi-select ul').css({
		'border': '1px solid gainsboro',
		'border-bottom': 'none'
	});

	$('.datepicker-normal').datepicker({
		todayBtn: "linked"
	});

	$('.datepicker').datepicker({
		todayBtn: "linked"
	});

	$('.datepicker-before').datepicker({
		todayBtn: "linked",
		endDate:'+0d'
	});

	$('.datepicker-yesterday').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true,
		endDate:'-1d'
	}).datepicker("setDate", new Date(yesterday()));

	$('.datepicker-today').datepicker({
		todayBtn: "linked",
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		autoclose: true,
		endDate:'+0d'
	}).datepicker("setDate", new Date());

	function yesterday(){
		let today = new Date();
		let yesterday = new Date(today);
		yesterday.setDate(today.getDate() - 1);
		let fulldate = yesterday.getFullYear()+"-"+(yesterday.getMonth() + 1)+"-"+yesterday.getDate();
		return fulldate;
	}

	$('.datepicker-after').datepicker({
		todayBtn: "linked",
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		startDate:'+0d',
		autoclose: true
	}).datepicker("setDate", new Date());

	$('.datepicker-after_empty').datepicker({
		todayBtn: "linked",
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		startDate:'+0d',
		autoclose: true
	});

	$('.input-daterange').datepicker({
		todayBtn: "linked"
	});



	$("#f2_quantity").keyup(function() {
	    var val = $("#f2_quantity").val();
	    if (parseInt(val) < 0 || isNaN(val)) {
	        $("#f2_quantity").val("");
	        $("#f2_quantity").focus();
	    }
	});

	//for current balance of player


	accounting.settings = {
		currency: {
			symbol : "",   // default currency symbol is '$'
			format: "%s%v", // controls output: %s = symbol, %v = value/number (can be object: see below)
			decimal : ".",  // decimal point separator
			thousand: ",",  // thousands separator
			precision : 2   // decimal places
		},
		number: {
			precision : 0,  // default precision on numbers is 0
			thousand: ",",
			decimal : "."
		}
	}
});

function getCurrentBalance(){
	var player_id = $('body').data('player_id');
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	if ($("body").attr('data-player_id')) {
		if (player_id != "" || player_id != null || player_id != 'undefined') {
			$.ajax({
				type:'post',
				url:base_url+'Main/getCurrentBalance',
				data:{'player_id':player_id},
				success:function(data){
					if (data.success == 1) {
						var res = data.result;
						$("#current_balance_header").text(accounting.formatMoney(res[0].current_balance));
						$("#current_balance_val").val(res[0].current_balance);
					}else{
						$("#current_balance_header").text("");
						$("#current_balance_val").val("");
					}

				}
			});
		}
	}

}
getCurrentBalance();


function validate_strong_password(password){
	var regex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)[a-zA-Z\\d]{8,}$");

	if (regex.test(password)) {
	    return true;
	}else{
		return false;
	}
}

//FIXX ISSUE IN NAV

$("#toggle-btn").click(function(e){
	if ($(this).hasClass('active')) {

		$(".pageNavigation").find('a').css('word-break', 'normal');

	}else{

		$(".pageNavigation").find('a').css('word-break', 'break-all');
	}
});

function today_now(){
	var currentdate = new Date();
	var today = ('0' + (currentdate.getMonth() + 1)).slice(-2) + '/' + ('0' + currentdate.getDate()).slice(-2) + '/' + currentdate.getFullYear();
	return today;
}

function numberWithCommas(x) {
	x = parseFloat(x).toFixed(2);
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
