$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var poretno = $("#poretno").data("poretno");
	var supid = $("#supid").data("supid");
    var token = $("#token").val();
	
	// reuseable toast call function for easeness and shorter code
	function toastMessage(heading, text, icon, bgcolor) {
		// #5cb85c success
		// #f0ad4e error
		$.toast({
			heading: heading,
			text: text,
			icon: icon,
			loader: false,  
			stack: false,
			position: 'top-center', 
			allowToastClose: false,
			bgColor: bgcolor,
			textColor: 'white'  
		});
	}

	var dataTable = $('#table-grid').DataTable({
		"serverSide": true,
		"destroy":true,
		"ajax":{
			url :base_url+"purchase/PR_allocate/table_poreturn_allocate", // json datasource
			type: "post",  // method  , by default get
			data: {"poretno" : poretno, "supid" : supid},
            beforeSend : function() {
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
		},
	});

    dataTable.destroy();
    
    $("#table-grid").delegate("#allocamt", "keyup", function(e){
        if (parseFloat($(this).val()) > parseFloat($("#retbalance").val())) {
            toastMessage('Note', 'Allocated amount is exceeds the unpaid amount.', 'error', '#f0ad4e');
            $(this).val(0);
        }
    });

	$("#allocateBtn").on("click", function(){
        allocValues = [];

        $( ".allocamt" ).each(function( index ) {
            entry = {
                difference: $(this).data("difference"),
                apvtrandate: $(this).data("apvtrandate"),
                apvno: $(this).data("apvno"),
                allocamt: $(this).val()
            }

            allocValues.push(entry);
        });

        retbalance = $("#retbalance").val();

        $.ajax({
            type:'post',
            url:base_url+'purchase/PR_allocate/tbl_purchasereturnallocate_save',
            data: {"retbalance" : retbalance, "poretno" : poretno, "supid" : supid, "allocValues" : allocValues},
            beforeSend:function(data){
                $('#allocateBtn').prop('disabled', true);
            },
            success:function(data){
                if (data.success == 1) {
                    $.LoadingOverlay("show"); 
                    toastMessage('Success', 'Purchase Return Allocation has been successfully updated.', 'success', '#5cb85c');
                    window.setTimeout(function(){
				        window.open(base_url+"Main_purchase/return_summary/" + token, '_self');
                    },1500);
                }
                else {
                    toastMessage('Note', 'Purchase Return Allocation failed.', 'error', '#f0ad4e');
                }
            }
        });
    });

});


function amountAllocate(count, unpaidamount) {
    var fieldID = 'poret'+count;
    var fieldrun = 'poret';
    var retbalance = document.getElementById("retbalance").value;
    var grandtotal = document.getElementById("grandtotal").value;
    var recAmount = document.getElementById(fieldID).value;
    var diff=0;
   
    checker=1;
    if (recAmount == "") {
        document.getElementById(fieldID).value=0;
    }
    else {
        if(parseFloat(recAmount) || (recAmount==0)) {
            if (recAmount>unpaidamount) {
                checker = 3;
            }
        }
        else {
            document.getElementById(fieldID).value=0;
        }
    }
   
    if (checker == 1) {
        var totalamt = 0;
        for(var a = 1; a <= grandtotal; a++) {
            fieldrun = 'poret'+a;
            var val = document.getElementById(fieldrun).value;
            totalamt = (totalamt * 1) + (val * 1);
        }
        diff = (retbalance*1)-(totalamt*1);
        
        if (diff >= 0) {
            if(totalamt == 0) {
                document.getElementById("allocateBtn").disabled = true;
            }
            else {
                document.getElementById("allocateBtn").disabled = false;
            }
            
            totalamt = formatMoney(totalamt); 
            setText('allocLabel',totalamt);
        }
        else {
        	$.toast({
                heading: 'Note',
                text: 'You have insufficient balance for allocation.',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                bgColor: '#f0ad4e',
                textColor: 'white',
                allowToastClose: false,
                hideAfter: 3000
            });
            document.getElementById(fieldID).value=0;
        }
    }
    else if (checker==3) {
    	$.toast({
            heading: 'Note',
            text: 'Allocated amount is exceeds the unpaid amount.',
            icon: 'error',
            loader: false,  
            stack: false,
            position: 'top-center', 
            bgColor: '#f0ad4e',
            textColor: 'white',
            allowToastClose: false,
            hideAfter: 3000
        });
        document.getElementById(fieldID).value=0;
    }
    else {
        $.toast({
            heading: 'Note',
            text: 'Please make sure all values entered are correct.',
            icon: 'error',
            loader: false,  
            stack: false,
            position: 'top-center', 
            bgColor: '#f0ad4e',
            textColor: 'white',
            allowToastClose: false,
            hideAfter: 3000
        });
        document.getElementById(fieldID).value=0;
    }
}

//set label text
function setText(id, txt) {
    var elem;
    
    if( document.getElementById  && (elem=document.getElementById(id)) ) {
        if( !elem.firstChild )
            elem.appendChild( document.createTextNode( txt ) );
        else
            elem.firstChild.data = txt;
    }
    
    return false;
}
