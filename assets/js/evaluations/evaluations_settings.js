$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var assessment_tbl_data = {
    action: "",
    id: "",
    rating: "",
    desc: "",
    equivalent_rating: "",
    score: ""
  }
  var assessment_questions_data = {
    action: "",
    id: "",
    title: "",
    section: "",
    desc: ""
  }
  var recommend_data = {
    action: "",
    id: "",
    desc: ""
  }
  var assessment_data = {
    action: "",
    id: "",
    desc: ""
  }

  $(document).on('click', '.btn_assess_tbl', function(){
    $('#btn_save').text('Save');
    var action = $(this).data('action');
    assessment_tbl_data.action = action;
    switch (action) {
      case 'add':
        $('#assessment_tbl_modal .modal-title').text(action.toUpperCase() + ' PERFORMACE EVALUATION');

        $('#assessment_tbl_modal .sections').removeClass('active');
        $('#assessment_tbl_modal .sections').hide();

        $(`#${action}_section`).addClass('active');
        $(`#${action}_section`).show();
        $('#assessment_tbl_modal').modal();
        break;
      case 'edit':
        $('#assessment_tbl_modal .modal-title').text(action.toUpperCase() + ' PERFORMACE EVALUATION');
        $('#edit_id').val($(this).data('id'));
        $('#edit_rating').val($(this).data('rating'));
        $('#edit_desc').val($(this).data('desc'));
        $('#edit_equivalent_rating').val($(this).data('equivalent_rating'));
        $('#edit_score').val($(this).data('score'));

        $('#assessment_tbl_modal .sections').removeClass('active');
        $('#assessment_tbl_modal .sections').hide();
        $(`#${action}_section`).addClass('active');
        $(`#${action}_section`).show();
        $('#assessment_tbl_modal').modal();
        break;
      case 'delete':
        assessment_tbl_data.id = $(this).data('id');
        $('#assessment_tbl_modal .sections').removeClass('active');
        $('#assessment_tbl_modal .sections').hide();

        $(`#${action}_section`).addClass('active');
        $(`#${action}_section`).show();

        $('#assessment_tbl_modal .modal-title').text(action.toUpperCase() + ' PERFORMACE EVALUATION');
        $('#btn_save').text('Yes');
        $('#delete_item').text($(this).data('equivalent_rating'));
        $('#assessment_tbl_modal').modal();
        break;
      default:

    }
  });

  $(document).on('click', '#btn_save', function(){
    var error = 0;
    var errorMsg = "";

    switch (assessment_tbl_data.action) {
      case 'add':
        assessment_tbl_data.rating = $('#add_rating').val();
        assessment_tbl_data.desc = $('#add_desc').val();
        assessment_tbl_data.equivalent_rating = $('#add_equivalent_rating').val();
        assessment_tbl_data.score = $('#add_score').val();
        // console.log(assessment_tbl_data);
        break;
      case 'edit':
        assessment_tbl_data.id = $('#edit_id').val();
        assessment_tbl_data.rating = $('#edit_rating').val();
        assessment_tbl_data.desc = $('#edit_desc').val();
        assessment_tbl_data.equivalent_rating = $('#edit_equivalent_rating').val();
        assessment_tbl_data.score = $('#edit_score').val();
        break;
      case 'delete':
        break;
      default:

    }

    if(assessment_tbl_data.action !== 'delete'){
      $(`.rq_${assessment_tbl_data.action}`).each(function(){
        if($(this).val() == ""){
          $(this).css("border", "1px solid #ef4131");
        }else{
          $(this).css("border", "1px solid gainsboro");
        }
      });

      $(`.rq_${assessment_tbl_data.action}`).each(function(){
        if($(this).val() == ""){
          $(this).focus();
          error = 1;
          errorMsg = "Please fill up all required fields.";
          return false;
        }
      });
    }

    if(error == 0){
      $.ajax({
        url: base_url+'evaluations/Evaluations_settings/assessment_tbl_settings',
        type: 'post',
        data: assessment_tbl_data,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#assessment_tbl_modal').modal('hide');
            notificationSuccess('Success', data.message);
            setTimeout(function(){
              location.reload();
            },1500)
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
    // console.log('Hi');
  });

  $(document).on('click', '.btn_assess_question', function(){
    $('#btn_save2').text('Save');
    var action = $(this).data('action');
    assessment_questions_data.action = action;
    switch (action) {
      case 'add':
        $('.sections2').hide();
        $(`#${action}_sections2`).show();
        $('.modal-title').text(action.toUpperCase() + " ASSESMENT QUESTIONS");
        $('#assessment_questions_modal').modal();
        break;
      case 'edit':
        $(`#${action}_id2`).val($(this).data('id'));
        $(`#${action}_title2`).val($(this).data('title'));
        $(`#${action}_section2 option[value="${$(this).data('section')}"]`).prop('selected', true);
        $(`#${action}_desc2`).val($(this).data('desc'));

        $('.sections2').hide();
        $(`#${action}_sections2`).show();
        $('.modal-title').text(action.toUpperCase() + " ASSESMENT QUESTIONS");
        $('#assessment_questions_modal').modal();
        break;
      case 'delete':
        assessment_questions_data.id = $(this).data('id');
        $('#delete_item2').text($(this).data('title'));
        $('#btn_save2').text('Yes');

        $('.sections2').hide();
        $(`#${action}_sections2`).show();
        $('.modal-title').text(action.toUpperCase() + " ASSESMENT QUESTIONS");
        $('#assessment_questions_modal').modal()
        break;
      default:

    }
  });

  $(document).on('click', '#btn_save2', function(){
    var action = assessment_questions_data.action;
    switch (action) {
      case 'add':
        assessment_questions_data.title = $('#add_title2').val();
        assessment_questions_data.section = $('#add_section2').val();
        assessment_questions_data.desc = $('#add_desc2').val();
        break;
      case 'edit':
        assessment_questions_data.id = $('#edit_id2').val();
        assessment_questions_data.title = $('#edit_title2').val();
        assessment_questions_data.section = $('#edit_section2').val();
        assessment_questions_data.desc = $('#edit_desc2').val();
        break;
      case 'delete':

        break;
      default:
    }

    var error = 0;
    var errorMsg = "";
    // console.log(assessment_questions_data);

    if(action !== 'delete'){
      $(`.rq2_${action}`).each(function(){
        if($(this).val() == ""){
          $(this).css("border", "1px solid #ef4131");
        }else{
          $(this).css("border", "1px solid gainsboro");
        }
      });

      $(`.rq2_${action}`).each(function(){
        if($(this).val() == ""){
          $(this).focus();
          error = 1;
          errorMsg = "Please fill up all required fields.";
          return false;
        }
      });
    }

    if(error == 0){
      $.ajax({
        url: base_url+'evaluations/Evaluations_settings/assessment_question_settings',
        type: 'post',
        data:assessment_questions_data,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#assessment_questions_modal').modal('hide');
            notificationSuccess("Success", data.message);
            setTimeout(() => { location.reload()}, 1500);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_recommend', function(){
    $('#btn_save3').text('Save');
    var action = $(this).data('action');
    recommend_data.action = action;
    switch (action) {
      case 'add':
        $('.sections3').hide();
        $(`#${action}_sections3`).show();
        $('.modal-title').text(action.toUpperCase() + " RECOMMENDATIONS AND DEVELOPMENT");
        $('#recommend_modal').modal();
        break;
      case 'edit':
        $(`#${action}_id3`).val($(this).data('id'));
        $(`#${action}_desc3`).val($(this).data('desc'));

        $('.sections3').hide();
        $(`#${action}_sections3`).show();
        $('.modal-title').text(action.toUpperCase() + " RECOMMENDATIONS AND DEVELOPMENT");
        $('#recommend_modal').modal();
        break;
      case 'delete':
        recommend_data.id = $(this).data('id');
        $('#delete_item3').text($(this).data('desc'));
        $('#btn_save3').text('Yes');

        $('.sections3').hide();
        $(`#${action}_sections3`).show();
        $('.modal-title').text(action.toUpperCase() + " RECOMMENDATIONS AND DEVELOPMENT");
        $('#recommend_modal').modal()
        break;
      default:

    }
  });

  $(document).on('click', '.btn_assessment', function(){
    $('#btn_save4').text('Save');
    var action = $(this).data('action');
    assessment_data.action = action;
    switch (action) {
      case 'add':
        $('.sections4').hide();
        $(`#${action}_sections4`).show();
        $('.modal-title').text(action.toUpperCase() + " SELF ASSESMENT");
        $('#assessment_modal').modal();
        break;
      case 'edit':
        $(`#${action}_id4`).val($(this).data('id'));
        $(`#${action}_desc4`).val($(this).data('desc'));

        $('.sections4').hide();
        $(`#${action}_sections4`).show();
        $('.modal-title').text(action.toUpperCase() + " SELF ASSESMENT");
        $('#assessment_modal').modal();
        break;
      case 'delete':
        recommend_data.id = $(this).data('id');
        $('#delete_item4').text($(this).data('desc'));
        $('#btn_save4').text('Yes');

        $('.sections4').hide();
        $(`#${action}_sections4`).show();
        $('.modal-title').text(action.toUpperCase() + " SELF ASSESMENT");
        $('#assessment_modal').modal()
        break;
      default:

    }
  });

  $(document).on('click', '#btn_save3', function(){
    var action = recommend_data.action;
    switch (action) {
      case 'add':
        recommend_data.desc = $(`#${action}_desc3`).val();
        break;
      case 'edit':
        recommend_data.desc = $(`#${action}_desc3`).val();
        recommend_data.id = $(`#${action}_id3`).val();
      case 'delete':
        break;
      default:

    }

    var error = 0;
    var errorMsg = "";

    if(action !== 'delete'){
      $(`.rq3_${action}`).each(function(){
        if($(this).val() == ""){
          $(this).css("border", "1px solid #ef4131");
        }else{
          $(this).css("border", "1px solid gainsboro");
        }
      });

      $(`.rq3_${action}`).each(function(){
        if($(this).val() == ""){
          $(this).focus();
          error = 1;
          errorMsg = "Please fill up all required fields.";
          return false;
        }
      });
    }

    // console.log(recommend_data);

    if(error == 0){
      $.ajax({
        url: base_url+'evaluations/Evaluations_settings/recommendation_settings',
        type: 'post',
        data:recommend_data,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#recommend_modal').modal('hide');
            notificationSuccess('Success', data.message);
            setTimeout(() => { location.reload()}, 1500);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '#btn_save4', function(){
    var action = assessment_data.action;
    switch (action) {
      case 'add':
        assessment_data.desc = $(`#${action}_desc4`).val();
        break;
      case 'edit':
        assessment_data.desc = $(`#${action}_desc4`).val();
        assessment_data.id = $(`#${action}_id4`).val();
      case 'delete':
        break;
      default:

    }

    var error = 0;
    var errorMsg = "";

    if(action !== 'delete'){
      $(`.rq4_${action}`).each(function(){
        if($(this).val() == ""){
          $(this).css("border", "1px solid #ef4131");
        }else{
          $(this).css("border", "1px solid gainsboro");
        }
      });

      $(`.rq4_${action}`).each(function(){
        if($(this).val() == ""){
          $(this).focus();
          error = 1;
          errorMsg = "Please fill up all required fields.";
          return false;
        }
      });
    }

    // console.log(recommend_data);

    if(error == 0){
      $.ajax({
        url: base_url+'evaluations/Evaluations_settings/self_assessment_settings',
        type: 'post',
        data:assessment_data,
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#assessment_modal').modal('hide');
            notificationSuccess('Success', data.message);
            setTimeout(() => { location.reload()}, 1500);
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '.btn_formula', function(){
    var id = $(this).data('id');
    var formula = $(this).data('formula');
    $('#eval_formula').val(formula);
    $('#eval_formula_modal').modal();

    $('#btn_update_formula').click(function(){
      var updated_formula = $('#eval_formula').val();
      if(formula !== ""){
        $.ajax({
          url: base_url+'evaluations/Evaluations_settings/eval_formula_settings',
          type: 'post',
          data:{id: id, formula: updated_formula},
          beforeSend: function(){
            $.LoadingOverlay('show');
          },
          success: function(data){
            $.LoadingOverlay('hide');
            if(data.success == 1){
              $('#eval_formula_modal').modal('hide');
              notificationSuccess('Success', data.message);
              setTimeout(() => {location.reload()},1500);
            }else{
              notificationError('Error', data.message);
            }
          }
        });
      }else{
        notificationError('Error', 'Please fill up all required fields.');
      }
    });
  });
});
