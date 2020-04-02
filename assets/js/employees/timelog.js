$(function(){
	var base_url = $("body").data('base_url');
	var test = mobileAndTabletcheck();
	console.log('test', test);
	function showTime(){
	    var date = new Date();
	    var h = date.getHours(); // 0 - 23
	    var m = date.getMinutes(); // 0 - 59
	    var s = date.getSeconds(); // 0 - 59
	    // var session = "AM";

	    // if(h == 0){
	    //     h = 12;
	    // }

	    // if(h > 12){
	    //     h = h - 12;
	    //     session = "PM";
	    // }

	    h = (h < 10) ? "0" + h : h;
	    m = (m < 10) ? "0" + m : m;
	    s = (s < 10) ? "0" + s : s;

	    var time = h + ":" + m;
	    document.getElementById("MyClockDisplay").innerText = time;
	    document.getElementById("MyClockDisplay").textContent = time;
	    $('#currentTime').val(time);
	    setTimeout(showTime, 1000);

	}
	showTime();
	function compute_trs(){
		alert("data computed");
	}
	//function that will trigger time record summary
 		function update_timelog(){
 			var date = moment().format('YYYY-MM-DD');
 			var day = moment().day();
 			console.log(day);
 			var data = {
 				date:date,
 				day:day
 			};
 			// alert('success');
 			//this will tally and select the last in and first out of employee
 			//this will get all the data from timelog WHERE the date is not yet in the time record summary. GROUP BY date
 			$.ajax({
 				type: 'POST',
 				url: base_url + 'time_record/Timerecordsummary/Get_timerecord',
 				data:data,
 				success:function(data){
 				}
 			});
 			//this will input the date on time record summary
 		};
	 function timelog(callback){
		var returnvalue = "";
		// console.log($("#getaddress").val());
		var getlocation = $("#getaddress").val();
		var getTime = $('#currentTime').val();
		var empId = $('.input-box').val();

		// console.log(getlocation);
		// console.log(getTime);
		// console.log(empId);
		// return false;

		if( $('#logModal').is(':visible') ) {

		    $('#logModal').modal('hide');

		}else {
			if(empId === "" || empId === null) {
				notificationError('Error',"Please input your Employee ID");
			}else {
				blob = takeSnapshot();

				var formData = new FormData();
				formData.append('picture', blob);
				formData.append('timeIn', getTime);
				formData.append('empId', empId);
				formData.append('getlocation', getlocation);

				$.ajax({
				    url: base_url+'employees/Timelog/create',
				    type: "POST",
				    cache: false,
				    contentType: false,
				    processData: false,
				    data: formData,
				    beforeSend:function(){
				    	$.LoadingOverlay('show');
				    	$('#capturebtn').attr('disabled','disabled');
				    },
				    success:function(result) {
	    				$('#capturebtn').removeAttr('disabled');
						console.log(result);
						var data = JSON.parse(result);
						// console.log(takeSnapshot());
						if(data.success == 1) {
							$('.input-box').val("");
							$('#modalTitle').html(data.mode);
							$('#modalMessage').html(data.message);
							$("#getloginstatus").val(data.loginstatus);
							var callbackparam =
								{
									loginstatus: data.loginstatus,
									time_in: data.time_in,
									time_out: data.time_out,
									employee_idno:empId,
									date:getTime
								};
							var loginstatus = data.loginstatus;
							//will check if the data is time out. then, it will update the total minutes in timelog
							if(loginstatus == 'ti'){
								callback(callbackparam);
							}
							$.LoadingOverlay('hide');
						}else {
							$('#modalTitle').html(data.mode);
							$('#modalMessage').html(data.message);
							$.LoadingOverlay('hide');
						}
						 $('#logModal').modal('toggle');
					}
				});
			}
		}
		return returnvalue;
	}

	$('.input-box').bind("enterKey",function(e){
		timelog();
	});
	$('#refreshbtn').click(function(){
		location.reload(true);
	});
	$("#capturebtn").click(function(e){
	    var confirmlocation = $("#confirmlocation").val();
	    if(confirmlocation == "location_accessible"){
	    	//check for not timing out last time user time in
	    	//if login is time_in,  this will be triggered
	    	timelog(function (status){
   				var employee_idno = status.employee_idno;
			});


	    }else{
	    	notificationError("Error", "Map is unaccessible");
	    }
	});

	$('.input-box').keyup(function(e){
	    if(e.keyCode == 13)
	    {
	        $(this).trigger("enterKey");
	    }
	});

});
