$(function(){
  // console.log('error');
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var question = [];
  var recommend = [];
  var assessment = [];
  var project = [];

  var get_question = () => {
    question = [];
    $('.eval_quest').each(function(){
      var data = {};
      data.id = $(this).data('id');
      data.value = $(this).val();
      data.remarks = $(`#remark${$(this).data('id')}`).val() || '';
      question.push(data);
    });
  }
  var get_recommend = () => {
    recommend = [];
    $('.eval_recommend').each(function(){
      var data = {};
      data.id = $(this).data('id');
      data.value = $(this).val();
      recommend.push(data);
    });
  }
  var get_assessment = () => {
    assessment = [];
    $('.eval_assessment').each(function(){
      var data = {};
      data.id = $(this).data('id');
      data.value = $(this).val();
      assessment.push(data);
    });
  }
  var get_project = () => {
    project = [];
    $('.projects').each(function(){
      var data = [];
      data.push($(this).val());
      data.push($('input[name="task'+$(this).data('id')+'"]:checked').val());
      project.push(data);
    });
  }

  $(document).on('click', '#btn_next', function(){
    // alert();
    // return false;
    get_question();
    get_recommend();
    if($('#eval_type').val() == 'type_2'){
      get_assessment();
    }
    // get_project();

    $('#btn_back').show();
    $('#btn_save').show();

    var error = 0;
    var errorMsg = "";

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    var score = 0;
    var formula = $('#eval_formula').val();

    question.forEach((data) => {
      score += parseInt(data.value);
    });


    if(error == 0){
      $('#eval_form').hide('slow');
      $(this).hide();
      $.ajax({
        url: base_url+'evaluations/Evaluations/calculate',
        type: 'post',
        data:{score, formula},
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#eval_total_score').val(data.score);
            $('#eval_score_percent').val(data.score_percent + ' % ');
            $('#eval_equivalent_rate').val(data.result.equivalent_rating);
            $('.card-body').css("pointer-events", "none");
            $('#eval_form').show('slow');
            notificationSuccess('Success',data.message)
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }

    // console.log(score);
    // return false;
    // console.log(question);
    // console.log(recommend);
    // console.log(project);
  });

  $(document).on('click', '#btn_back', function(){
    // CLEAR
    $('#eval_total_score').val('');
    $('#eval_score_percent').val('');
    $('#eval_equivalent_rate').val('');
    // HIDE
    $('.card-body').css("pointer-events", "auto");
    $('#eval_form').hide('slow');
    $(this).hide();
    $('#btn_save').hide();
    // SHOW
    $('#btn_next').show();
    $('#eval_form').show('slow');
  });

  $(document).on('click', '#btn_save', function(){
    var error = 0;
    var errorMsg = "";
    // console.log(assessment);
    // return false

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'evaluations/Evaluations/create',
        type: 'post',
        data:{
          question: JSON.stringify(question),
          recommend: JSON.stringify(recommend),
          assessment: JSON.stringify(assessment),
          emp_comment: $('#emp_comment').val(),
          project: JSON.stringify(project),
          proj_comment: $('#proj_comment').val(),
          purpose: $('#purpose').val(),
          purpose_value: $('#purpose_value').val(),
          eval_id: $('#eval_id').val(),
          eval_total_score: $('#eval_total_score').val(),
          eval_score_percent: parseInt($('#eval_score_percent').val()),
          eval_equivalent_rate: $('#eval_equivalent_rate').val()
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            notificationSuccess('Success', data.message);
            setTimeout(() => {window.location.href = base_url + 'evaluations/Evaluations/index/'+token},1300)
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '#btn_certify', function(){
    var action_hr = $('#action_hr').val();
    var eval_id = $('#eval_id').val();
    // console.log(action_hr);
    // console.log(eval_id);
    // return;
    $.ajax({
      url: base_url + 'evaluations/Evaluations/certify',
      type: 'post',
      data: {action_hr, eval_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          notificationSuccess('Success', data.message);
          setTimeout(() => {window.location.href = base_url + 'evaluations/Evaluations/index/'+token},1300)
          // setTimeout(() => {location.reload()},1500);
        }else{
          notificationError('Error', data.message);
        }
      }
    })
  });

  $(document).on('click', '#btn_print', function(){
    // $('#print_div').css('page-break-after','always');
    var modal = document.getElementById('print_div').innerHTML;
	  var body = document.body.innerHTML;
	  document.body.innerHTML = modal;
	  window.print();
	  document.body.innerHTML = body;
  });

});
