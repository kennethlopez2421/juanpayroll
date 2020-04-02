$(function(){
	var base_url = $('body').data('base_url');
 var btncheck = 0;
$(document).on('click', '.emp_qrcode', function(){
	$('#view_modal').modal();
});

$("#default_pic").bind('click',function(e){
	e.preventDefault();
	$("#image_file").click();
$("#upload_btn").show();

});
$("#disabled_textbox").bind('click',function(e){
  e.preventDefault();
  $("#edit_image_file").click();
});
var employee_idno = $('#employee_idno').val();
var data = {
  employee_idno:employee_idno
};
// get_employee_ids()
$("#image_upload_btn").click(function(){
	var image_upload = $("#image_upload").val();
	if(image_upload == ""){
		notificationError("Error","No / Invalid file has been selected");
		$(this).hide();
	}else{ //may laman
		console.log(image_upload);
		var formData = new FormData();
		formData.append('image_file', image_upload);
     $.ajax({
          url:base_url + "profile/Edit_profile/ajax_upload",
          //base_url() = http://localhost/tutorial/codeigniter
          method:"POST",
          data:formData,
          contentType: false,
          cache: false,
          processData:false,
          success:function(data)
          {
          	alert('adslfadskhfkjadsgfadfagfgj');
          	$("#upload_btn").hide();
          }
     });

	}
});

        var id_table = $('#id_table').DataTable({
            processing:false,
            serverSide:false,
            destroy:true,
            ajax:{
              url: base_url+'profile/Edit_profile/get_valid_ids',
              data:data
            },
            columns:[
              {data:'id'},
              {data:'valid_id_type'},
              {data:'id_number'},
              {data:'id_value'},
              {data:'upload_date'},
            ],
            columnDefs:[
            {
              "targets":5,
              "data":null,
              "render":function(data, type, row, meta) {

                $(document).on('click','#view_button'+data.id,function(e){
                  e.stopImmediatePropagation();
                  var picture_extension = $(this).data('picture');
                  $('#picture_base').html('<img src="'+base_url+'/assets/employee_ids/'+picture_extension+'" style = "width: 800px;" class="img-fluid img-thumbnail" id = "emp_pic" alt="smaple image">');
                });
                $(document).on('click','#edit_button'+data.id,function(e){
                  e.stopImmediatePropagation();
                  var valid_id_id = $(this).data('id');
                  var picture = $(this).data('picture');
                  var valid_id_type = $(this).data('valid_id_type');
                  var id_number = $(this).data('id_number');
                  var id_value = $(this).data('id_value');

                  $('#edit_valid_id_id').val(valid_id_id);
                  $('#edit_image_file_temp').val(picture);
                  $("#edit_valid_id_type").val(valid_id_type);
                  $('#edit_id_number').val(id_number);
                  $("#edit_id_value").val(id_value);

                });


                $(document).on('click','#delete_button'+data.id,function(e){
                  e.stopImmediatePropagation();

                  var valid_id_id = $(this).data('id');
                  var id_type = $(this).data('id_type');

                  $('#id_type_delete').html(id_type);
                  $("#delete_id").val(valid_id_id);
                  // var description = $(this).data('description');
                  // $('.caID').val(caID);
                });

                var buttons = "";
                buttons += " <button type='button' id = 'view_button"+data.id+"' data-picture = '"+data.picture_extension+"' data-toggle='modal' data-target='#ViewImageModal' class='btn btn-info' style='width:30%;'><i class = 'fa fa-eye'></i>&nbsp; View ID </button>";
                buttons += " <button type='button' id = 'edit_button"+data.id+"' data-id = '"+data.id+"' data-picture = '"+data.picture_extension+"' data-valid_id_type = '"+data.valid_id_type+"' data-id_number = '"+data.id_number+"'data-id_value = '"+data.id_value+"' data-toggle='modal' data-target='#EditModal' class='btn btn-primary' style='width:30%;'><i class = 'fa fa-edit'></i>&nbsp Edit</button>";
                buttons += " <button type='button' id = 'delete_button"+data.id+"' data-id_type = '"+data.valid_id_type+"'  data-id = '"+data.id+"' data-toggle='modal' data-target='#DeleteModal' class='btn btn-danger' style='width:30%;'><i class = 'fa fa-trash'></i>&nbsp; Delete</button>";


                return buttons;

              }

            }]


        });
// function get_employee_ids(){
//  var employee_idno = $('#employee_idno').val();
//  var data = {
//     employee_idno:employee_idno
//  }
//   $.ajax({
//     url: base_url + "profile/Edit_profile/employee_valid_ids",
//     type: "POST",
//     data:data,
//     beforeSend: function(){
//       $.LoadingOverlay('show');
//     },
//     success:function(data){
//       var result = JSON.parse(data);
//       if(result.success == 1){
//         var resultnum = result.output.length;
//         var resultoutput = result.output;
//         var card_append = "";
//         for(x = 0; x < resultnum; x++){
//           //for color of id value
//           var value_color = "";
//           if(resultoutput[x].id_value == "Primary"){
//             value_color = "success";
//           }else{
//             value_color = "danger";
//           }
//           card_append += '<div class = "card-footer">';
//           card_append += ' <div class = "row">';
//           card_append += '        <div class = "col-md-8">';
//           card_append += '            <div class = "row">';
//           card_append += '              <div class = "col-md-3">';
//           card_append += '                <p><b>Valid ID Type: </b>'+resultoutput[x].valid_id_type+'</p>';
//           card_append += '              </div>';
//           card_append += '              <div class = "col-md-3">';
//           card_append += '                <p><b>ID Number: </b>'+resultoutput[x].id_number+'</p>';
//           card_append += '              </div>';
//           card_append += '              <div class = "col-md-3">';
//           card_append += '                <p><b>ID Value: </b><span class = "text-'+value_color+'">'+resultoutput[x].id_value+'</span></p>';
//           card_append += '              </div>';
//           card_append += '              <div class = "col-md-3">';
//           card_append += '                <p><b>Upload Date: </b>'+resultoutput[x].upload_date+'</p>';
//           card_append += '              </div>';
//           card_append += '            </div>';
//           card_append += '        </div>';
//           card_append += '        <div class = "col-md-4">';
//           card_append += '              <div class="form-group row">';
//           card_append += '                <div class = "pull-right">';
//           card_append += '                  <div class = "col-md-12">';
//           card_append += '                      <button data-toggle="modal" id="view_pdf_btn" data-target = "#ViewImageModal" class="btn btn-primary text-right btnClickAddArea" style=""><i class="fa fa-eye"></i>&nbsp;View Image</button>';
//           card_append += '                      <button data-toggle="modal" id="view_pdf_btn" data-target = "#EditModal" class="btn btn-warning text-right btnClickAddArea" style=""><i class="fa fa-edit"></i>&nbsp;Edit</button>';
//           card_append += '                      <button data-toggle="modal" id="view_pdf_btn" data-target = "#DeleteModal" class="btn btn-danger text-right btnClickAddArea" style=""><i class="fa fa-trash"></i>&nbsp;Delete</button>';
//           card_append += '                  </div>';
//           card_append += '                </div>';
//           card_append += '              </div>';
//           card_append += '        </div>';
//           card_append += '      </div>';
//           card_append += '  </div>';
//         }
//         // $("#profile_card").append(card_append);
//         $.LoadingOverlay('hide');
//       }else{
//         $.LoadingOverlay('hide');
//       }
//     }
//   });
// }
$("#image_file").change(function(){
  readURL(this);
});
    $('#upload_form').on('submit', function(e){
       e.preventDefault();
       if($('#image_file').val() == '')
       {
			notificationError("Error","No / Invalid file has been selected. Page will reload.");
      setTimeout(function(){
       location.reload();
     }, 3000);
       }
       else
       {
            $.ajax({
                 url:base_url + "profile/Edit_profile/ajax_upload",
                 //base_url() = http://localhost/tutorial/codeigniter
                 method:"POST",
                 data:new FormData(this),
                 contentType: false,
                 cache: false,
                 processData:false,
                 success:function(data)
                 {
                  $('#image_file').val('');
                  $(this).hide();
                  location.reload();
                 }
            });
          }
      });
    $("#delete_btn").click(function(){
      var id = $("#delete_id").val();
      var data = {
        id:id
      };
      $.ajax({
        url: base_url + "profile/Edit_profile/Delete_id",
        type: "POST",
        data:data,
        beforeSend:function(){
          $.LoadingOverlay('show');
        },
        success:function(data){

          var result = JSON.parse(data);

          if(result.success == 1){

             $.LoadingOverlay('hide');
             notificationSuccess("Success", result.output);
             id_table.ajax.reload(null,false);
          }else{
            $.LoadingOverlay('hide');
             notificationSuccess("Success", result.output);
          }
        }
      });
      $("#DeleteModal").modal('toggle');
    });
    $("#edit_info_btn").click(function(){
        var first_name = $("#inf_first_name").val();
        var middle_name = $("#inf_middle_name").val();
        var last_name = $("#inf_last_name").val();
        var contact_number = $("#inf_contact_number").val();
        var gender = $("#inf_gender").val();
        var birthdate = $("#inf_birth_date").val();
        var marital_status = $("#inf_marital_status").val();
        var email = $("#inf_email").val();
        var address1 = $("#inf_address1").val();
        var address2 = $("#inf_address2").val();
        var country = $("#inf_country").val();


        var data = {
              first_name:first_name,
              middle_name:middle_name,
              last_name:last_name ,
              contact_number:contact_number,
              marital_status:marital_status,
              gender:gender,
              birthdate:birthdate,
              email:email,
              address1:address1,
              address2:address2,
              country:country
        };

        $.ajax({
          url: base_url + "profile/Edit_profile/Temp_save_details",
          type: "POST",
          data:data,
          success:function(data){
            var result = JSON.parse(data);

            if(result.success == 1){
              notificationSuccess("Success", result.output);
            }else{
              notificationError("Error", resultoutput);
            }

          }
        });
        $("#Edit_information_modal").modal('toggle');
    });
    $('#add_upload_form').on('submit', function(e){
       e.preventDefault();
        // var valid_id_type = $('#valid_id_type').val();
        // var id_number = $('#id_number').val();
        // var id_value = $("#id_value").val();
        // var add_image_file = $("#add_image_file").val();
        var formData = new FormData(this);
        // formData.append('valid_id_type',valid_id_type);
        // formData.append('id_number',id_number);
        // formData.append('id_value',id_value);
        // formData.append('add_image_file',this);

            $.ajax({
                 url:base_url + "profile/Edit_profile/Add_new_id",
                 //base_url() = http://localhost/tutorial/codeigniter
                 method:"POST",
                 data:formData,
                 contentType: false,
                 cache: false,
                 processData:false,
                 beforeSend:function(){
                  $.LoadingOverlay('show');
                 },
                 success:function(data)
                 {
                    var result = JSON.parse(data);
                    var resultoutput = result.output;

                    if(result.success == 1){
                      $('#valid_id_type').val('');
                      $('#id_number').val('');
                      $("#id_value").val('');
                      $("#add_image_file").val('');
                      id_table.ajax.reload(null,false);
                      notificationSuccess('Success', resultoutput);
                      $.LoadingOverlay('hide');
                    }else{
                      notificationError('Error',resultoutput);
                      $.LoadingOverlay('hide');
                    }
                 }
            });
          $('#AddIDModal').modal('toggle');
      });


    $('#edit_upload_form').on('submit', function(e){
     e.preventDefault();
      $.ajax({
           url:base_url + "profile/Edit_profile/edit_id",
           //base_url() = http://localhost/tutorial/codeigniter
           method:"POST",
           data:new FormData(this),
           contentType: false,
           cache: false,
           processData:false,
           beforeSend:function(){
            $.LoadingOverlay('show');
           },
           success:function(data)
           {
            var result = JSON.parse(data);
            var resultoutput = result.output;

            if(result.success == 1){
              $('#edit_valid_id_type').val('');
              $('#edit_id_number').val('');
              $("#edit_id_value").val('');
              $("#edit_image_file").val('');
              $("#edit_image_file_temp").val('');
              id_table.ajax.reload(null,false);
              notificationSuccess('Success', resultoutput);
              $.LoadingOverlay('hide');
            }else{
              notificationError('Error',resultoutput);
              $.LoadingOverlay('hide');
            }

           }
      });
      $("#EditModal").modal('toggle');
    });
  function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#emp_pic').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}
})
