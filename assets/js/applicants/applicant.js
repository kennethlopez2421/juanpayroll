$(function(){
  // alert();
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var e = $('#summernote');
  if($('#summernote').length != 0){
    e.summernote();
  }

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

  function gen_appTbl(search){
    var pagIbig_tbl = $('#applicantTbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'applicants/Applicant/applicant_json',
        type: 'post',
        data: { searchValue: search },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(){
          $.LoadingOverlay('hide');
        },
        error: function(){

        }
      }
    });
  }

  gen_appTbl("");

  $(document).on('click', '#btnSearchApp', function(){
    var applicant = $('.searchArea').val();
    gen_appTbl(applicant);
  });

  $(document).on('keydown', '#applicantIdNo', function(e) {
    if (e.keyCode == 32) return false;
  });

  $(document).on('click', '#btnAddApplicant', function(){
    $('#add_applicant_modal').modal();
  });

  $(document).on('click', '.btn_del_app_modal', function(){
    // alert($(this).data('deleteid'));
    // return false;
    var deleteid = $(this).data('deleteid');
    $('.info_desc').text($(this).data('name'));
    $('#delEmployeeModal').modal();
    $('#delEmpBtn').click(function(){
      $.ajax({
        url: base_url + 'applicants/Applicant/destroy',
        type: 'post',
        data: { appId: deleteid },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(){
          $.LoadingOverlay('hide');
          $('#delEmployeeModal').modal('hide');
        },
        success: function(data){
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            gen_appTbl("");
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    });;
  })

  $(document).on('click', '#btnGenLink', function(){
    $.ajax({
      url: base_url + 'applicants/Applicant/gen_link',
      type: 'post',
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      complete: function(){
        $.LoadingOverlay('hide');
      },
      success: function(data){
        $('#genLink').val(data.href);
        $('#genLink').data('token', data.token);
        $('#gen_link_modal').modal();
      }
    });
  });

  $(document).on('click', '#btnCopyLink', function(){
    var copyText = document.getElementById('genLink');
    var thiss = $(this);

    $.ajax({
      url: base_url+'applicants/Applicant/copy_link',
      type: 'post',
      data:{
        href: $('#genLink').val(),
        token: $('#genLink').data('token')
      },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          copyText.select();
          document.execCommand("copy");
          thiss.text("Copied");
          thiss.attr("disabled", true);
        }else{
          notificationError('Error', data.message);
        }
      }
    });

    $('#gen_link_modal').on('hidden.bs.modal', function(){
      thiss.text("Copy");
      thiss.attr("disabled", false);
    });
  });

  $(document).on('click', '#approveApplicant', function(){
    var appId = $('#appId').val();

    if($('#med_certificate').length === 1){
      notificationError('Error', 'You need to upload medical certificate before hiring this applicant.');
      return;
    }

    $.ajax({
      url: base_url +'applicants/Applicant/approve',
      type: 'post',
      data: { appId },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      complete: function(){
        $.LoadingOverlay('hide');
      },
      success: function(data){
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          setTimeout(function(){
            window.location.href = base_url+'/applicants/Applicant/index/'+token;
            // window.location.href = base_url+'/applicants/Applicant/index/'+token;
          },1000)
        }else{
          // if(res.educError == 1){
          //   $('.educError').show();
          // }
          //
          // if(res.dependentsError == 1){
          //   $('.dependentError').show();
          // }
          //
          // if(res.workHistoryError == 1){
          //   $('.workError').show();
          // }

          notificationError('Error', data.message);
        }
      }
    })
  });

  $(document).on('submit', '#req_form', function(e){
    e.preventDefault();
    var upload = 0
    $('.requirement').each(function(){
      if(this.files.length === 1){
        console.log(this.files);
        var size = this.files[0].size;
        if(size != 0){
          upload++;
        }
      }
    });

    if(upload == 0){
      notificationError('Error', 'Oops! Theirs nothing to upload. Please check the file size of the things your going to upload.');
      return;
    }

    $.ajax({
      url: base_url+'applicants/Applicant/update_requirements',
      type: 'post',
      data: new FormData(this),
      processData: false,
      contentType: false,
      beforeSend: function(){
        $.LoadingOverlay('show');
        $('#btn_save_req').attr('disabled', true);
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#btn_save_req').prop('disabled', false);
        if(data.success == 1){
          notificationSuccess('Success',data.message);
          setTimeout(() => {window.location.reload(true)},1500);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
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

  $('.dateField').daterangepicker({
		showDropdowns: true,
		singleDatePicker:true,
		maxDate:new Date(),
		'locale':{
			format:'YYYY-MM-DD'
		}
	});

  // New Education
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
						// html += "<input type='text' name='empEducYear["+educationCounter+"]' class='form-control dateFieldRange em_rField' readonly>";
            html += "<div class = 'form-group row'>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' name = 'empEducYearFrom["+educationCounter+"]'>"+
												"<small class = 'form-text'>From <span class = 'asterisk'></span></small>"+
											"</div>"+
											"<div class = 'col-md-6'>"+
												"<input type = 'text' placeholder = 'yyyy-mm-dd' class = 'form-control date_input' name = 'empEducYearTo["+educationCounter+"]'>"+
												"<small class = 'form-text'>From <span class = 'asterisk'></span></small>"+
											"</div>"+
										"</div>"
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>School<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='empEducSchool["+educationCounter+"]' class='form-control em_rField'>";
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
						html += "<select name='empEducLevel["+educationCounter+"]' class='form-control em_rField'>";
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
  // New WorkHistory
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
						// html += "<input type='text' name='workYear["+workHisCounter+"]' class='form-control dateFieldRange em_rField'>";
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
						html += "<input type='text' name='workStay["+workHisCounter+"]' class='form-control em_rField'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Company Name<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='workCompany["+workHisCounter+"]' class='form-control em_rField'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Position<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='workPosition["+workHisCounter+"]' class='form-control em_rField'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Level<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='workLevel["+workHisCounter+"]' class='form-control em_rField'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Contact# (Company)</label>";
						html += "<input type='text' name='workContact["+workHisCounter+"]' class='form-control contactNumber'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-6'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Responsibility<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<textarea name='workResp["+workHisCounter+"]' class='form-control em_rField'></textarea>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";



		$('#workHisContainer').append(html);

    $('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);

    $('.contactNumber').numeric({
  		maxPreDecimalPlaces : 11,
  		maxDecimalPlaces: 0,
  		allowMinus: false
  	});

	});

	$(document).on('click', '.btn_del_work', function(){
		$(this).closest('.workHisContainer').remove();
	});
  // New Dependents
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
						html += "<label>Birthday<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' placeholder = 'yyyy-mm-dd' name='bday["+dependentsCounter+"]' class='form-control date_input'>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Relationship<span class = 'ml-2 text-danger' >*</span></label>";
            html += "<select name = 'relationship["+dependentsCounter+"]' class = 'form-control'>";
						$.each(rel, function(i, val){
							html += "<option value = '"+val["relationshipid"]+"'>"+val["description"]+"</option>";
						});
						html += "</select>";
					html += "</div>";
				html += "</div>";

				html += "<div class='col-md-4'>";
					html += "<div class='form-group text-left'>";
						html += "<label>Contact#<span class = 'ml-2 text-danger' >*</span></label>";
						html += "<input type='text' name='contactNo["+dependentsCounter+"]' class='form-control contactNumber'>";
					html += "</div>";
				html += "</div>";
		html += "</div>";
		html += "<hr>";
		html += "</div>";


		$('#dependentsContainer').append(html);

    $('.date_input').datepicker(
			{format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
		);

    $('.contactNumber').numeric({
  		maxPreDecimalPlaces : 11,
  		maxDecimalPlaces: 0,
  		allowMinus: false
  	});
	});

	$(document).on('click', '.btn_del_dependent', function(){
		$(this).closest('.dependentsContainer').remove();
	});
  // Filter
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

  // $('#addEmployeeBtn').click(function(){
  //   var token_dec = $('#tDec').val();
  //   alert(token_dec);
  // })

	$('#addEmployeeBtn').click(function() {
    var token_dec = $('#tDec').val();
    // console.log(token_dec);
    // alert(token_dec);
		var applicantIdNo = $('#applicantIdNo').val();
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
		var isActive = $('#isActive option:selected').val();

    var sss_no = $('#sss_no').val();
    var philhealth_no = $('#philhealth_no').val();
    var pagibig_no = $('#pagibig_no').val();
    var tin_no = $('#tin_no').val();

		var data = {
			applicantIdNo: applicantIdNo,
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
			isActive: isActive,
      sss_no,
      philhealth_no,
      pagibig_no,
      tin_no
		};

    $('.em_rField').each(function(){
			if($(this).val() == ""){
				$(this).css("border", "1px solid #ef4131");
			}else{
				$(this).css("border", "1px solid gainsboro");
			}
		});

		$('.em_rField').each(function(){
			if($(this).val() == ""){
				$(this).focus();
				return false;
			}
		});

		var educCounter = 0;
		var educationArray = [];

		$('.educContainer').each(function() {
			educCounter++;
			educationArray[educCounter] = [
									// $('input[name^="empEducYear['+educCounter+']"]').val(),
									$('input[name^="empEducYearFrom['+educCounter+']"]').val(),
									$('input[name^="empEducYearTo['+educCounter+']"]').val(),
									$('input[name^="empEducSchool['+educCounter+']"]').val(),
									$('input[name^="empEducCourse['+educCounter+']"]').val(),
									$('select[name^="empEducLevel['+educCounter+']"]').find(":selected").val()
									];
		});

		data.educations = educationArray;


		var workCounter = 0;
		var workArray = [];

		$('.workHisContainer').each(function() {
			workCounter++;
			workArray[workCounter] = [
										// $('input[name^="workYear['+workCounter+']"]').val(),
										$('input[name^="workYearFrom['+workCounter+']"]').val(),
										$('input[name^="workYearTo['+workCounter+']"]').val(),
										$('input[name^="workStay['+workCounter+']"]').val(),
										$('input[name^="workCompany['+workCounter+']"]').val(),
										$('input[name^="workPosition['+workCounter+']"]').val(),
										$('input[name^="workLevel['+workCounter+']"]').val(),
										$('input[name^="workContact['+workCounter+']"]').val(),
										$('textarea[name^="workResp['+workCounter+']"]').val(),
									];
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

		if(nameError == 0 && emailError == 0){
			$.ajax({
				url: base_url+'applicants/Applicant/create',
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
            $.ajax({
              url: base_url+'applicants/Applicant/update_link',
              type: 'post',
              data:{token_dec},
              beforeSend: function(){
                $.LoadingOverlay('show');
              },
              success: function(data){
                $.LoadingOverlay('hide');
                // location.reload();
                setTimeout(function(){
                  location.reload();
                },1500);
              }
            });
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

			var data = {
				id:$('#app_ref_no').val(),
				// educYear:educYear,
        educYearFrom:educYearFrom,
				educYearTo:educYearTo,
				educSchool:educSchool,
				educCourse:educCourse,
				educLevel:educLevel
			};

			$.ajax({
				url: base_url+'applicants/Applicant/addeducation',
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
				id: $('#app_ref_no').val(),
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
				url: base_url+'applicants/Applicant/addworkhistory',
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
	// var deptsIdCounter = parseInt($('.depts').last().get(0).id);
	var dependentsCounter2 = parseInt($('.depts').length);
	$('#editNewDependents').click(function(){
		dependentsCounter2 += 1;
		deptsIdCounter += 1;

		var html = '';

		html += "<div class='dependentsContainer col-md-12'>";
		html += "<div class='row'>";
			html += "<div class='col-md-12 mb-1'>";
				html += "<div class='form-group text-left'>";
					html += "<h3>Dependents "+dependentsCounter2+" <button class = 'btn_del_dependent btn btn-sm btn-danger float-right'><i class = 'fa fa-trash'></i><button id='editDependent"+deptsIdCounter+"' class='btn btn-success btn-sm float-right'><i class='fa fa-save'></i></button></h3>";
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
						html += "<input type='text' id = 'dependBday"+deptsIdCounter+"' name='bday["+dependentsCounter2+"]' class='form-control dateField'>";
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

		$('#editDependent'+deptsIdCounter).click(function(){

			var firstName = $('#dependFname'+deptsIdCounter).val();
			var middleName = $('#dependMname'+deptsIdCounter).val();
			var lastName = $('#dependLname'+deptsIdCounter).val();
			var bday = $('#dependBday'+deptsIdCounter).val();
			var relationship = $('#dependRelationship'+deptsIdCounter).val();
			var contactNo = $('#dependContact'+deptsIdCounter).val();

			var data = {
				id: $('#app_ref_no').val(),
				firstName:firstName,
				middleName:middleName,
				lastName:lastName,
				bday:bday,
				relationship:relationship,
				contactNo:contactNo
			};

			$.ajax({
				url: base_url+'applicants/Applicant/adddependents',
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

		var appId = $('#appId').val();
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

    var edit_sss_no = $('#edit_sss_no').val();
    var edit_philhealth_no = $('#edit_philhealth_no').val();
    var edit_pagibig_no = $('#edit_pagibig_no').val();
    var edit_tin_no = $('#edit_tin_no').val();
		// var editIsActive = $('#editIsActive').val();

		var data = {
			appId:appId,
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
      edit_sss_no,
      edit_philhealth_no,
      edit_pagibig_no,
      edit_tin_no
		};

		$.ajax({
			url: base_url+'applicants/Applicant/update',
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

	//get educations
	$.ajax({
			url: base_url+'applicants/Applicant/educationjson',
			type:'POST',
			data:{ app_ref_no:$('#app_ref_no').val() },
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
							// educYear:educYear,
              educYearFrom: educYearFrom,
							educYearTo: educYearTo,
							educSchool:educSchool,
							educCourse:educCourse,
							educLevel:educLevel
						};

						$.ajax({
							url: base_url+'applicants/Applicant/updateeducation',
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
							url: base_url+'applicants/Applicant/destroyeducation',
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
		url: base_url+'applicants/Applicant/workhisjson',
		type:'POST',
		data:{app_ref_no: $('#app_ref_no').val()},
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
						url: base_url+'applicants/Applicant/updateworkhistory',
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
						url: base_url+'applicants/Applicant/destroyworkhis',
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
		url: base_url+'applicants/Applicant/dependentsjson',
		type:'POST',
		data:{app_ref_no: $('#app_ref_no').val()},
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
						url: base_url+'applicants/Applicant/updatedependents',
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
						url: base_url+'applicants/Applicant/destroydependent',
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

  // requirements section
  if($('.requirement').length === 0){
    $('#btn_save_req').hide();
  }

  $(document).on('click', '.btn_view_req', function(){
    var thiss = $(this);
    var path = thiss.siblings('a').attr('href');
    var title = thiss.siblings('a').data('title');
    $('.req_image').attr('src', path);
    $('#view_req_modal .modal-title').text(title);
    $('#view_req_modal').modal();
    console.log(path);
  });

  $(document).on('click', '.btn_view_app', function(){
    var thiss = $(this);
    thiss.parents('.dropup').siblings('form').submit()
  });

  $(document).on('click', '.btn_action' , function(){
    var id = $(this).data('id');
    var action = $(this).data('action');

    if(id == "" || action == ""){
      notificationError('Error', "Unable to do any action . Please try again.");
      return;
    }

    $.ajax({
      url: base_url+'applicants/Applicant/update_applicant_status',
      type: 'post',
      data:{id, action},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success',data.message);
          setTimeout(() => { window.location.reload(true) },2000);
          // gen_appTbl("");
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '#btn_int_pass', function(){
    var app_ref_no = $('#editEmployeeIdNo').val();
    var summernote = $('#summernote').val();

    if(app_ref_no == "" || summernote == ""){
      notificationError('Error', 'Unable to change applicant status');
      return;
    }

    $.ajax({
      url: base_url+'applicants/Applicant/applicant_pass_interview',
      type: 'post',
      data:{summernote,app_ref_no},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success',data.message);
          setTimeout(() => { window.location.reload(true) },2000);
        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

  $(document).on('click', '#btn_job_offer', function(){
    $('#jo_footer').hide();
    var app_ref_no = $('#editEmployeeIdNo').val();
    var f_name = $('#editFirstName').val();
    var m_name = $('#editMiddleName').val();
    var l_name = $('#editLastName').val();
    var haddress_1 = $('#editHomeAddress1').val();
    var haddress_2 = $('#editHomeAddress2').val();
    var select_jo = $('#select_jo').val();

    $.ajax({
      url: base_url+'applicants/Applicant/get_job_offer_template',
      type: 'post',
      data:{app_ref_no,f_name,m_name,l_name,haddress_1,haddress_2, select_jo},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          var info = data.info;
          $('#jo_modal').modal('hide');
          $('#jo_footer').show();
          $('#job_offer_wrapper').html(`${data.template['template_format']}`);

          Object.keys(info).forEach((key) => {
            $(`#job_offer_wrapper .${key} b`).text(info[key]);
          });

          var canvas_count = $('.signature-pad').length;
          if(canvas_count > 0){
            $('.signature-pad').toArray().forEach((field, ind, el) => {
              var signature_pad = new SignaturePad(field, {
                onEnd: function(data){
                  var timer = new Timer(3000, function(){
                    var img = signature_pad.toDataURL();
                    field.remove();
                    $(`#job_offer_wrapper .signature-pad-img:eq(${ind})`).attr('src', img);
                    // $(`${field} .signature-pad-img`).attr('src', img);
                  })

                  $('.signature-pad').mousedown(function(){ timer.addTime(6000)});
                  $('.signature-pad').dblclick(function(){
                    var canvas = $(this)[0];
                    canvas.width = canvas.width;
                    timer.stop();
                    // timer.addTime(3000);
                  });
                  // console.log(img);
                }
              })
            })
          }

        }else{
          notificationError('Error',data.message);
        }
      }
    });
  });

  $(document).on('click', '#btn_accept_jo', function(){
    var app_ref_no = $('#editEmployeeIdNo').val();
    var job_offer = $('#job_offer_wrapper').html();

    $.ajax({
      url: base_url+'applicants/Applicant/applicant_accept_jo',
      type: 'post',
      data:{app_ref_no, job_offer},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success',data.message);
          setTimeout(() => {window.location.reload(true)},2000);
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '#btn_print_job_offer', function(){
    var modal = $('#job_offer_wrapper').html();
	  var body = document.body.innerHTML;
	  document.body.innerHTML = modal;
	  window.print();
	  document.body.innerHTML = body;
  });

  $(document).on('blur', '.input-text', function(){
    var text = $(this).val();
    // console.log(text);
    if(text != ''){
      // $(this).closest('span').find('.input-text-container').text(text);
      $(this).next().append('<span class = "font-weight-bold">'+text+'</span> ')
      $(this).remove();
    }
  });

  $(document).on('click', '#btn_job_offer_modal', function(){
    $('#jo_modal').modal();
  });

});
