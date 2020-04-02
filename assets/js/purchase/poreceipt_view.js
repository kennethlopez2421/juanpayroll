$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var pono = $("#pono_id_sec").data("pono");
	var idno = $(".idno").val();
	var token = $(".token").val();


	var dataTable = $('#table-grid').DataTable({	
		"serverSide": true,
		//"destroy":true,
		 "columnDefs": [
		    { "visible": false, "targets": 0 },
		    { "visible": false, "targets": 5 },
		    { "visible": false, "targets": 6 },
		    { "visible": false, "targets": 7 },
		    { "visible": false, "targets": 8 }
		  ],

		"ajax":{
			url :base_url+"Main_purchase/table_poreceipt_view", // json datasource
			type: "post",  // method  , by default get
			data: {"pono" : pono},
			
			beforeSend:function(data)
            {
                $("body").LoadingOverlay("show"); 
            },
            complete: function()
            {
                $("body").LoadingOverlay("hide"); 
            },
			error: function(){  // error handling
				$(".table-grid-error").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		},
	});
	
	$('.search-input-text').on('keyup click', function(){   // for text boxes
		var i =$(this).attr('data-column');  // getting column index
		var v =$(this).val();  // getting search input value
		dataTable.columns(i).search(v).draw();
	});

	$('.search-input-select').on('change', function(){   // for select box
		var i =$(this).attr('data-column');  
		var v =$(this).val();  
		dataTable.columns(i).search(v).draw();
	});

	orderItems = [];
	  $(".approvePOReceiptBtn").click(function(e){
        e.preventDefault();

        var token = $("#token").val();    
        var count = $("#tdata").val(); // validation    
        var supplierid = $("#supplierid").val();  
        var warehouseid = $("#warehouseid").val();   
        var trandate = $("#trandate").val();   

        var gen_discount = $("#gen_discount").val();
        var discount_type = $("#discount_type").val();      

		//start - to get total amount in the specific column
        var data_totalamt = dataTable.rows().columns(7).data();

        data_totalamt.each(function(value, index){ 
            grand_total = eval(value.join("+").replace(/,/g, ''));
        });

        var data = dataTable.rows().data();
	    data.each(function (value, index) {
	        entry = {
	            itemid: value[0],
	            qty: value[2].replace(/,/g, ''), //remove ' , ' comma to work properly
	            uomid: value[3],	            
	            rqty: value[4].replace(/,/g, ''), //remove ' , ' comma to work properly
	            price: value[5],
	            uomid: value[6],
	            damount: value[7],
	            dtype: value[8],	  
	        }
	        orderItems.push(entry);
	    });
 

            $.ajax({
                type:'post',
                url:base_url+'Main_purchase/convert_poreceipt',
                data:{
                "orderItems":orderItems,
                "orderTotal":grand_total,
                "pono": pono,
                "count" : count,
                "supplierid": supplierid,
                "warehouseid": warehouseid,
                "trandate": trandate,
                "gen_discount":gen_discount,
                "discount_type":discount_type
                },
             //    beforeSend:function(data)
	            // {
	            //     $("body").LoadingOverlay("show"); 
	            // },
	            // complete: function()
	            // {
	            //     $("body").LoadingOverlay("hide"); 
	            // },
                success:function(data){
                if(data.success == 1)
                    {     
					$.toast({
                        heading: 'Success',
                        text: 'Purchase Order has successfully been converted to PO Receipt.',
                        icon: 'success',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        bgColor: '#5cb85c',
                        textColor: 'white',
                        allowToastClose: false,
                        hideAfter: 3000
                    });
                   
                    } else{
                        $.toast({
                            heading: 'Note',
                            text: 'No record found.',
                            icon: 'error',
                            loader: false,  
                            stack: false,
                            position: 'top-center', 
                            bgColor: '#f0ad4e',
                            textColor: 'white',
                            allowToastClose: false,
                            hideAfter: 3000
                        });

                    	}
                //     window.setTimeout(function(){
	               //       window.location.href=base_url+"Main_purchase/poreceipt_summary/" + token;
	             	 // })   
                }   
            });    
		});
});

