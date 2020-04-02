$(document).ready(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$('.dividno').show('slow');
    $("#divsearchfilter").change(function() {
        var searchtype = $('#divsearchfilter').val();

		if(searchtype == "divitemcode") {
			$('#divitemcode').show('slow');
			$('#divname').hide('slow');
			$('#divbarcode').hide('slow');
			$("#nameSearch").val("");
			$("#idnosearch").val("");
			$("#barcodeSearch").val("");
		}
		else if(searchtype == "divname") {
			$('#divname').show('slow');
			$('#divitemcode').hide('slow');   
			$('#divbarcode').hide('slow'); 
			$("#nameSearch").val("");
			$("#idnosearch").val("");
			$("#barcodeSearch").val("");
		}
		else if(searchtype == "divbarcode") {
			$('#divbarcode').show('slow');
			$('#divname').hide('slow');
			$('#divitemcode').hide('slow');
			$("#nameSearch").val("");
			$("#idnosearch").val("");
			$("#barcodeSearch").val("");
		}
	});
	
	$("#barcode").on("focusin", function(){
		$("#btnSave").attr("disabled", true);
	});
	
	$("#barcode").on("focusout", function(){
		$("#btnSave").attr("disabled", false);
	});

	function fillDatatable(search, name, itemcode, barcode) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"processing": true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 4, orderable: false, "sClass":"text-center" }
			],
			"ajax":{
				url:base_url+"inventory/Inv_inventorylist/inventory_table", // json datasource
				type: "post",  // method  , by default get,
				data:{'search':search, 'name':name, 'itemcode':itemcode, 'barcode':barcode},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	fillDatatable("", "", "", "");

    //start
	$(".searchBtn").click(function(e){
		e.preventDefault();
		var search	 	= $('#divsearchfilter').val();
		var itemcode 	= $("#idnosearch").val();
		var name 		= $("#nameSearch").val();
		var barcode 	= $("#barcodeSearch").val();
		
		if(search == "divitemcode") {
			if(itemcode != "") {
				fillDatatable(search, name, itemcode, barcode);
			}
			else {
				toastMessage('Note', 'No id number to be search. Please fill in data.', 'error');
			}
		}
		else if(search == "divname") {
			if(nameSearch != "") {
				fillDatatable(search, name, itemcode, barcode);
			}
			else {
				toastMessage('Note', 'No name to be search. Please fill in data.', 'error');
			}
		}
		else if (search == "divbarcode") {

		}


	});

	$('#add_item_btn').click(function(e){
		$('#add_inventory_form')[0].reset();
		$('#addItemModal').modal();
	});

	$('#table-grid').delegate(".btnView", "click", function(){
	  	var id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_inventory/get_item',
	  		data:{ 'id':id },
	  		success:function(data) {
	  			$('#update_inventory_form')[0].reset();

	  			data = JSON.parse(data);

	  			$('#update_id').val(data.id);
	  			$('#update_item_code').val(data.id);
	  			$('#update_inventory_name').val(data.itemname);
				$('#update_inventory_category').val(data.catid).change();
				$('#update_unit').val(data.uomid).change();
				$('#update_reorder_point').val(data.reorderpt);
				$('#update_reorder_value').val(data.reorderval);
				$('#update_gl_account').val(data.acctid).change();
				$('#update_barcode').val(data.barcode);
				$('#update_other_info').val(data.otherinfo);

				if (data.isforsale == "1") { $('#update_is_for_sale').prop("checked", true); }
				if (data.webforsale == "1") { $('#update_web_for_sale').prop("checked", true); }
				if (data.trackinventorycount == "1") { $('#update_track_inventory_count').prop("checked", true); }
				if (data.isportal == "1") { $('#update_is_portal_for_sale').prop("checked", true); }
				if (data.isarchive == "1") { $('#update_is_archive').prop("checked", true); }

				$('#updateItemModal').modal();
	  		},
	  		error: function(error){
				toastMessage('Note', 'Something went wrong. Please try again.', 'error');
	  		}
	  	});
	});

	$('#add_inventory_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			data: form.serialize(),
		}).done(function(response) {
			var response = JSON.parse(response);

			if(response.success===false) {
				toastMessage('Note', response.message, 'error');
			}
			else {
				fillDatatable("", "", "", "");
				$('#addItemModal').modal('hide');
				$('#add_inventory_form')[0].reset();
				toastMessage('Success', response.message, 'success');
			}
		});
	});

	$('#update_inventory_form').submit(function(event){
		event.preventDefault();

		var form = $(this);
		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			data: form.serialize(),
		}).done(function(response) {
			var response = JSON.parse(response);

			if(response.success===false) {
				toastMessage('Note', response.message, 'error');
			}
			else {
				fillDatatable("", "", "", "");
				$('#updateItemModal').modal('hide');
				$('#update_inventory_form')[0].reset();
				toastMessage('Success', response.message, 'success');
			}
		});
	});

	$('#table-grid').delegate(".btnDelete","click", function(){
		var id = $(this).data('value');
		var itemname = $(this).data('itemname');

		$('#del_item_id').val(id);
		$('#info_desc').html(itemname);
	  	$('#deleteItemModal').modal();
	});

	$('#delete_item_form').submit(function(event){
		event.preventDefault();

		var form = $(this);

		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			data: form.serialize(),
		}).done(function(response) {
			var response = JSON.parse(response);

			if(response.success===false) {
				toastMessage('Note', response.message, 'error');
			}
			else {
				fillDatatable("", "", "", "");
				toastMessage('Note', response.message, 'success');
				$('#deleteItemModal').modal('hide');	
			}
		});
	});
});