$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };
  var searchStatus = {
    employment_status: 1,
    emp_status: 1,
    con_status: 1
  };

  function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function gen_employee_info_tbl(search,status){
    var employee_info_tbl = $('#employee_info_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3,4,5], orderable: false}
      ],
      "ajax":{
        url: base_url+'reports/Employee_info/get_employee_info_json',
        type: 'post',
        data: {
          searchValue: search,
          status
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 0){
            notificationError('Error', data.message);
          }
        },
        error: function(){

        }
      }
    });
  };

  gen_employee_info_tbl(JSON.stringify(searchValue),JSON.stringify(searchStatus));

  $(document).on('click', '.btn_view', function(){
    $('.nav-link').data('emp_idno', '');
    let app_ref_no = $(this).data('app_ref_no');
    let employee_idno = $(this).data("employee_idno");
    let first_name = $(this).data('first_name');
    let middle_name = $(this).data('middle_name');
    let last_name = $(this).data('last_name');
    let birthday = $(this).data('birthday');
    let gender = $(this).data('gender');
    let marital_status = $(this).data('marital_status');
    let home_address1 = $(this).data('home_address1');
    let home_address2 = $(this).data('home_address2');
    let contact_no = $(this).data('contact_no');
    let email = $(this).data('email');
    let sss_no = $(this).data('sss_no');
    let philhealth_no = $(this).data('philhealth_no');
    let pagibig_no = $(this).data('pagibig_no');
    let tin_no = $(this).data('tin_no');
    let contract_id = $(this).data('contract_id');
    let work_sched = $(this).data('work_sched');

    // console.log(birthday);

    $('#app_ref_no').val(app_ref_no);
    $('#employee_idno').val(employee_idno);
    $('#first_name').val(first_name);
    $('#middle_name').val(middle_name);
    $('#last_name').val(last_name);
    $('#birthday').val(birthday);
    $('#gender').val(gender);
    $('#marital_status').val(marital_status);
    $('#home_address1').val(home_address1);
    $('#home_address2').val(home_address2);
    $('#contact_no').val(contact_no);
    $('#email').val(email);
    $('#sss_no').val(sss_no);
    $('#philhealth_no').val(philhealth_no);
    $('#pagibig_no').val(pagibig_no);
    $('#tin_no').val(tin_no);
    $('.nav-link').data('emp_idno', employee_idno);
    $('.contract_accordion').data('cid',contract_id);
    $('.contract_worksched').data('worksched',work_sched);

    $('#employmee_info_modal').modal()
  })

  $(document).on('click', '.nav-link', function(){
    let stype = $(this).data('stype');
    let emp_idno = $(this).data('emp_idno');

    $('.educ_ajax').html('');
    $('.work_ajax').html('');
    $.ajax({
      url: base_url+'reports/Employee_info/get_emp_info',
      type: 'post',
      data:{stype, emp_idno},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          switch (stype) {
            case 'emp_educ':
              if(data.record.length > 0){
                $.each(data.record, function(i, val){
                  $('.educ_ajax').append(
                    `<div class="col-md-4 mb-2">
                      <label for="Education Level:" class="form-control-label col-form-label-sm">Education Level:</label>
                      <input type="text" value = "${val['educ_level']}" class="form-control" readonly />
                    </div>
                    <div class="col-md-4 mb-2">
                      <label for="School:" class="form-control-label col-form-label-sm">School:</label>
                      <input type="text" value = "${val['school']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4 mb-2">
                      <label for="Course:" class="form-control-label col-form-label-sm">Course:</label>
                      <input type="text" value = "${val['course']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-6 mb-2">
                      <label for="From:" class="form-control-label col-form-label-sm">From:</label>
                      <input type="text" value = "${val['year_from']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-6 mb-2">
                      <label for="To:" class="form-control-label col-form-label-sm">To:</label>
                      <input type="text" value = "${val['year_to']}" class="form-control" readonly/>
                    </div>
                    <div class="col-12">
                      <hr />
                    </div>
                    `
                  );
                });
              }else{
                $('.educ_ajax').html(
                  `<div class="col-12 text-center" style = "padding-top:13%;padding-bottom:25%;">
                    <i class="fa fa-clone d-block mb-2" style = "font-size: 80px;"></i>
                    <h5>No availabe education</h5>
                  </div>`
                );
              }
              break;
            case 'emp_work':
              if(data.record.length > 0){
                $.each(data.record, function(i, val){
                  $('.work_ajax').append(
                    `
                    <div class="col-md-4 mb-2">
                      <label for="Company Name:" class="form-control-label col-form-label-sm">Company Name:</label>
                      <input type="text" value = "${val['company_name']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4 mb-2">
                      <label for="Positon:" class="form-control-label col-form-label-sm">Positon:</label>
                      <input type="text" value = "${val['position']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4 mb-2">
                      <label for="Positon level:" class="form-control-label col-form-label-sm">Positon level:</label>
                      <input type="text" value = "${val['level']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-3 mb-2">
                      <label for="Stay:" class="form-control-label col-form-label-sm">Stay:</label>
                      <input type="text" value = "${val['stay']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-3 mb-2">
                      <label for="From:" class="form-control-label col-form-label-sm">From:</label>
                      <input type="text" value = "${val['year_from']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-3 mb-2">
                      <label for="To:" class="form-control-label col-form-label-sm">To:</label>
                      <input type="text" value = "${val['year_to']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-3 mb-2">
                      <label for="Contact No:" class="form-control-label col-form-label-sm">Contact No:</label>
                      <input type="text" value = "${val['contact_no']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-12 mb-2">
                      <label for="Responsibilities:" class="form-control-label col-form-label-sm">Responsibilities:</label>
                      <textarea class = "form-control" name="" id="" cols="30" rows="3" readonly>${val['responsibility']}</textarea>
                    </div>
                    <div class="col-12 mb-2">
                      <hr />
                    </div>
                    `
                  );
                });
              }else{
                $('.work_ajax').html(
                  `<div class="col-12 text-center" style = "padding-top:13%;padding-bottom:25%;">
                    <i class="fa fa-clone d-block mb-2" style = "font-size: 80px;"></i>
                    <h5>No availabe workhistory</h5>
                  </div>`
                )
              }
              break;
            case 'emp_depend':
              if(data.record.length > 0){
                $.each(data.record, function(i, val) {
                  $('.depend_ajax').append(
                    `
                    <div class="col-md-4">
                      <label for="First Name:" class="form-control-label col-form-label-sm">First Name:</label>
                      <input type="text" value = "${val['first_name']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4">
                      <label for="Middle Name:" class="form-control-label col-form-label-sm">Middle Name:</label>
                      <input type="text" value = "${val['middle_name']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4">
                      <label for="Last Name:" class="form-control-label col-form-label-sm">Last Name:</label>
                      <input type="text" value = "${val['last_name']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4">
                      <label for="Relationship:" class="form-control-label col-form-label-sm">Relationship:</label>
                      <input type="text" value = "${val['relation']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4">
                      <label for="Birtdate:" class="form-control-label col-form-label-sm">Birtdate:</label>
                      <input type="text" value = "${val['birthday']}" class="form-control" readonly/>
                    </div>
                    <div class="col-md-4">
                      <label for="Contact No:" class="form-control-label col-form-label-sm">Contact No:</label>
                      <input type="text" value = "${val['contact_no']}" class="form-control" readonly/>
                    </div>
                    <div class="col-12 mb-2">
                      <hr />
                    </div>
                    `
                  );
                });
              }else{
                $('.depend_ajax').html(
                  `<div class="col-12 text-center" style = "padding-top:13%;padding-bottom:25%;">
                    <i class="fa fa-clone d-block mb-2" style = "font-size: 80px;"></i>
                    <h5>No availabe Dependents</h5>
                  </div>`
                );
              }
              break;
            default:

          }
        }else{
          // notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '.contract_accordion', function(){
    let cid = $(this).data('cid');
    $('#sal_ajax').html(
      `<tr>
        <th>Total</th>
        <td id = "total_sal">PHP 0.00</td>
      </tr>`
    );
    $('#leave_ajx').html('');

    $.ajax({
      url: base_url+'reports/Employee_info/get_contract',
      type: 'post',
      data:{cid},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          if(data.record.length > 0){
            const contract = data.record[0];
            const sal_cat = JSON.parse(contract['sal_cat']);
            const leave = JSON.parse(contract['emp_leave']);
            $('#worksite').val(contract['worksite']);
            $('#position').val((contract['department']) + contract['position']);
            $('#emp_status').val(contract['emp_status']);
            $('#start_date').val(contract['contract_start']);
            $('#end_date').val(contract['contract_end']);
            $('#company').val(contract['company']);
            $('#contract_type').val(contract['contract_type'].toUpperCase());
            $('#sss').val(contract['sss']);
            $('#philhealth').val(contract['philhealth']);
            $('#pagibig').val(contract['pagibig']);
            $('#tax').val(contract['tax']);
            $('#paytype').val(contract['paytype']);
            $('#payout_medium').val(contract['payout_medium']);

            $.each(sal_cat, function(i, val){
              $('#sal_ajax').prepend(
                `
                  <tr>
                    <th>${val.desc}</th>
                    <td>${contract['currency']} ${numberWithCommas(val.amount)}</td>
                  </tr>
                `
              );
            });

            $('#total_sal').html(contract['currency'] +" "+numberWithCommas(contract['total_sal']));

            $.each(leave, function(i, val){
              $('#leave_ajax').prepend(
                `
                  <tr>
                    <th>${val.desc}</th>
                    <td>${val.days}</td>
                  </tr>
                `
              );
            })
          }else{
          }
        }else{
          notificationError('Error', data.message);
        }
      }
    });
  });

  $(document).on('click', '.contract_worksched', function(){
    let worksched = $(this).data('worksched');
    let days = Object.keys(worksched);
    $('#work_sched_ajax').html('');
    days.forEach((day) => {

      // console.log(worksched[day]);
      $('#work_sched_ajax').append(
        `
          <tr>
            <th>${day.toUpperCase()}</th>
            <td><input type="time" class = "form-control text-center" value = "${worksched[day][0]}" readonly/></td>
            <td><input type="time" class = "form-control text-center" value = "${worksched[day][1]}" readonly/></td>
            <td><input type="time" class = "form-control text-center" value = "${worksched[day][3]}" readonly/></td>
            <td><input type="time" class = "form-control text-center" value = "${worksched[day][4]}" readonly/></td>
            <td><input type="text" class = "form-control text-center" value = "${worksched[day][2]}" readonly/></td>
          </tr>
        `
      );
    })
  });

  // filter
  $(document).on('change', '#filter_by', function(){
  	$('.filter_div').removeClass('active');
  	switch ($(this).val()) {
  		case "by_name":
  			$('.filter_div').hide("slow");
  			$('#divName').show("slow");
  			$('#divName').addClass('active');
  			break;
  		case "by_id":
  			$('.filter_div').hide("slow");
  			$('#divEmpID').show("slow");
  			$('#divEmpID').addClass('active');
  			break;
  		case "by_dept":
  			$('.filter_div').hide("slow");
  			$('#divDept').show("slow");
  			$('#divDept').addClass('active');
  			break;
  		case "by_pos":
  			$('.filter_div').hide("slow");
  			$('#divPos').show("slow");
  			$('#divPos').addClass('active');
  			break;
  		default:

  	}

  });

  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    let tab = $('.nav-link.active').get(0).id;
    searchValue.filter = filter_by;
    // for single date
    if($("#"+filter_by).hasClass('single_search')){
      searchValue.search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('dual_search')){
      searchValue.from = $('#'+filter_by).children().find('.from').val();
      searchValue.to = $('#'+filter_by).children().find('.to').val();
    }

    // console.log(searchValue);
    // return;
    gen_employee_info_tbl(JSON.stringify(searchValue),JSON.stringify(searchStatus));

  });

  $('#employmee_info_modal').on('hidden.bs.modal', function(){
    $('#collapseTwo').collapse('hide');
    $('#collapseThree').collapse('hide');
    $('#collapseOne').collapse('toggle');
    $('#collapseOne').addClass('show');
  });
});
