$(function(){

	var base_url = $("body").data('base_url');

	$('[data-toggle="tooltip"]').tooltip();

	function gen_workSiteTable(search){
    var workSiteTable_tbl = $('#workSiteTable').DataTable( {
      "processing": true,
      "serverSide": true,
      "searching": false,
      "destroy": true,
      "autoWidth": false,
      "columnDefs":[
        {targets: [1,2,3,4], orderable: false}
      ],
      "ajax":{
        url: base_url+'settings/Worksite/get_worksite_json',
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

	gen_workSiteTable('');

	$('#w_city').select2();

	$('#addWorkSiteBtn').click(function(){

		var description = $('#addWorkSiteDesc').val();
		var city = $('#w_city').val();
		var loc_address = $('#pac-input').val();
		var loc_latitude = $('#loc_latitude').val();
		var loc_longitude = $('#loc_longitude').val();
		var distance = $('#addDistance').val();

		$(this).attr('disabled',true);
		$(this).html('Please Wait');

		var data = {
			description:description,
			city,
			loc_address,
			loc_latitude,
			loc_longitude,
			distance:distance
		};

		$.ajax({
			url: base_url+'settings/worksite/create',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);

				$('#addWorkSiteBtn').attr('disabled',false);
				$('#addWorkSiteBtn').html('Add');

				if(result.success == 1) {
					$('#addWorkSiteDesc').val("");
					$('#addWorkSiteModal').modal('toggle');
					notificationSuccess('Success',result.message);
					gen_workSiteTable('');
				}else {
					$('#addWorkSiteDesc').val("");
					notificationError('Error',result.message);
				}
			}
		});
	});

	$('#addWorkSiteModal').on('hidden.bs.modal', function () {
		$('#addWorkSiteDesc').val("");
		$('#addDistance').val("");
	});

	$('#editWorkSiteBtn').click(function(){

		var workSiteId = $('.worksiteid').val();
		var description = $('#editWorkSiteDesc').val();
		var city = $('#edit_w_city').val();
		var currentDesc = $('#currentWorkSiteDesc').val();
		var edit_location = $('#edit_pac-input').val();
		var edit_loc_latitude = $('#edit_loc_latitude').val();
		var edit_loc_longitude = $('#edit_loc_longitude').val();
		var distance = $('#editDistance').val();

		var data = {
			id:workSiteId,
			description:description,
			city,
			currentDesc,
			edit_location,
			edit_loc_latitude,
			edit_loc_longitude,
			distance:distance
		};

		$.ajax({
			url: base_url+'settings/worksite/update',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				if(result.success == 0){
					notificationError('Error',result.message);
				}else{
					$('#editWorkSiteModal').modal('toggle');
					notificationSuccess('Success',result.message);
					gen_workSiteTable('');
				}
			}
		});

	});

	$('#delWorkSiteBtn').click(function(){

		var workSiteId = $('#edit_worksiteid').val();

		var data = {
			id:workSiteId
		};

		$.ajax({
			url: base_url+'settings/worksite/destroy',
			type:'POST',
			data:data,
			success:function(data) {

				var result = JSON.parse(data);
				$('#delWorkSiteModal').modal('toggle');
				notificationSuccess('Success',result.message);
				gen_workSiteTable('');
			}
		});

	});

	$(document).on('click', '.btn_copy', function(){
		var link = $(this).data('link');
		link.select();
		document.execCommand("copy");
	});

	$(document).on('click', '.btn_edit', function(){
		var uid = $(this).data('uid');
		var desc = $(this).data('desc');
		var loc = $(this).data('loc');
		var lat = $(this).data('lat');
		var lng = $(this).data('lng');
		var dist = $(this).data('dist');

		$('#editWorkSiteDesc').val(desc);
		$('#currentWorkSiteDesc').val(desc);
		$('#editDistance').val(dist);
		$('.worksiteid').val(uid);
		$('#edit_pac-input').val(loc);
		$('#edit_loc_latitude').val(loc);
		$('#edit_loc_longitude').val(lng);

		initMap2();

		$('#editWorkSiteModal').modal();
	});

	$(document).on('click', '.btn_delete', function(){
		var desc = $(this).data('desc');
		var delid = $(this).data('delid');

		$('.info_desc').text(desc);
		$('#edit_worksiteid').val(delid);

		$('#delWorkSiteModal').modal();

	});

	$(document).on('click', '#btnSearchButton', function(){
		var search = $('.searchArea').val();

		gen_workSiteTable(search);
	});


});
