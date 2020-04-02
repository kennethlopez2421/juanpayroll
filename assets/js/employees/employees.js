$(function(){

	var base_url = $("body").data('base_url');
	var token = $('#token').val();
	var educ_level = function (){
		var educ_data = null;
		$.ajax({
			url: base_url+'employees/Employee/get_educ_level',
			type: 'post',
			async: false,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success: function(data){
				$.LoadingOverlay('hide');
				educ_data = data.educ;
			}
		});

		return educ_data;
	}();

	var rel = function (){
		var rel_data = null;
		$.ajax({
			url: base_url+'employees/Employee/get_relation',
			type: 'post',
			async: false,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success: function(data){
				$.LoadingOverlay('hide');
				rel_data = data.rel;
			}
		});

		return rel_data;
	}();


	$('.dateField').daterangepicker({
		showDropdowns: true,
		singleDatePicker:true,
		maxDate:new Date(),
		'locale':{
			format:'YYYY-MM-DD'
		}
	});

	$('.editDateFieldRange').daterangepicker({
		showDropdowns: true,
		linkedCalendars: false,
		'locale':{
			format:'YYYY-MM-DD'
		}
	});

	$(document).on('keydown', '#employeeIdNo', function(e) {
    if (e.keyCode == 32) return false;
  });


	 var employeeTable = $('#employeeTable').DataTable({
						processing:false,
						serverSide:true,
						searching: false,
						ajax:{
							url: base_url+'employees/Employee/employeejson',
							beforeSend:function(){
								$.LoadingOverlay('show');
							},
							complete:function(){
								$.LoadingOverlay('hide');
							}
						},
						columnDefs:[
							{
								"targets": 0,
								"render": function(data, type, row, meta){
									// console.log(row);
									// var qr = "<center><div class='img-thumbnail'><img alt='' class='card-img-profile' src='https://api.qrserver.com/v1/create-qr-code/?data="+row.employee_idno+"&amp;size=50x50'></div></center>";
									var qr =
									`
										<center style = "cursor: pointer;">
											<img alt='' class='emp_qrcode' data-url = 'https://api.qrserver.com/v1/create-qr-code/?data=${row.employee_idno}&amp;size=50x50' src='https://api.qrserver.com/v1/create-qr-code/?data=${row.employee_idno}&amp;size=50x50'>
										</center>
									`
									return qr;
								}
							},
							{
								"targets": 1,
								"render": function(data, type, row, meta){
									return row.employee_idno;
								}
							},
							{
								"targets": 2,
								"render": function(data, type, row, meta){
									return row.name;
								}
							},
							{
								"targets": 3,
								"render": function(data, type, row, meta){
									var status = "";
									if(row.isActive == 1){
										status += '<center>';
										status += '<span class = "badge badge-pill badge-success" style= "width:50px;">Active</span>';
										status += '</center>';
									}else{
										status += '<center>';
										status += '<span class = "badge badge-pill badge-danger" style= "width:50px;">Inactive</span>';
										status += '</center>';
									}

									return status;
								}
							},
							{
							"targets":4,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#delete-btn'+data.id,function(){

									var employeeId = $(this).data('id');
									var employeeId2 = $(this).data('emp_id');
									var description = $(this).data('fname')+", "+$(this).data('lname');
									$('#emp_id').val(employeeId);
									$('#emp_id2').val(employeeId2);
									$('#name_of_emp').val(description);
									$('#end_employment_modal').modal();
									// $('.info_desc').html(description)
								});

								var buttons = "";
								buttons += "<center>";
								buttons += "<form class = 'd-inline' method='post' action='"+base_url+"employees/contracts/Contract/index/"+token+"' ><input type='hidden' name='empID' value='"+data.id+"'><button class = 'btn btn-sm btn-info d-inline mr-1' data-approveid = '"+data.id+"' style='width:90px;font-size:7px !important;'><i class = 'fa fa-clone mr-1'></i>Contract</button></form>";
								buttons += "<form class = 'd-inline' method='post' action='"+base_url+"/employees/Employee/edit/"+token+"' ><input type='hidden' name='empID' value='"+data.id+"'><button type='submit' id='edit-btn"+data.id+"' data-id='"+data.id+"' class='btn btn-primary' style='width:90px;font-size:7px !important;'><i class='fa fa-pencil'></i> Edit </button></form>";
								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-emp_id = '"+data.employee_idno+"' data-fname='"+data.first_name+"' data-lname='"+data.last_name+"' data-toggle='modal' class='btn btn-danger d-inline' style='width:90px;font-size:6px !important;'><i class='fa fa-trash'></i>End Employment</button>";
								buttons += "</center>";
								return buttons;
							}
						}]

					});

	$(document).on('click', '.emp_qrcode', function(){
		var url = $(this).data('url');
		$('.view_image').css({
      "background-image": `url(${url})`,
      "background-size": "contain",
      "background-repeat": "no-repeat",
			"background-position": 'center center'
    });
    $('#qr_modal').modal();
	});

	$(document).on('click', '#btn_end_employment', function(){
		var error = 0;
		var errorMsg = "";
		var date_of_termination = $('#date_of_termination').val();
		var emp_id = $('#emp_id').val();
		var emp_id2 = $('#emp_id2').val();
		var reason = $('#reason').val();

		// console.log(emp_id);
		// console.log(emp_id2);
		// return false;

		$('.rq3').each(function(){
		  if($(this).val() == ""){
		    $(this).css("border", "1px solid #ef4131");
		  }else{
		    $(this).css("border", "1px solid gainsboro");
		  }
		});

		$('.rq3').each(function(){
		  if($(this).val() == ""){
		    $(this).focus();
		    error = 1;
		    errorMsg = "Please fill up all required fields.";
		    return false;
		  }
		});

		if(error == 0){
		  $.ajax({
		    url: base_url+'employees/Employee/end_employment',
		    type: 'post',
		    data:{date_of_termination, emp_id, emp_id2, reason},
		    beforeSend: function(){
		      $.LoadingOverlay('show');
		    },
		    success: function(data){
		      $.LoadingOverlay('hide');
		      if(data.success == 1){
						$('#end_employment_modal').modal('hide');
						notificationSuccess('Success', data.message);
						employeeTable.ajax.reload(null,false);
		      }else{
						notificationError('Error',data.message);
		      }
		    }
		  });
		}else{
		  notificationError('Error', errorMsg);
		}
	});

	$('#btnSearchEmp').click(function(){
		var emp = $('.searchArea').val();
		$('#employeeTable').DataTable().destroy();
		var employeeTable = $('#employeeTable').DataTable({
 						processing:false,
 						serverSide:true,
 						searching: false,
 						ajax:{
 							url: base_url+'employees/Employee/employeejson',
							data: { searchValue: emp },
 							beforeSend:function(){
 								$.LoadingOverlay('show');
 							},
 							complete:function(){
 								$.LoadingOverlay('hide');
 							}
 						},
 						columnDefs:[
 							{
 								"targets": 0,
 								"render": function(data, type, row, meta){
 									// console.log(row);
 									// var qr = "<center><div class='img-thumbnail'><img alt='' class='card-img-profile' src='https://api.qrserver.com/v1/create-qr-code/?data="+row.employee_idno+"&amp;size=50x50'></div></center>";
 									var qr =
 									`
 										<center style = "cursor: pointer;">
 											<img alt='' class='emp_qrcode' data-url = 'https://api.qrserver.com/v1/create-qr-code/?data=${row.employee_idno}&amp;size=50x50' src='https://api.qrserver.com/v1/create-qr-code/?data=${row.employee_idno}&amp;size=50x50'>
 										</center>
 									`
 									return qr;
 								}
 							},
 							{
 								"targets": 1,
 								"render": function(data, type, row, meta){
 									return row.employee_idno;
 								}
 							},
 							{
 								"targets": 2,
 								"render": function(data, type, row, meta){
 									return row.name;
 								}
 							},
 							{
 								"targets": 3,
 								"render": function(data, type, row, meta){
 									var status = "";
 									if(row.isActive == 1){
 										status += '<center>';
 										status += '<span class = "badge badge-pill badge-success" style= "width:50px;">Active</span>';
 										status += '</center>';
 									}else{
 										status += '<center>';
 										status += '<span class = "badge badge-pill badge-danger" style= "width:50px;">Inactive</span>';
 										status += '</center>';
 									}

 									return status;
 								}
 							},
 							{
 							"targets":4,
 							"data":null,
 							"render":function(data, type, row, meta) {

 								$(document).on('click','#delete-btn'+data.id,function(){

 									var employeeId = $(this).data('id');
 									var employeeId2 = $(this).data('emp_id');
 									var description = $(this).data('fname')+", "+$(this).data('lname');
 									$('#emp_id').val(employeeId);
 									$('#emp_id2').val(employeeId2);
 									$('#name_of_emp').val(description);
 									$('#end_employment_modal').modal();
 									// $('.info_desc').html(description)
 								});

 								var buttons = "";
 								buttons += "<center>";
 								buttons += "<form class = 'd-inline' method='post' action='"+base_url+"employees/contracts/Contract/index/"+token+"' ><input type='hidden' name='empID' value='"+data.id+"'><button class = 'btn btn-sm btn-info d-inline mr-1' data-approveid = '"+data.id+"' style='width:90px;font-size:7px !important;'><i class = 'fa fa-clone mr-1'></i>Contract</button></form>";
 								buttons += "<form class = 'd-inline' method='post' action='"+base_url+"/employees/Employee/edit/"+token+"' ><input type='hidden' name='empID' value='"+data.id+"'><button type='submit' id='edit-btn"+data.id+"' data-id='"+data.id+"' class='btn btn-primary' style='width:90px;font-size:7px !important;'><i class='fa fa-pencil'></i> Edit </button></form>";
 								buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-emp_id = '"+data.employee_idno+"' data-fname='"+data.first_name+"' data-lname='"+data.last_name+"' data-toggle='modal' class='btn btn-danger d-inline' style='width:90px;font-size:6px !important;'><i class='fa fa-trash'></i>End Employment</button>";
 								buttons += "</center>";
 								return buttons;
 							}
 						}]

 					});

		// var employeeTable = $('#employeeTable').DataTable({
 		// 				processing:false,
 		// 				serverSide:true,
 		// 				searching: false,
 		// 				ajax:{
 		// 					url: base_url+'employees/Employee/employeejson',
 		// 					beforeSend:function(){
 		// 						$.LoadingOverlay('show');
 		// 					},
		// 					data: { searchValue: emp },
 		// 					complete:function(){
 		// 						$.LoadingOverlay('hide');
 		// 					}
 		// 				},
 		// 				columns:[
 		// 					{data:'id'},
 		// 					{data:'name'}
 		// 				],
 		// 				columnDefs:[
		// 					{
		// 						"targets": 2,
		// 						"render": function(data, type, row, meta){
		// 							var status = "";
		// 							if(row.isActive == 1){
		// 								status += '<center>';
		// 								status += '<span class = "badge badge-pill badge-success" style= "width:50px;">Active</span>';
		// 								status += '</center>';
		// 							}else{
		// 								status += '<center>';
		// 								status += '<span class = "badge badge-pill badge-danger" style= "width:50px;">Inactive</span>';
		// 								status += '</center>';
		// 							}
		//
		// 							return status;
		// 						}
		// 					},
		// 					{
 		// 					"targets":3,
 		// 					"data":null,
 		// 					"render":function(data, type, row, meta) {
		//
 		// 						$(document).on('click','#delete-btn'+data.id,function(){
		//
 		// 							var employeeId = $(this).data('id');
 		// 							var description = $(this).data('fname')+", "+$(this).data('lname');
 		// 							$('.employeeid').val(employeeId);
 		// 							$('.info_desc').html(description)
 		// 						});
		//
 		// 						var buttons = "";
 		// 						buttons += "<center>";
		// 						buttons += "<form class = 'd-inline' method='post' action='"+base_url+"employees/contracts/Contract/index/"+token+"' ><input type='hidden' name='empID' value='"+data.id+"'><button class = 'btn btn-sm btn-info d-inline mr-1' data-approveid = '"+data.id+"' style='width:90px;font-size:7px !important;'><i class = 'fa fa-clone mr-1'></i>Contract</button></form>";
 		// 						buttons += "<form class = 'd-inline' method='post' action='"+base_url+"/employees/Employee/edit/"+token+"' ><input type='hidden' name='empID' value='"+data.id+"'><button type='submit' id='edit-btn"+data.id+"' data-id='"+data.id+"' class='btn btn-primary' style='width:90px;font-size:7px !important;'><i class='fa fa-pencil'></i> Edit </button></form>";
 		// 						buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-fname='"+data.first_name+"' data-lname='"+data.last_name+"' data-toggle='modal' data-target='#delEmployeeModal' class='btn btn-danger d-inline' style='width:90px;font-size:6px !important;'><i class='fa fa-trash'></i> End Employment</button>";
 		// 						buttons += "</center>";
 		// 						return buttons;
 		// 					}
 		// 				}]
		//
 		// 			});
	});

	$('#delEmpBtn').click(function(){

		var employeeId = $('.employeeid').val();
		// console.log(employeeId);
		// return false;
		var data = {
			employeeId:employeeId
		};

		$.ajax({
			url: base_url+'employees/Employee/destroy',
			type:'POST',
			data:data,
			success:function(result){

				var data = JSON.parse(result);

				if(data.success == 1) {
					notificationSuccess('Success',data.message);
					$('#delEmployeeModal').modal('hide');
					employeeTable.ajax.reload(null,false);
				}else {
					$('#delEmployeeModal').modal('hide');
				}

			}
		});

	});

	var educationCounter = 0;

	$('#newEducation').click(function(){

		educationCounter += 1;

		var html = '';

		html += "<div class='educContainer'>";
		html += "<div class='row'>";
			html += "<div class='col-md-12 mb-1'>";
				html += "<div class='form-group text-left'>";
					html += "<h3>Education "+educationCounter+" <button class = 'btn_del_educ btn btn-small btn-danger float-right'><i class = 'fa fa-trash'></i></button></h3>";
				html += "</div>";
			html += "</div>";

				html += "<div class='col-md-6 mb-1'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Year<span class = 'ml-2 text-danger' >*</span></label>";
						// html += "<input type='text' name='empEducYear["+educationCounter+"]' class='form-control dateFieldRange' readonly>";
						html += "<div class = 'form-group row'>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' name = 'empEducYearFrom["+educationCounter+"]'>"+
												"<small class = 'form-text'>From <span class = 'asterisk'></span></small>"+
											"</div>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' name = 'empEducYearTo["+educationCounter+"]'>"+
												"<small class = 'form-text'>To <span class = 'asterisk'></span></small>"+
											"</div>"+
										"</div>"
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>School<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='empEducSchool["+educationCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Course</label>";
						html += "<input type='text' name='empEducCourse["+educationCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Level<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<select name='empEducLevel["+educationCounter+"]' class='form-control'>";
						$.each(educ_level, function(i, val){
							html += "<option value = '"+val["id"]+"'>"+val["description"]+"</option>";
						});
						html += "</select>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";


		$('#educationContainer').append(html);

		$('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);
	});

	$(document).on('click', '.btn_del_educ', function(){
		$(this).closest('.educContainer').remove();
	});

	var workHisCounter = 0;

	$('#newWorkHis').click(function(){

		workHisCounter += 1;

		var html = '';

		html += "<div class='workHisContainer'>";
		html += "<div class='row'>";
			html += "<div class='col-md-12 mb-1'>";
				html += "<div class='form-group text-left'>";
					html += "<h3>Work "+workHisCounter+" <button class = 'btn_del_work btn btn-small btn-danger float-right'><i class = 'fa fa-trash'></i></button></h3>";
				html += "</div>";
			html += "</div>";

				html += "<div class='col-md-6 mb-1'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Year<span class = 'ml-2 text-danger' >*</span></label>";
						// html += "<input type='text' name='workYear["+workHisCounter+"]' class='form-control dateFieldRange'>";
						html += "<div class = 'form-group row'>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' name = 'workYearFrom["+workHisCounter+"]'>"+
												"<small class = 'form-text'>From <span class = 'asterisk'></span></small>"+
											"</div>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' name = 'workYearTo["+workHisCounter+"]'>"+
												"<small class = 'form-text'>To <span class = 'asterisk'></span></small>"+
											"</div>"+
										"</div>"
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Stay<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='workStay["+workHisCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Company Name<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='workCompany["+workHisCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Position<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='workPosition["+workHisCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Level<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='workLevel["+workHisCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Contact# (Company)</label>";
						html += "<input type='text' name='workContact["+workHisCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Responsibility<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<textarea name='workResp["+workHisCounter+"]' class='form-control'></textarea>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";



		$('#workHisContainer').append(html);

		$('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);

	});

	$(document).on('click', '.btn_del_work', function(){
		$(this).closest('.workHisContainer').remove();
	});

	var dependentsCounter = 0;

	$('#newDependents').click(function(){

		dependentsCounter += 1;

		var html = '';

		html += "<div class='dependentsContainer'>";
		html += "<div class='row'>";
			html += "<div class='col-md-12 mb-1'>";
				html += "<div class='form-group text-left'>";
					html += "<h3>Dependents "+dependentsCounter+" <button class = 'btn_del_dependent btn btn-small btn-danger float-right'><i class = 'fa fa-trash'></i></h3>";
				html += "</div>";
			html += "</div>";

				html += "<div class='col-md-4 mb-1'>";
					html += "<div class='form-group text-left'>";
						html += "<label>First Name<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='dependFname["+dependentsCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Middle Name</label>";
						html += "<input type='text' name='dependMname["+dependentsCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Last Name<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='dependLname["+dependentsCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Birthday</label>";
						html += "<input type='text' placeholder = 'yyyy-mm-dd' name='bday["+dependentsCounter+"]' class='form-control date_input'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Relationship<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<select name='relationship["+dependentsCounter+"]' class = 'form-control'>";
						$.each(rel, function(i, val){
							html += "<option value = '"+val["relationshipid"]+"'>"+val["description"]+"</option>";
						});
						html += "</select>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Contact#<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='contactNo["+dependentsCounter+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";


		$('#dependentsContainer').append(html);

		$('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);
	});

	$(document).on('click', '.btn_del_dependent', function(){
		$(this).closest('.dependentsContainer').remove();
	});

	var nameError = 0;
	$('#firstName').blur(function() {

		$.ajax({
			url:base_url + 'employees/Employee/check_duplicate_name',
			type: 'post',
			data: {
				fname: $('#lastName').val(),
				lname: $(this).val()
			},
			success: function(data){
				if(data.duplicate == 1){
					$('.duplicateNameError').text(data.message);
					nameError = 1;
				}else{
					$('.duplicateNameError').text(data.message);
					nameError = 0;
				}
			}
		})
	});

	$('#lastName').blur(function() {

		$.ajax({
			url:base_url + 'employees/Employee/check_duplicate_name',
			type: 'post',
			data: {
				fname: $('#firstName').val(),
				lname: $(this).val()
			},
			success: function(data){
				if(data.duplicate == 1){
					$('.duplicateNameError').text(data.message);
					nameError = 1;
				}else{
					$('.duplicateNameError').text(data.message);
					nameError = 0;
				}
			}
		})
	});

	var emailError = 0;
	$('#email').blur(function(){
		$.ajax({
			url: base_url + 'employees/Employee/check_email_exist',
			type: 'post',
			data: {
				email: $(this).val()
			},
			success: function(data){
				if(data.emailExist == 1){
					$('.emailExistError').text(data.message);
					emailError = 1;
				}else{
					$('.emailExistError').text(data.message);
					emailError = 0;
				}
			}
		})
	});

	$('#addEmployeeBtn').click(function() {

		var employeeIdNo = $('#employeeIdNo').val();
		var firstName = $('#firstName').val();
		var middleName = $('#middleName').val();
		var lastName = $('#lastName').val();
		var birthday = $('#birthday').val();
		var gender = $('#gender option:selected').val();
		var maritalStatus = $('#maritalStatus option:selected').val();
		var homeAddress1 = $('#homeAddress1').val();
		var homeAddress2 = $('#homeAddress2').val();
		var contactNo = $('#contactNo').val();
		var email = $('#email').val();
		var sss_no = $('#sss_no').val();
		var philhealth_no = $('#philhealth_no').val();
		var pagibig_no = $('#pagibig_no').val();
		var tin_no = $('#tin_no').val();
		// var isActive = $('#isActive option:selected').val();
		// console.log(birthday);
		var data = {
			employeeIdNo: employeeIdNo,
			firstName: firstName,
			middleName: middleName,
			lastName: lastName,
			birthday: birthday,
			gender: gender,
			maritalStatus: maritalStatus,
			homeAddress1: homeAddress1,
			homeAddress2: homeAddress2,
			contactNo: contactNo,
			email: email,
			isActive: 0,
			sss_no,
			philhealth_no,
			pagibig_no,
			tin_no
		};

		var educCounter = 0;
		var educationArray = [];

		$('.educContainer').each(function() {
			educCounter++;
			educationArray[educCounter] = [
									$('input[name^="empEducYearFrom['+educCounter+']"]').val(),
									$('input[name^="empEducYearTo['+educCounter+']"]').val(),
									$('input[name^="empEducSchool['+educCounter+']"]').val(),
									$('input[name^="empEducCourse['+educCounter+']"]').val(),
									$('select[name^="empEducLevel['+educCounter+']"]').find(":selected").val()
									];
			// console.log($('input[name^="empEducYearFrom['+educCounter+']"]').val());
			// console.log($('input[name^="empEducYearTo['+educCounter+']"]').val())
		});

		data.educations = educationArray;;


		var workCounter = 0;
		var workArray = [];

		$('.workHisContainer').each(function() {
			workCounter++;
			workArray[workCounter] = [
										$('input[name^="workYearFrom['+workCounter+']"]').val(),
										$('input[name^="workYearTo['+workCounter+']"]').val(),
										$('input[name^="workStay['+workCounter+']"]').val(),
										$('input[name^="workCompany['+workCounter+']"]').val(),
										$('input[name^="workPosition['+workCounter+']"]').val(),
										$('input[name^="workLevel['+workCounter+']"]').val(),
										$('input[name^="workContact['+workCounter+']"]').val(),
										$('textarea[name^="workResp['+workCounter+']"]').val(),
									];

			// console.log($('input[name^="workYearFrom['+workCounter+']"]').val());
			// console.log($('input[name^="workYearTo['+workCounter+']"]').val());

		});

		data.workHistory = workArray;

		var dependentsCounter = 0;
		var dependentsArray = [];

		$('.dependentsContainer').each(function() {
			dependentsCounter++;
			dependentsArray[dependentsCounter] = [
										$('input[name^="dependFname['+dependentsCounter+']"]').val(),
										$('input[name^="dependMname['+dependentsCounter+']"]').val(),
										$('input[name^="dependLname['+dependentsCounter+']"]').val(),
										$('input[name^="bday['+dependentsCounter+']"]').val(),
										$('select[name^="relationship['+dependentsCounter+']"]').find(":selected").val(),
										$('input[name^="contactNo['+dependentsCounter+']"]').val(),
									];
		});

		data.dependents = dependentsArray;
		// console.log(data);
		// return false
		if(nameError == 0 && emailError == 0){
			$.ajax({
				url: base_url+'employees/Employee/create',
				type:'POST',
				data:data,
				beforeSend: function(){
					$.LoadingOverlay('show');
					$('.educError').hide();
					$('.dependentError').hide();
					$('.workError').hide();
				},
				success:function(res) {
					$.LoadingOverlay('hide');

					// var res = JSON.parse(result);

					if(res.success == 1) {
						notificationSuccess('Success',res.message);
					}else {
						if(res.educError == 1){
							$('.educError').show();
						}

						if(res.dependentsError == 1){
							$('.dependentError').show();
						}

						if(res.workHistoryError == 1){
							$('.workError').show();
						}

						notificationError('Error',res.message);
					}

				}
			});
		}else{
			notificationError('Error', 'Please address all existing error before submitting.');
		}

	});

	if($('.educs').length > 0){
		var educIdCounter = parseInt($('.educs').last().get(0).id);
	}else{
		var educIdCounter = 0;
	}
	// var educIdCounter = parseInt($('.educs').last().get(0).id);
	var educationCounter2 = parseInt($('.educs').length);
	$('#editnewEducation').click(function(){
		educationCounter2 += 1;
		educIdCounter += 1;

		// alert(educIdCounter);
		var html = '';

		html += "<div class='col-12 educContainer educs'>";
		html += "<div class='row'>";
			html += "<div class='col-md-12 mb-1'>";
				html += "<div class='form-group text-left'>";
					html += "<h3>Education "+educationCounter2+" <button class = 'btn_del_educ btn btn-sm btn-danger float-right'><i class = 'fa fa-trash'></i></button> <button id='editEducBtn"+educIdCounter+"' class='btn btn-success btn-sm float-right'><i class='fa fa-save mr-2'></i>Save</button></h3>";
				html += "</div>";
			html += "</div>";

				html += "<div class='col-md-6 mb-1'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Year<span class = 'ml-2 text-danger' >*</span></label>";
						// html += "<input type='text' id = 'empEducYear"+educIdCounter+"' name='empEducYear["+educationCounter2+"]' class='form-control dateFieldRange' readonly>";
						html += "<div class = 'form-group row'>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' id = 'empEducYearFrom"+educIdCounter+"'>"+
												"<small class = 'form-text'>From <span class = 'asterisk'></span></small>"+
											"</div>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' id = 'empEducYearTo"+educIdCounter+"'>"+
												"<small class = 'form-text'>To <span class = 'asterisk'></span></small>"+
											"</div>"+
										"</div>"
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>School<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'empEducSchool"+educIdCounter+"' name='empEducSchool["+educationCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Course</label>";
						html += "<input type='text' id = 'empEducCourse"+educIdCounter+"' name='empEducCourse["+educationCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Level<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<select id = 'empEducLevel"+educIdCounter+"' name='empEducLevel["+educationCounter2+"]' class='form-control'>";
						$.each(educ_level, function(i, val){
							html += "<option value = '"+val["id"]+"'>"+val["description"]+"</option>";
						});
						html += "</select>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";


		$('#educationContainer').append(html);

		$('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);

		$('#editEducBtn'+educIdCounter).click(function(){
			// alert($('#employee_idno').val());
			// var educYear = $('#empEducYear'+educIdCounter).val();
			var educYearFrom = $('#empEducYearFrom'+educIdCounter).val();
			var educYearTo = $('#empEducYearTo'+educIdCounter).val();
			var educSchool = $('#empEducSchool'+educIdCounter).val();
			var educCourse = $('#empEducCourse'+educIdCounter).val();
			var educLevel = $('#empEducLevel'+educIdCounter+' option:selected').val();

			// console.log(educYearFrom);
			// console.log(educYearTo);
			// console.log(educSchool);
			// console.log(educCourse);
			// console.log(educLevel);
			// return false;

			var data = {
				id:$('#employee_idno').val(),
				// educYear:educYear,
				educYearFrom:educYearFrom,
				educYearTo:educYearTo,
				educSchool:educSchool,
				educCourse:educCourse,
				educLevel:educLevel
			};

			$.ajax({
				url: base_url+'employees/Employee/addeducation',
				type:'POST',
				data:data,
				beforeSend: function(){
					$.LoadingOverlay('show');
				},
				success:function(data) {
					$.LoadingOverlay('hide');

					if(data.success == 1) {
						notificationSuccess('Success', data.message);
					}else {
						 notificationError('Error', data.message);
					}

				}
			});

		});


	});

	if($('.workHis').length > 0){
		var workIdCounter = parseInt($('.workHis').last().get(0).id);
	}else{
		var workIdCounter = 0;
	}
	// var workIdCounter = parseInt($('.workHis').last().get(0).id);
	var workHisCounter2 = parseInt($('.workHis').length);
	$('#editNewWorkHistory').click(function(){
		workHisCounter2 += 1;
		workIdCounter += 1;

		var html = '';

		html += "<div class='workHisContainer'>";
		html += "<div class='row'>";
			html += "<div class='col-md-12 mb-1'>";
				html += "<div class='form-group text-left'>";
					html += "<h3>Work "+workHisCounter2+" <button class = 'btn_del_work btn btn-sm btn-danger float-right'><i class = 'fa fa-trash'></i></button><button id='editWorkHis"+workIdCounter+"' class='btn btn-success btn-sm float-right'><i class='fa fa-save mr-2'></i>Save</button></h3>";
				html += "</div>";
			html += "</div>";

				html += "<div class='col-md-6 mb-1'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Year<span class = 'ml-2 text-danger' >*</span></label>";
						// html += "<input type='text' id = 'workYear"+workIdCounter+"' name='workYear["+workHisCounter2+"]' class='form-control dateFieldRange'>";
						html += "<div class = 'form-group row'>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' id = 'workYearFrom"+workIdCounter+"' name = 'workYearFrom["+workHisCounter2+"]'>"+
												"<small class = 'form-text'>From <span class = 'asterisk'></span></small>"+
											"</div>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' id = 'workYearTo"+workIdCounter+"' name = 'workYearTo["+workHisCounter2+"]'>"+
												"<small class = 'form-text'>To <span class = 'asterisk'></span></small>"+
											"</div>"+
										"</div>"
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Stay<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'workStay"+workIdCounter+"' name='workStay["+workHisCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Company Name<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'workCompany"+workIdCounter+"' name='workCompany["+workHisCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Position<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'workPosition"+workIdCounter+"' name='workPosition["+workHisCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Level<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'workLevel"+workIdCounter+"' name='workLevel["+workHisCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Contact# (Company)</label>";
						html += "<input type='text' id = 'workContact"+workIdCounter+"' name='workContact["+workHisCounter2+"]' class='contactNumber form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Responsibility<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<textarea id = 'workResp"+workIdCounter+"' name='workResp["+workHisCounter2+"]' class='form-control'></textarea>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";


		$('#workHisContainer').append(html);

		$('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);


		$('#editWorkHis'+workIdCounter).click(function(){

			// var workYear = $('#workYear'+workIdCounter).val();
			var workYearFrom = $('#workYearFrom'+workIdCounter).val();
			var workYearTo = $('#workYearTo'+workIdCounter).val();
			var workStay = $('#workStay'+workIdCounter).val();
			var workCompany = $('#workCompany'+workIdCounter).val();
			var workPosition = $('#workPosition'+workIdCounter).val();
			var workLevel = $('#workLevel'+workIdCounter).val();
			var workContact = $('#workContact'+workIdCounter).val();
			var workResp = $('#workResp'+workIdCounter).val();

			var data = {
				id: $('#employee_idno').val(),
				// workYear:workYear,
				workYearFrom,
				workYearTo,
				workStay:workStay,
				workCompany:workCompany,
				workPosition:workPosition,
				workLevel:workLevel,
				workContact:workContact,
				workResp:workResp
			};

			$.ajax({
				url: base_url+'employees/Employee/addworkhistory',
				type:'POST',
				data:data,
				beforeSend: function(){
					$.LoadingOverlay('show');
				},
				success:function(data) {
					$.LoadingOverlay('hide');

					if(data.success == 1) {
						notificationSuccess('Success',data.message);
					}else {
						notificationError('Error', data.message);
					}

				}
			});

		});

		$('.dateFieldRange').daterangepicker({
			showDropdowns: true,
			'locale':{
				format:'YYYY-MM-DD'
			}
		});

		$('.contactNumber').numeric({
			maxPreDecimalPlaces : 11,
			maxDecimalPlaces: 0,
			allowMinus: false
		});
	});

	if($('.depts').length > 0){
		var deptsIdCounter = parseInt($('.depts').last().get(0).id);
	}else{
		var deptsIdCounter = 0;
	}

	var dependentsCounter2 = parseInt($('.depts').length);
	$('#editNewDependents').click(function(){
		dependentsCounter2 += 1;
		deptsIdCounter += 1;

		var html = '';

		html += "<div class='dependentsContainer col-md-12'>";
		html += "<div class='row'>";
			html += "<div class='col-md-12 mb-1'>";
				html += "<div class='form-group text-left'>";
					html += "<h3>Dependents "+dependentsCounter2+" <button class = 'btn_del_dependent btn btn-sm btn-danger float-right'><i class = 'fa fa-trash'></i><button id='editDependent"+deptsIdCounter+"' class='btn btn-success btn-sm float-right'><i class='fa fa-save mr-2'></i>Save</button></h3>";
				html += "</div>";
			html += "</div>";

				html += "<div class='col-md-4 mb-1'>";
					html += "<div class='form-group text-left'>";
						html += "<label>First Name<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'dependFname"+deptsIdCounter+"' name='dependFname["+dependentsCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Middle Name</label>";
						html += "<input type='text' id = 'dependMname"+deptsIdCounter+"' name='dependMname["+dependentsCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Last Name<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'dependLname"+deptsIdCounter+"' name='dependLname["+dependentsCounter2+"]' class='form-control'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Birthday</label>";
						html += "<input type='text' id = 'dependBday"+deptsIdCounter+"' placeholder = 'yyyy-mm-dd' name='bday["+dependentsCounter2+"]' class='form-control date_input'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Relationship<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<select id = 'dependRelationship"+deptsIdCounter+"' name = 'relationship["+dependentsCounter2+"]' class = 'form-control'>";
						$.each(rel, function(i, val){
							html += "<option value = '"+val["relationshipid"]+"'>"+val["description"]+"</option>";
						});
						html += "</select>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Contact#<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' id = 'dependContact"+deptsIdCounter+"' name='contactNo["+dependentsCounter2+"]' class='contactNumber form-control'>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";


		$('#dependentsContainer').append(html);

		$('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);

		$('#editDependent'+deptsIdCounter).click(function(){

			var firstName = $('#dependFname'+deptsIdCounter).val();
			var middleName = $('#dependMname'+deptsIdCounter).val();
			var lastName = $('#dependLname'+deptsIdCounter).val();
			var bday = $('#dependBday'+deptsIdCounter).val();
			var relationship = $('#dependRelationship'+deptsIdCounter).val();
			var contactNo = $('#dependContact'+deptsIdCounter).val();

			var data = {
				id: $('#employee_idno').val(),
				firstName:firstName,
				middleName:middleName,
				lastName:lastName,
				bday:bday,
				relationship:relationship,
				contactNo:contactNo
			};

			$.ajax({
				url: base_url+'employees/Employee/adddependents',
				type:'POST',
				data:data,
				beforeSend: function(){
					$.LoadingOverlay('show');
				},
				success:function(data) {
					$.LoadingOverlay('hide');

					if(data.success == 1) {
						notificationSuccess('Success',data.message);
					}else {
						 notificationError('Error', data.message);
					}

				}
			});

		});


		$('.dateField').daterangepicker({
			showDropdowns: true,
			singleDatePicker:true,
			maxDate:new Date(),
			'locale':{
				format:'YYYY-MM-DD'
			}
		});

		$('.contactNumber').numeric({
			maxPreDecimalPlaces : 11,
			maxDecimalPlaces: 0,
			allowMinus: false
		});
	});

	$('#editEmployeeBtn').click(function(){

		var empID = $('#empID').val();
		var editEmployeeNo = $('#editEmployeeIdNo').val();
		var editFirstName = $('#editFirstName').val();
		var editMiddleName = $('#editMiddleName').val();
		var editLastName = $('#editLastName').val();
		var editBirthday = $('#editBirthday').val();
		var editGender = $('#editGender').val();
		var editMaritalStatus = $('#editMaritalStatus').val();
		var editHomeAddress1 = $('#editHomeAddress1').val();
		var editHomeAddress2 = $('#editHomeAddress2').val();
		var city = "null";
		var country = "Philippines";
		var editContactNo = $('#editContactNo').val();
		var editEmail = $('#editEmail').val();
		var editIsActive = $('#editIsActive').val();
		var sss_no = $('#sss_no').val();
		var philhealth_no = $('#philhealth_no').val();
		var pagibig_no = $('#pagibig_no').val();
		var tin_no = $('#tin_no').val();

		var data = {
			empID:empID,
			editEmployeeNo:editEmployeeNo,
			editFirstName:editFirstName,
			editMiddleName:editMiddleName,
			editLastName:editLastName,
			editBirthday:editBirthday,
			editGender:editGender,
			editMaritalStatus:editMaritalStatus,
			editHomeAddress1:editHomeAddress1,
			editHomeAddress2:editHomeAddress2,
			city:city,
			country:country,
			editContactNo:editContactNo,
			editEmail:editEmail,
			sss_no,
			philhealth_no,
			pagibig_no,
			tin_no
		};

		$.ajax({
			url: base_url+'employees/Employee/update',
			type:'POST',
			data:data,
			beforeSend: function(){
				$.LoadingOverlay('show');
			},
			success:function(result) {
				$.LoadingOverlay('hide');
				var res = JSON.parse(result);

				if(res.success == 1) {
					notificationSuccess('Success',res.message);
				}else {
					notificationError('Error','Error');
				}

			}
		});


	});

	$('#contactNo').numeric({
		maxPreDecimalPlaces : 11,
		maxDecimalPlaces: 0,
		allowMinus: false
	});
	$('.contactNumber').numeric({
		maxPreDecimalPlaces : 11,
		maxDecimalPlaces: 0,
		allowMinus: false
	});

	$('#editContactNo').numeric({
		maxPreDecimalPlaces : 11,
		maxDecimalPlaces: 0,
		allowMinus: false
	});

	//get educations
	$.ajax({
			url: base_url+'employees/Employee/educationjson',
			type:'POST',
			data:{ employee_idno:$('#employee_idno').val() },
			success:function(result) {

				var data = JSON.parse(result);

				$.each(data,function(index, value){

					$('#editEducBtn'+value.id).click(function(){

						// var educYear = $('#empEducYear'+value.id).val();
						var educYearFrom = $('#empEducYearFrom'+value.id).val();
						var educYearTo = $('#empEducYearTo'+value.id).val();
						var educSchool = $('#empEducSchool'+value.id).val();
						var educCourse = $('#empEducCourse'+value.id).val();
						var educLevel = $('#empEducLevel'+value.id+' option:selected').val();

						var data = {
							id:value.id,
							educYearFrom: educYearFrom,
							educYearTo: educYearTo,
							educSchool:educSchool,
							educCourse:educCourse,
							educLevel:educLevel
						};

						$.ajax({
							url: base_url+'employees/Employee/updateeducation',
							type:'POST',
							data:data,
							beforeSend: function(){
								$.LoadingOverlay('show');
							},
							success:function(result) {
								$.LoadingOverlay('hide');
								var data = JSON.parse(result);

								if(data.success == 1) {
									notificationSuccess('Success',data.message);
								}else {
									 notificationError('Error','Server Error');
								}

							}
						});

					});

					$('#delEducBtn'+value.id).click(function(){
						$.ajax({
							url: base_url+'employees/Employee/destroyeducation',
							type:'POST',
							data:{id:value.id},
							beforeSend: function(){
								$.LoadingOverlay('show');
							},
							success:function(result) {
								$.LoadingOverlay('hide');
								var data = JSON.parse(result);
								if(data.success == 1) {
									notificationSuccess('Success',data.message);
									$('.educationHandler'+value.id).remove();
								}else {
									notificationError('Error','Server Error');
								}
							}
						});
					});


				});

			}
		});

	//get work history
	$.ajax({
		url: base_url+'employees/Employee/workhisjson',
		type:'POST',
		data:{employee_idno: $('#employee_idno').val()},
		success:function(result) {
			var data = JSON.parse(result);

			$.each(data,function(index, value){


				$('#editWorkHis'+value.id).click(function(){

					// var workYear = $('#workYear'+value.id).val();
					var workYearFrom = $('#workYearFrom'+value.id).val();
					var workYearTo = $('#workYearTo'+value.id).val();
					var workStay = $('#workStay'+value.id).val();
					var workCompany = $('#workCompany'+value.id).val();
					var workPosition = $('#workPosition'+value.id).val();
					var workLevel = $('#workLevel'+value.id).val();
					var workContact = $('#workContact'+value.id).val();
					var workResp = $('#workResp'+value.id).val();

					var data = {
						id:value.id,
						// workYear:workYear,
						workYearFrom: workYearFrom,
						workYearTo: workYearTo,
						workStay:workStay,
						workCompany:workCompany,
						workPosition:workPosition,
						workLevel:workLevel,
						workContact:workContact,
						workResp:workResp
					};

					$.ajax({
						url: base_url+'employees/Employee/updateworkhistory',
						type:'POST',
						data:data,
						beforeSend: function(){
							$.LoadingOverlay('show');
						},
						success:function(result) {
							$.LoadingOverlay('hide');
							var data = JSON.parse(result);

							if(data.success == 1) {
								notificationSuccess('Success',data.message);
							}else {
								 notificationError('Error','Server Error');
							}

						}
					});

				});


				$('#delWorkHis'+value.id).click(function(){

					$.ajax({
						url: base_url+'employees/Employee/destroyworkhis',
						type:'POST',
						data:{id:value.id},
						beforeSend: function(){
							$.LoadingOverlay('show');
						},
						success:function(result) {
							$.LoadingOverlay('hide');
							var data = JSON.parse(result);
							if(data.success == 1) {
								notificationSuccess('Success',data.message);
								$('.workHisHandler'+value.id).remove();
							}else {
								notificationError('Error','Server Error');
							}
						}
					});
				});

			});
		}
	});

	//get dependents
	$.ajax({
		url: base_url+'employees/Employee/dependentsjson',
		type:'POST',
		data:{employee_idno: $('#employee_idno').val()},
		success:function(result) {
			var data = JSON.parse(result);

			$.each(data,function(index, value){

				$('#editDependent'+value.id).click(function(){

					var firstName = $('#dependFname'+value.id).val();
					var middleName = $('#dependMname'+value.id).val();
					var lastName = $('#dependLname'+value.id).val();
					var bday = $('#bday'+value.id).val();
					var relationship = $('#relationship'+value.id).val();
					var contactNo = $('#contactNo'+value.id).val();

					var data = {
						id:value.id,
						firstName:firstName,
						middleName:middleName,
						lastName:lastName,
						bday:bday,
						relationship:relationship,
						contactNo:contactNo
					};

					$.ajax({
						url: base_url+'employees/Employee/updatedependents',
						type:'POST',
						data:data,
						beforeSend: function(){
							$.LoadingOverlay('show');
						},
						success:function(result) {
							$.LoadingOverlay('hide');
							var data = JSON.parse(result);

							if(data.success == 1) {
								notificationSuccess('Success',data.message);
							}else {
								 notificationError('Error','Server Error');
							}

						}
					});

				});


				$('#delDependent'+value.id).click(function(){

					$.ajax({
						url: base_url+'employees/Employee/destroydependent',
						type:'POST',
						data:{id:value.id},
						beforeSend: function(){
							$.LoadingOverlay('show');
						},
						success:function(result) {
							$.LoadingOverlay('jode');
							var data = JSON.parse(result);
							if(data.success == 1) {
								notificationSuccess('Success',data.message);
								$('.dependentHandler'+value.id).remove();
							}else {
								notificationError('Error','Server Error');
							}
						}
					});
				});

			});
		}
	});

});
