$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var searchValue  = {
    filter: "",
    search: "",
    from: "",
    to: ""
  };
  $.fn.extend({
      placeCursorAtEnd: function() {
          // Places the cursor at the end of a contenteditable container (should also work for textarea / input)
          if (this.length === 0) {
              throw new Error("Cannot manipulate an element if there is no element!");
          }
          var el = this[0];
          var range = document.createRange();
          var sel = window.getSelection();
          var childLength = el.childNodes.length;
          if (childLength > 0) {
              var lastNode = el.childNodes[childLength - 1];
              var lastNodeChildren = lastNode.childNodes.length;
              range.setStart(lastNode, lastNodeChildren);
              range.collapse(true);
              sel.removeAllRanges();
              sel.addRange(range);
          }
          return this;
      }
  });

  var e = $('#summernote');
  var f = $('#edit_summernote');
  // $('.note-editable').html('<h1>Hellow</h1>');
  e.summernote();
  f.summernote();

  function gen_contract_template_tbl(search){
    var contract_template_tbl = $('#contract_template_tbl').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2,3], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Contract_template/get_contract_template_json',
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

  gen_contract_template_tbl(JSON.stringify(searchValue));

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
  		case "by_date":
  			$('.filter_div').hide("slow");
  			$('#divDate').show("slow");
  			$('#divDate').addClass('active');
  			break;
  		case "by_amount":
  			$('.filter_div').hide("slow");
  			$('#divAmount').show("slow");
  			$('#divAmount').addClass('active');
  			break;
  		default:

  	}

  });
  // CALL ADD MODAL
  $(document).on('click', '#btn_add', function(){
    $('#add_modal').modal();
  });
  // ADD FIELDS
  $(document).on('click', '#add_fields', function(){
    var selected = $('#select_fields').find('option:selected');
    var id = selected.val();
    var field = selected.data('text');
    e.summernote('focus');
    // $('.note-editable').placeCursorAtEnd();
    // $('.cke_editable ').append('<img class = "'+id+'"><strong>'+field+'</strong>');
    switch (id) {
      case "check_box":
        e.summernote('pasteHTML', '<input type="checkbox" name = "checkbox[]" class = "'+id+'"/>');
        e.summernote('pasteHTML', '<span>&nbsp;</span>');
        break;
      case "signature":
        e.summernote('pasteHTML', '<canvas class = "signature-pad" width = 300 height = 100></canvas>');
        e.summernote('pasteHTML', '<span>&nbsp;</span>');
        var canvas = document.querySelector('.signature-pad');
        var signature_pad = new SignaturePad(canvas);
        break;
      case "input_date":
        e.summernote('pasteHTML', '<input type="text" class="form-control date_input_empty"/ style = "width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;">');
        e.summernote('pasteHTML', '<span>&nbsp;</span>');
        $('.date_input_empty').datepicker(
          {format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
        );
        break;
      case "input_text":
        e.summernote('pasteHTML', '<input type="text" class="form-control input-text"/ style = "width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;"><span class = "input-text-container"></span>');
        e.summernote('pasteHTML', '<span>&nbsp;</span>');
        break;
      default:
        e.summernote('pasteHTML', '<span class = "'+id+'"><b>'+field+'</b></span>');
        e.summernote('pasteHTML', '<span>&nbsp;</span>');
    }

  });
  // ADD EDIT FIELDS
  $(document).on('click', '#add_fields2', function(){
    var selected = $('#edit_select_fields').find('option:selected');
    var id = selected.val();
    var field = selected.data('text');
    f.summernote('focus');
    // $('.note-editable').placeCursorAtEnd();
    // $('.cke_editable ').append('<img class = "'+id+'"><strong>'+field+'</strong>');
    switch (id) {
      case "check_box":
        f.summernote('pasteHTML', '<input type="checkbox" name = "checkbox[]" class = "'+id+'"/>');
        f.summernote('pasteHTML', '<span>&nbsp;</span>');
        break;
      case "signature":
        f.summernote('pasteHTML', '<canvas class = "signature-pad" width = 300 height = 100></canvas><img class = "signature-pad-img" src="" alt="" />');
        f.summernote('pasteHTML', '<span>&nbsp;</span>');
        var canvas = document.querySelector('.signature-pad');
        var signature_pad = new SignaturePad(canvas);
        break;
      case "input_date":
        f.summernote('pasteHTML', '<input type="text" class="form-control date_input_empty"/ style = "width:200px !important;border:none;border-bottom: 1px solid black;display:inline !important;">');
        f.summernote('pasteHTML', '<span>&nbsp;</span>');
        $('.date_input_empty').datepicker(
          {format: 'yyyy-mm-dd', autoclose: true, todayHighlight: true}
        );
        break;
      case "input_text":
        f.summernote('pasteHTML', '<input type="text" class="form-control input-text"/ style = "width:100px !important;border:none;border-bottom: 1px solid black;display:inline !important;"><span class="input-text-container"></span>');
        f.summernote('pasteHTML', '<span>&nbsp;</span>');
        break;
      default:
        f.summernote('pasteHTML', '<span class = "'+id+'"><b>'+field+'</b></span>');
        f.summernote('pasteHTML', '<span>&nbsp;</span>');
    }

  });

  // $(document).on('keyup', '.note-editable', function(e){
  //   if(e.keyCode === 13){
  //     $(this).append('h1');
  //   }
  // })
  // SUBMIT ADD FORM
  $(document).on('submit', '#template_form', function(e){
    e.preventDefault();
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

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Contract_template/create',
        type: 'post',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_save').attr('disabled', true)
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_save').prop('disabled', false);
          if(data.success == 1){
            $('#add_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_contract_template_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // CALL EDIT MODAL
  $(document).on('click', '.btn_view', function(){
    var thiss = $(this);
    var id = thiss.data('uid');
    var template_type = $(this).data('template_type');
    // console.log(template_type);
    // return;
    $.ajax({
      url: base_url+'settings/Contract_template/edit',
      type: 'post',
      data:{id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#uid').val(id);
          $('#edit_template_name').val(data.template_name);
          $('#edit_modal .note-editable').html(data.template_format);
          $('#edit_template_type option[value="'+template_type+'"]').prop('selected', true);
          var canvas_count = $('.signature-pad').length;
          if(canvas_count > 0){
            var signature_pad = new SignaturePad(document.querySelector('.signature-pad'));
          }
        }else{
          notificationError('Error',data.message);
        }
      }
    });
    $('#edit_template_name').val(thiss.data('template_name'));
    $('#edit_summernote').val(thiss.data('template_format'));
    $('#edit_modal').modal();
  });
  // SUBMIT EDIT FORM
  $(document).on('submit', '#edit_template_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";
    $('.rq2').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq2').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Contract_template/update',
        type: 'post',
        data:new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_update').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_update').prop('disabled', false);
          if(data.success == 1){
            $('#edit_modal').modal('hide');
            notificationSuccess('Success',data.message);
            gen_contract_template_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error',data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });
  // CALL DELETE MODAL
  $(document).on('click', '.btn_delete', function(){
    var thiss = $(this);
    $('#del_txt').text(thiss.data('template_name'));
    $('#delid').val(thiss.data('delid'));
    $('#delete_modal').modal();
  });
  // SUBMIT DELETE FORM
  $(document).on('submit', '#delete_template_form', function(e){
    e.preventDefault();
    var error = 0;
    var errorMsg = "";

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
        errorMsg = "Unable to find any id.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'settings/Contract_template/delete',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_yes').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_yes').prop('disabled', false);
          if(data.success == 1){
            $('#delete_modal').modal('hide');
            notificationSuccess('Success', data.message);
            gen_contract_template_tbl(JSON.stringify(searchValue));
          }else{
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $('#searchButton').click(function(){
    var filter_by = $('.filter_div.active').get(0).id;
    searchValue.filter = filter_by;
    // for single date
    if($("#"+filter_by).hasClass('single_search')){
      searchValue.search = $('#'+filter_by).children('.searchArea').val();
    }
    // for date range
    if($("#"+filter_by).hasClass('range_date')){
      searchValue.from = $('#'+filter_by).children().find('.from').val();
      searchValue.to = $('#'+filter_by).children().find('.to').val();
    }

    gen_contract_template_tbl(JSON.stringify(searchValue));

  });

  // $('#edit_modal').on('show.bs.modal', function(){
  //   var canvas_count = $('.signature-pad').length;
  //   console.log(canvas_count);
  //   if(canvas_count > 0){
  //     var signature_pad = new SignaturePad(document.querySelector('.signature-pad'));
  //   }
  // })
});
