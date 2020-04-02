$(function(){

	var base_url = $("body").data('base_url');

	function gen_systemUserTable(search){
    var systemUserTable = $('#systemUserTable').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [0,1,2], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Systemusers/get_systemuser_json',
        type: 'post',
        data: { searchValue: search },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        complete: function(){
          $.LoadingOverlay('hide');
        },
        error: function(){
					$.LoadingOverlay('hide');
        }
      }
    });
  }

	gen_systemUserTable('');
	 // var systemUserTable = $('#systemUserTable').DataTable({
		// 				processing:false,
		// 				serverSide:true,
		// 				searching: false,
		// 				ajax:{
		// 					url: base_url+'settings/Systemusers/systemusersjson',
		// 					beforeSend:function() {
		// 						$.LoadingOverlay('show');
		// 					},
		// 					complete:function() {
		// 						$.LoadingOverlay('hide');
		// 					}
		// 				},
		// 				columns:[
		// 					{data:'id'},
		// 					{data:'description'}
		// 				],
		// 				columnDefs:[{
		// 					"targets":2,
		// 					"data":null,
		// 					"render":function(data, type, row, meta) {
	 //
		// 						$(document).on('click','#edit-btn'+data.id,function(){
	 //
		// 							var systemUserId = $(this).data('id');
		// 							var description = $(this).data('description');
	 //
		// 							$('.systemuserid').val(systemUserId);
		// 							$('#editSystemUserDesc').val(description);
	 //
		// 						});
	 //
		// 						$(document).on('click','#delete-btn'+data.id,function(){
	 //
		// 							var systemUserId = $(this).data('id');
		// 							var description = $(this).data('description');
		// 							$('.systemuserid').val(systemUserId);
		// 							$('.info_desc').html(description)
		// 						});
	 //
		// 						var buttons = "";
		// 						buttons += "<center>";
		// 						buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editSystemUserModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
		// 						buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delSystemUserModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
		// 						buttons += "</center>";
	 //
		// 						return buttons;
		// 					}
		// 				}]
	 //
		// 			});



	$('#btnSearchSysUser').click(function(){
		var sysUser = $('.searchArea').val();
		$('#systemUserTable').DataTable().destroy();

		// var systemUserTable = $('#systemUserTable').DataTable({
 		// 				processing:false,
 		// 				serverSide:true,
 		// 				searching: false,
 		// 				ajax:{
 		// 					url: base_url+'settings/Systemusers/systemusersjson',
 		// 					beforeSend:function() {
 		// 						$.LoadingOverlay('show');
 		// 					},
		// 					data: { searchValue: sysUser },
 		// 					complete:function() {
 		// 						$.LoadingOverlay('hide');
 		// 					}
 		// 				},
 		// 				columns:[
 		// 					{data:'id'},
 		// 					{data:'description'}
 		// 				],
 		// 				columnDefs:[{
 		// 					"targets":2,
 		// 					"data":null,
 		// 					"render":function(data, type, row, meta) {
		//
 		// 						$(document).on('click','#edit-btn'+data.id,function(){
		//
 		// 							var systemUserId = $(this).data('id');
 		// 							var description = $(this).data('description');
		//
 		// 							$('.systemuserid').val(systemUserId);
 		// 							$('#editSystemUserDesc').val(description);
		//
 		// 						});
		//
 		// 						$(document).on('click','#delete-btn'+data.id,function(){
		//
 		// 							var systemUserId = $(this).data('id');
 		// 							var description = $(this).data('description');
 		// 							$('.systemuserid').val(systemUserId);
 		// 							$('.info_desc').html(description)
 		// 						});
		//
 		// 						var buttons = "";
 		// 						buttons += "<center>";
 		// 						buttons += "<button type='button' id='edit-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#editSystemUserModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
 		// 						buttons += " <button type='button' id='delete-btn"+data.id+"' data-id='"+data.id+"' data-description='"+data.description+"' data-toggle='modal' data-target='#delSystemUserModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
 		// 						buttons += "</center>";
		//
 		// 						return buttons;
 		// 					}
 		// 				}]
		//
 		// 			});
	});

	$('#addSystemUserBtn').click(function(){

		var description = $('#addSystemUserDesc').val();
		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description
		};

		$.ajax({
			url: base_url+'settings/Systemusers/create',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#addSystemUserBtn').attr('disabled',false);
				$('#addSystemUserBtn').html('Add');

				if(result.success == 1) {
					$('#addSystemUserDesc').val("");
					$('#addSystemUserModal').modal('toggle');
					notificationSuccess('Success',result.message);
					systemUserTable.ajax.reload(null,false);
				}else {
					$('#addSystemUserDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addSystemUserModal').on('hidden.bs.modal', function () {
		$('#addSystemUserDesc').val("");
	});

	$('#editSystemUserBtn').click(function(){

		var systemUserId = $('.systemuserid').val();
		var description = $('#editSystemUserDesc').val();

		var data = {
			id:systemUserId,
			description:description
		};

		$.ajax({
			url: base_url+'settings/Systemusers/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#editSystemUserModal').modal('toggle');
				notificationSuccess('Success',result.message);
				systemUserTable.ajax.reload(null,false);
			}
		});

	});

	$('#delSystemUserBtn').click(function(){

		var systemUserId = $('.systemuserid').val();

		var data = {
			id:systemUserId
		};

		$.ajax({
			url: base_url+'settings/Systemusers/destroy',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#delSystemUserModal').modal('toggle');
				notificationSuccess('Success',result.message);
				systemUserTable.ajax.reload(null,false);
			}
		});

	});


});
