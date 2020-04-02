 $(function(){
		var base_url = $('body').data('base_url');


				var serialize = $('#addSalCat-form').serialize();
				var salCatTable = $('#salCatTable').DataTable({
					processing:"true",
						serverSide:true,
            searching: false,
						ajax:{
							url: base_url+'settings/Salarycategory/salcatjson',
							dataSrc:'data',
              beforeSend: function(){
                $.LoadingOverlay('show');
              },
              complete: function(){
                $.LoadingOverlay('hide');
              }
						},
						columns:[
							{data:'salarycatid'},
							{data:'description'}
						],
						columnDefs:[{
							"targets":2,
							"data":null,
							"render":function(data, type, row, meta) {

								$(document).on('click','#edit-btn'+data.salarycatid,function(){

									var salarycatid = $(this).data('id');
									var description = $(this).data('description');

									$('.salarycatid').val(salarycatid);
									$('#updateSalCat_desc').val(description);

								});


								$(document).on('click','#delete-btn'+data.salarycatid,function(){

									var deptId = $(this).data('id');
									var description = $(this).data('description');
									$('.salarycatid').val(deptId);
									$('.updateSalCat_desc').html(description)
								});

								var buttons = "";
                buttons += "<center>";
								buttons += "<button type='button' id='edit-btn"+data.salarycatid+"' data-id='"+data.salarycatid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#updateSalCatModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
								buttons += " <button type='button' id='delete-btn"+data.salarycatid+"' data-id='"+data.salarycatid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteSalCatModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
                buttons += "</center>";

								return buttons;
							}

						}]


				});




    $('#btnSearchCat').click(function(){
      var cat = $('.searchArea').val();
      $('#salCatTable').DataTable().destroy();

      var salCatTable = $('#salCatTable').DataTable({
        processing:"true",
          serverSide:true,
          searching: false,
          ajax:{
            url: base_url+'settings/Salarycategory/salcatjson',
            dataSrc:'data',
            data: { searchValue: cat },
            beforeSend: function(){
              $.LoadingOverlay('show');
            },
            complete: function(){
              $.LoadingOverlay('hide');
            }
          },
          columns:[
            {data:'salarycatid'},
            {data:'description'}
          ],
          columnDefs:[{
            "targets":2,
            "data":null,
            "render":function(data, type, row, meta) {

              $(document).on('click','#edit-btn'+data.salarycatid,function(){

                var salarycatid = $(this).data('id');
                var description = $(this).data('description');

                $('.salarycatid').val(salarycatid);
                $('#updateSalCat_desc').val(description);

              });


              $(document).on('click','#delete-btn'+data.salarycatid,function(){

                var deptId = $(this).data('id');
                var description = $(this).data('description');
                $('.salarycatid').val(deptId);
                $('.updateSalCat_desc').html(description)
              });

              var buttons = "";
              buttons += "<center>";
              buttons += "<button type='button' id='edit-btn"+data.salarycatid+"' data-id='"+data.salarycatid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#updateSalCatModal' class='btn btn-primary' style='width:40%;'><i class='fa fa-pencil'></i> Edit </button>";
              buttons += " <button type='button' id='delete-btn"+data.salarycatid+"' data-id='"+data.salarycatid+"' data-description='"+data.description+"' data-toggle='modal' data-target='#deleteSalCatModal' class='btn btn-danger' style='width:40%;'><i class='fa fa-trash'></i> Delete</button>";
              buttons += "</center>";

              return buttons;
            }

          }]


      });
    });

		$('#saveBtnSalCat').click(function(){

		var description = $('#addSalCat_desc').val();

		var data = {
			description:description
		};

			$.ajax({
				type: 'POST',
				url: base_url + 'settings/Salarycategory/create',
				data: data,
				success:function(data){


						var result = JSON.parse(data);

						if(result.success == 1){
							$('#addSalCat_desc').val("");
							$('#addSalCatModal').modal('toggle');
							notificationSuccess('Success',result.message);
							salCatTable.ajax.reload(null,false);
						}
						else{
							$('#addSalCat_desc').val("");
							notificationError('Error',result.message);
							//$('#addSalCatModal').modal('toggle');
							salCatTable.ajax.reload(null,false);

						}
				}



			});
		});

		$('#updateSalCatBtn').click(function(){

		var salarycatid = $('.salarycatid').val();
		var description = $('#updateSalCat_desc').val();

		var data = {
			id:salarycatid,
			description:description
		};

		$.ajax({
			url: base_url+'settings/Salarycategory/update',
			type:'POST',
			data:data,
			success:function(data) {

				if(data.success == 0){
					notificationError('Error',data.message);
				}else{
				$('#updateSalCatModal').modal('toggle');
					notificationSuccess('Success',data.message);
					salCatTable.ajax.reload();
				}
			}
		});

	});

			$('#deleteSalCatBtn').click(function(){

		var salarycatid = $('.salarycatid').val();

		var data = {
			id:salarycatid
		};

		$.ajax({
			url: base_url+'settings/Salarycategory/destroy',
			type:'POST',
			data:data,
			success:function(data) {
				var result = JSON.parse(data);
				console.log(data);
				$('#deleteSalCatModal').modal('toggle');
				notificationSuccess('Success',result.message);
				salCatTable.ajax.reload();
			}
		});

	});


});
