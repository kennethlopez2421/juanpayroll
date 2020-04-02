$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$('.datepicker2').datepicker({
		format: 'yyyy/mm/dd',
    	startDate: '0',
	});

	var fis_app_id =  $(".fis_app_id").val();
	console.log(fis_app_id);

	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
        "columnDefs": [
            { targets: 3, orderable: false, "sClass":"text-center"}
        ],
		"ajax":{
			url :base_url+"Main_entity/lsf_transaction_history", // json datasource
			data: {'fis_app_id' :fis_app_id},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});

	$('.convertToCustomerBtn').click(function(e){
        e.preventDefault();
        var fis_app_id = $(this).data('value');

        $.ajax({ 
            type: 'post',
            url: base_url+'Main_entity/convert_to_customer',
            data: {'fis_app_id':fis_app_id},
            beforeSend:function(data){
            	$('.convertToCustomerBtn').prop('disabled',true);
                $(this).html('Please wait...');
            },
            success:function(data){
               $('.convertToCustomerBtn').html('Converted to Customer');
                $.toast({
                    heading: 'Success',
                    text: data.message,
                    icon: 'success',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    bgColor: '#5cb85c',
                    textColor: 'white',
                    allowToastClose: false,
                    hideAfter: 10000
                });
                
            }
        });
    });

	
});