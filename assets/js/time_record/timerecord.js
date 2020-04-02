$(function(){
	// console.log('timerecordsummary');
	var base_url = $('body').data('base_url');
	var token = $('#token').val();
	var current_date = moment().format("YYYY-MM-DD");
	display_current_timerecord(current_date,current_date,'')
	//will set default date to
	try_utilities();
	function try_utilities(){
		$.ajax({
			url: base_url + "time_record/Timerecordsummary/try_util",
			type: "POST",
			success:function(data){

			}
		});
	}

			//if dynamic data is available, change the fixed hours variables depending on the data
			var min_break = 60; //1 hour break for every shift
			var hour_break = 1;
			var regular_shift_hours = 540; // 9hrs including lunch break
			var regular_shift_start = 510; // equivalent to 8:30. Regular start shift of JCW.
			var regular_shift_end = 1050; // equivalent to 5:30. Regular end shift of JCW.
			// var currentdate_table = $('#currentdate_table').DataTable({
			// 		processing:false,
			// 		serverSide:true,
			// 		ajax:{
			// 			url: base_url+'time_record/Timerecordsummary/display_current_timerecord',
			// 			beforeSend:function(){
			// 				$.LoadingOverlay('show');
			// 			},
			// 			complete:function(){
			// 				$.LoadingOverlay('hide');
			// 			}
			// 		},
			// 		columns:[
			// 			{data:'time_in'},
			// 			{data:'time_out'},
			// 			{data:'date_created'},
			// 			{data:'employee_idno'},
			// 			{data:'employee_name'},
			// 			{data:'manhours'},
			// 			{data:'late'},
			// 			{data:'overbreak'},
			// 			{data:'undertime'},
			// 			{data:'absent'},
			// 			{data:'totalminutes'},
			// 			{'data':function getRemarks(data,type,dataToSet){
			// 				if(data.remarks == 1){
			// 					var getRemarks = "Holiday";
			// 				}else{
			// 					var getRemarks = "N/A";
			// 				}

			// 				return getRemarks;
			// 			}},

			// 		],
			// 		columnDefs:[{
			// 			 targets: [ 0,1, 2, 3, 4, 5, 6, 7, 8, 9], orderable: false
			// 		}]
			// });
 		$('#searchButton').click(function(){
 			// return;
 			//dito mag generate TRS
 			var optionval = $("#filter_by").val();
 			// console.log($("#filter_by").val());

 			// var searchtextname = $('.search_name').val();
 			var datestart = $('.date_from_only').val();
 			var dateend = $('.date_to_only').val();

  			var datestart_id = $('.date_from_id').val();
 			var dateend_id = $('.date_to_id').val();

   			var datestart_name = $('.date_from_name').val();
 			var dateend_name = $('.date_to_name').val();
 			var searchText = $('.search_id').val();
 			var searchText2 = $('.search_id2').val();


 			// var test = "";
			switch(optionval) {
 				case "by_date_range_only":
 				//will check if date inputted is inside the range of trs_range in settings
 					$.LoadingOverlay('show');
 					var datestart_d = new Date(datestart);
 					var dateend_d = new Date(dateend);
					if(current_date == moment(datestart_d).format('YYYY-MM-DD')){
							//search on current date table
						if((datestart_d!= null) && (dateend_d != null)){
							display_current_timerecord(datestart_d,dateend_d,'');
							$.LoadingOverlay('hide');
						}else{
							$.LoadingOverlay('hide');
						}
					}else{
						//search on real trs table
						$("#current_trs_div").hide();
 						$("#trs_div").show();
						if((datestart_d!= null) && (dateend_d != null)){
							//will trigger the update_timelog
							update_timerecord(datestart_d,dateend_d,'');
							$('#save_toggle').show();
							$.LoadingOverlay('hide');
						}else{
							$('#save_toggle').hide();
							$.LoadingOverlay('hide');
						}
					}
 				break;
				case "by_empid_date_range":
					$.LoadingOverlay('show');
 					var datestart_id_d = datestart_id;
 					var dateend_id_d = dateend_id;
 					if(current_date == moment(datestart_id_d).format('YYYY-MM-DD')){
 						if((datestart_id_d!= null) && (dateend_id_d != null)){
 							display_current_timerecord(datestart_id_d,dateend_id_d,searchText);
							$.LoadingOverlay('hide');
						}else{
							$.LoadingOverlay('hide');
						}
 					}else{
 						$("#current_trs_div").hide();
 						$("#trs_div").show();
						if((datestart_id_d != null) && (dateend_id_d != null)){
							update_timerecord(datestart_id_d,dateend_id_d,searchText);
							$('#save_toggle').show();
							$.LoadingOverlay('hide');
						}else{
							$('#save_toggle').hide();
							$.LoadingOverlay('hide');
						}
					}
 				break;
				case "by_name_date_range":
 					var datestart_name_d = datestart_name;
 					var dateend_name_d = dateend_name;
 					$.LoadingOverlay('show');
 					if(current_date == moment(datestart_name_d).format('YYYY-MM-DD')){

 						if((datestart_name_d!= null) && (dateend_name_d != null)){
 							display_current_timerecord(datestart_name_d,dateend_name_d,searchText2)
							$.LoadingOverlay('hide');
						}else{
							$.LoadingOverlay('hide');
						}
 					}else{
 						$("#current_trs_div").hide();
 						$("#trs_div").show();
						if((datestart_name_d != null) && (dateend_name_d != null)){
							update_timerecord(datestart_name_d,dateend_name_d,searchText2)
							$('#save_toggle').show();
							$.LoadingOverlay('hide');
						}else{
							$('#save_toggle').hide();
							$.LoadingOverlay('hide');
						}
					}
 				break;
 				default:

 				}
 		});
		$("#save_toggle").click(function(){

		});
		var get_timelog_data = [];
		//save data
		$('#save_btn').click(function(){
			$.ajax({
				type: 'POST',
				url: base_url + 'time_record/Timerecordsummary/save_data',
				beforeSend:function(){
					$.LoadingOverlay('show');
				},
				success:function(data){
					var result = JSON.parse(data);
					if(result.success == 1){
						$.LoadingOverlay('hide');
						notificationSuccess('Success',result.message);
						$('#confirm_modal').modal('toggle');
						setTimeout(() => {location.reload()},1500);
					}else{
						$.LoadingOverlay('hide');
						notificationError('Error',result.message);
						$('#confirm_modal').modal('toggle');
					}
				}
			});
		});
 		function update_timerecord(sd,ed,search){
 			// $("#timerecrod_table").dataTable().fnDestroy();
 			var date = moment().format('YYYY-MM-DD');
 			var day = moment().day();
 			var s_d = new Date(sd);
 			var e_d = new Date(ed);
 			var start_date = moment(s_d).format('YYYY-MM-DD');
 			var end_date = moment(e_d).format('YYYY-MM-DD');

 			// console.log(day);
 			var data = {
 				date:date,
 				day:day,
 				start_date:start_date,
 				end_date:end_date,
 				search: search
 			};
		    var trs_table = $('#timerecord_table').DataTable({
		        processing: false,
		        serverSide: false,
		        destroy: true,
		        order: [[2, 'asc']],
				ajax:{
					url: base_url+'time_record/Timerecordsummary/Get_timerecord',
					data:data,
					beforeSend:function(){
						$.LoadingOverlay('show');
					},
					complete:function(data){
						// var form_data  = trs_table.rows().data();
					 //        $.each( form_data, function( key, value ) {
					 //        	trs_objects.push(value);
					 //        });
						$.LoadingOverlay('hide');
					}
				},
				columns:[
					{'data':function getRemarks(data,type,dataToSet){
						if(data.time_in == 0){
							$time_in = '--:--';
						}else{
							$time_in = data.time_in;
						}
						return $time_in;
					}},
					{'data':function getRemarks(data,type,dataToSet){
						if(data.time_out == 0){
							$time_out = '--:--';
						}else{
							$time_out = data.time_out;
						}
						return $time_out;
					}},
					{data:'date_created'},
					{data:'employee_idno'},
					{data:'employee_name'},
					{data:'manhours'},
					{data:'late'},
					{data:'overbreak'},
					{data:'undertime'},
					{data:'absent'},
					{data:'totalminutes'},
					{'data':function getRemarks(data,type,dataToSet){
						if(data.remarks == 1){
							var getRemarks = "Holiday";
						}else if(data.remarks == 2){
							var getRemarks = "With Work Order";
						}
						else if(data.remarks == 3){
							var getRemarks = "Day-off Work Order"
						}
						else if(data.remarks == 4){
							var getRemarks = "Leave";
						}
						else{
							var getRemarks = "N/A";
						}
						return getRemarks;
					}},

				],
				columnDefs:[{
					 targets: [ 0,1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], orderable: false
				},
				]
		    });
 		};
  		function display_current_timerecord(sd,ed,search){
 			// $("#timerecrod_table").dataTable().fnDestroy();
 			var date = moment().format('YYYY-MM-DD');
 			var day = moment().day();
 			var s_d = new Date(sd);
 			var e_d = new Date(ed);
 			var start_date = moment(s_d).format('YYYY-MM-DD');
 			var end_date = moment(e_d).format('YYYY-MM-DD');

 			// console.log(day);
 			var data = {
 				date:date,
 				day:day,
 				start_date:start_date,
 				end_date:end_date,
 				search: search
 			};
		    var current_table = $('#currentdate_table').DataTable({
		        processing: false,
		        serverSide: false,
		        destroy: true,
		        order: [[2, 'asc']],
				ajax:{
					url: base_url+'time_record/Timerecordsummary/display_current_timerecord',
					data:data,
					beforeSend:function(){
						$.LoadingOverlay('show');
					},
					complete:function(data){
						// var form_data  = trs_table.rows().data();
					 //        $.each( form_data, function( key, value ) {
					 //        	trs_objects.push(value);
					 //        });
						$.LoadingOverlay('hide');
					}
				},
				columns:[
					{'data':function getRemarks(data,type,dataToSet){
						if(data.time_in == 0){
							$time_in = '--:--';
						}else{
							$time_in = data.time_in;
						}
						return $time_in;
					}},
					{'data':function getRemarks(data,type,dataToSet){
						if(data.time_out == 0){
							$time_out = '--:--';
						}else{
							$time_out = data.time_out;
						}
						return $time_out;
					}},
					{data:'date_created'},
					{data:'employee_idno'},
					{data:'employee_name'},
					{data:'manhours'},
					{data:'late'},
					{data:'overbreak'},
					{data:'undertime'},
					{data:'absent'},
					{data:'totalminutes'},
					{'data':function getRemarks(data,type,dataToSet){
						if(data.remarks == 1){
							var getRemarks = "Holiday";
						}else if(data.remarks == 2){
							var getRemarks = "With Work Order";
						}
						else if(data.remarks == 3){
							var getRemarks = "Day-off Work Order"
						}
						else if(data.remarks == 4){
							var getRemarks = "Leave";
						}
						else{
							var getRemarks = "N/A";
						}
						return getRemarks;
					}},

				],
				columnDefs:[{
					 targets: [ 0,1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], orderable: false
				},
				]
		    });
 		};
  	// 	function update_timerecord_new(sd,ed){
 		// 	var date = moment().format('YYYY-MM-DD');
 		// 	var day = moment().day();
 		// 	var s_d = new Date(sd);
 		// 	var e_d = new Date(ed);
 		// 	var start_date = moment(s_d).format('YYYY-MM-DD');
 		// 	var end_date = moment(e_d).format('YYYY-MM-DD');

 		// 	// console.log(day);
 		// 	var data = {
 		// 		date:date,
 		// 		day:day,
 		// 		start_date:start_date,
 		// 		end_date:end_date
 		// 	};
		 //    var trs_table = $('#timerecord_table').DataTable({
		 //        processing: false,
		 //        serverSide: false,
		 //        order: [[2, 'asc']],
			// 	ajax:{
			// 		url: base_url+'time_record/Timerecordsummary/Get_timerecord',
			// 		data:data,
			// 		beforeSend:function(){
			// 			$.LoadingOverlay('show');
			// 		},
			// 		complete:function(data){
			// 			// var form_data  = trs_table.rows().data();
			// 		 //        $.each( form_data, function( key, value ) {
			// 		 //        	trs_objects.push(value);
			// 		 //        });
			// 			$.LoadingOverlay('hide');
			// 		}
			// 	},
			// 	columns:[
			// 		{'data':function getRemarks(data,type,dataToSet){
			// 			if(data.time_in == 0){
			// 				$time_in = '--:--';
			// 			}else{
			// 				$time_in = data.time_in;
			// 			}
			// 			return $time_in;
			// 		}},
			// 		{'data':function getRemarks(data,type,dataToSet){
			// 			if(data.time_out == 0){
			// 				$time_out = '--:--';
			// 			}else{
			// 				$time_out = data.time_out;
			// 			}
			// 			return $time_out;
			// 		}},
			// 		{data:'date_created'},
			// 		{data:'employee_idno'},
			// 		{data:'employee_name'},
			// 		{data:'manhours'},
			// 		{data:'late'},
			// 		{data:'overbreak'},
			// 		{data:'undertime'},
			// 		{data:'absent'},
			// 		{data:'totalminutes'},
			// 		{'data':function getRemarks(data,type,dataToSet){
			// 			if(data.remarks == 1){
			// 				var getRemarks = "Holiday";
			// 			}else{
			// 				var getRemarks = "N/A";
			// 			}

			// 			return getRemarks;
			// 		}},

			// 	],
			// 	columnDefs:[{
			// 		 targets: [ 0,1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], orderable: false
			// 	},
			// 	]
		 //    });
 		// };
 		function add_absent(sd,ed){
  			var s_d = new Date(sd);
 			var e_d = new Date(ed);
 			var start_date = moment(s_d).format('YYYY-MM-DD');
 			var end_date = moment(e_d).format('YYYY-MM-DD');
 			var data = {
 				start_date:start_date,
 				end_date:end_date
 			};
 			$.ajax({
 				type: 'POST',
 				url: base_url + 'time_record/Timerecordsummary/input_absent',
 				data:data,
 				beforeSend: function(){
 					$.LoadingOverlay('show');
 				},
 				success:function(data){
 					$.LoadingOverlay('hide');
 				}
 			});
 		}
 		// function check_absent(sd,ed){
  	// 		var s_d = new Date(sd);
 		// 	var e_d = new Date(ed);
 		// 	var start_date = moment(s_d).format('YYYY-MM-DD');
 		// 	var end_date = moment(e_d).format('YYYY-MM-DD');

 		// 	var data = {
 		// 		start_date:start_date,
 		// 		end_date:end_date
 		// 	};

 		// 	$.ajax({
 		// 		type: "POST",
 		// 		url: base_url + 'time_record/Timerecordsummary/check_absent_record',
 		// 		data:data,
 		// 		beforeSend: function(){
 		// 			$.LoadingOverlay('show');
 		// 		},
 		// 		success:function(data){
			// 		$.LoadingOverlay('hide');
 		// 		}

 		// 	});
 		// }
 		function get_trs_currentdate(){
 			$.ajax({
 				type: 'POST',
 				url: base_url + 'time_record/Timerecordsummary/display_current_timerecord',
 				beforeSend: function(){
 					$.LoadingOverlay('show');
 				},
 				success:function(){
 					$.LoadingOverlay('hide');
 				}
 			});
 		}
 		$(document).on('change', '#filter_by', function(){
			$('.filter_div').removeClass('active');
			$('.search_id').val("");
			$('.search_id2').val("");

			//tr_table
			//current_date_table
			// currentdate_table.columns(1).search("");
			// currentdate_table.columns(2).search("");
			// currentdate_table.columns(3).search("");
			// currentdate_table.columns(4).search("");
			// currentdate_table.columns(5).search("");
			// currentdate_table.columns(6).search("");
			// currentdate_table.columns(7).search("");
			// currentdate_table.columns(8).search("");
			// currentdate_table.draw();

			switch ($(this).val()) {
				case "by_date_range_only":
					$('.filter_div').hide("slow");
					$('#div_range').show("slow");
					$('#div_range').addClass('active');
					break;
				case "by_empid_date_range":
					$('.filter_div').hide("slow");
					$('#div_id_range').show("slow");
					$('#div_id_range').addClass('active');
					break;
				case "by_name_date_range":
					$('.filter_div').hide("slow");
					$('#div_name_range').show("slow");
					$('#div_name_range').addClass('active');
					break;
				default:
			}
			// alert('changed');
		});

});
