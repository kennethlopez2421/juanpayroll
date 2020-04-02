$(function(){
var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$("#btnShipping").prop('disabled',true); 
	$(".btnManualItem").prop('disabled',true);
	$("#isActive").val(1);
	$("#notes").prop('disabled',true);

	var drno_id = $("#drno_id").val();
	var dataTable = $('#table-grid').DataTable({
		"destroy": true,
		
		"serverSide": true,
		"order": [[ 2, "desc" ]],
		"ajax":{
			type: "post", 
			url :base_url+"sales/Sales_siconvert/so_item_invoiceConvertDetails", // json datasource
			data:{'drno_id':drno_id},
			beforeSend:function(data)
			{
				$.LoadingOverlay("show"); 
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			},
			complete: function()
			{
					$.LoadingOverlay("hide"); 
			},
		}
	});
	
	//dataTable.destroy();

	//start
	$(".btnConvert").click(function(e){
		e.preventDefault();
		var checker = 0;
		var drno_id = $("#drno_id").val();
		var token = $("#token").val();
		if(drno_id > 0) { 
			$.ajax({
		  		type: 'post',
		  		url: base_url+'sales/Sales_siconvert/save_item_saleinvoiceDetails',
		  		data:{'drno_id':drno_id
		  		},
		  		beforeSend:function(data) {
					$.LoadingOverlay("show");
					$(".cancelBtn").prop('disabled', true); 
					$(".btnDeliveryComfirm").prop('disabled', true); 
					$(".btnDeliveryComfirm").text("Please wait...");
				},
		  		success:function(data) {
		  			if (data.success == 1) {
		  				$(".btnDeliveryComfirm").prop('disabled', true); 
		  				$(".btnDeliveryComfirm").text("Converted SO");

						$.toast({
						    heading: 'Success',
						    text: "DR #"+ data.drno +" has been successfully converted to sales invoice.",
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 10000,
						});

						dataTable.draw();

						window.setTimeout(function(){
							window.location.href = base_url+"Main_sales/sales_invoice/" + token;
						}, 500);
		  			}
		  			else {
		  				$(".btnDeliveryComfirm").prop('disabled', false); 
		  			}
					$.LoadingOverlay("hide");
		  		}

		  	});
		}
		else {
			$.toast({
			    heading: 'Note',
			    text: "No record found. Please check your data.",
			    icon: 'info',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#FFA500',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 5000          
			});
		}
	});
	//end
	
	//first
	$(".btnassignShip").click(function(e){
		e.preventDefault();

		$('#code-scan').focus();
		var totalamt = $("#totalamt").val();
		var origshipamt = $("#shippingamt").val();
		var diffamt = parseFloat(totalamt) - parseFloat(origshipamt);
		$("#totalamt").val(diffamt);
		var shipamt = $("#shipping").val();
		$("#shippingamt").val(shipamt);
		if(shipamt == "") {
			shipamt = 0;
		} 
		if(totalamt == "") {
			totalamt = 0;
		} 
		
		var grandtotal = parseFloat(diffamt) + parseFloat(shipamt); 
		document.getElementById('btnShipping').innerText = "Shipping: " + formatMoney(shipamt,2, ".", ",");
		document.getElementById('btnGrandtotal').innerText = "Total: " + formatMoney(grandtotal,2, ".", ",");

	 	$("#shippingamt").val(shipamt);
	 	$("#totalamt").val(grandtotal);
	});
	//end

  	$("#releaseqty").each(function () {
        var thisJ = $(this);
       // var max = thisJ.attr("max") * 1;
        var min = thisJ.attr("min") * 1;
        var intOnly = String(thisJ.attr("intOnly")).toLowerCase() == "true";

        var test = function (str) {
            return str == "" || /* (!intOnly && str == ".") || */
                ($.isNumeric(str)  && str * 1 >= min &&
                (!intOnly || str.indexOf(".") == -1) && str.match(/^0\d/) == null);
                // commented out code would allow entries like ".7"
        };

        thisJ.keydown(function () {
            var str = thisJ.val();
            if (test(str)) thisJ.data("dwnval", str);
        });

        thisJ.keyup(function () {
            var str = thisJ.val();
            if (!test(str)) thisJ.val(thisJ.data("dwnval"));
        })
    });

	$("#shipping").each(function () {

	    var thisJ = $(this);
	   // var max = thisJ.attr("max") * 1;
	    var min = thisJ.attr("min") * 1;
	    var intOnly = String(thisJ.attr("intOnly")).toLowerCase() == "true";

	    var test = function (str) {
	        return str == "" || /* (!intOnly && str == ".") || */
	            ($.isNumeric(str)  && str * 1 >= min &&
	            (!intOnly || str.indexOf(".") == -1) && str.match(/^0\d/) == null);
	            // commented out code would allow entries like ".7"
	    };

	    thisJ.keydown(function () {
	        var str = thisJ.val();
	        if (test(str)) thisJ.data("dwnval", str);
	    });

	    thisJ.keyup(function () {
	        var str = thisJ.val();
	        if (!test(str)) thisJ.val(thisJ.data("dwnval"));
	    })
	});
});


function formatMoney(n,c, d, t) {
    c = isNaN(c = Math.abs(c)) ? 2 : c;
    d = d == undefined ? "." : d;
    t = t == undefined ? "," : t; 
    s = n < 0 ? "-" : "";
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}



function assignShipping() {
	document.getElementById('btnShipping').innerText = "Shipping: " + formatMoney(shippingcharge,2, ".", ",");
}

function updateShippingSO_r() {
    var shipping = document.getElementById('shippingBtn').innerText;
    shipping = shipping.replace('Shipping: ' ,"");
    shipping=formatCurrency(shipping);
    
    var total = document.getElementById('totalBtn').innerText ;
    total = total.replace('Total: ' ,"");
    total=formatCurrency(total);
    
    var shippingcharge = document.getElementById('shippingcharge').value; shippingcharge=shippingcharge.replace(/#/ig,"").replace(/&/ig,"").replace(/'/ig,"").replace(/"/ig,"");
    var checker=1;
    
    if (shippingcharge == "")
    {
      document.getElementById("shippingcharge").style.border='1px solid red';
      checker=2;
    }
    else
    {
      if(parseFloat(shippingcharge) || shippingcharge==0)
      {
          document.getElementById("shippingcharge").style.border='1px solid #c8c8c8';
      }
      else
      {
          document.getElementById("shippingcharge").style.border='1px solid red';
          checker=2;
      }
    }
    
    if (checker==1)
    {
        document.getElementById('shippingcharge').value = "";
        var newtotal = (total*1)-(shipping*1)+(shippingcharge*1);
        document.getElementById('shippingBtn').innerText = "Shipping: " + formatMoney(shippingcharge,2, ".", ",");
        document.getElementById('totalBtn').innerText = "Total: " + formatMoney(newtotal,2, ".", ",");
        document.getElementById('shippingcharge').value = shippingcharge;
    }
    else
    {
        alert("ERROR: Please make sure all values entered are correct.");
    }
    
    return 1;
}


function ClearFieldsshipping()
{
	$("#shipping").val("");
}

function dispalyNotif(rowcount) {
	var totalcount = $("#release0").val();
	if(totalcount > 0)
	{
		$('#NotifInvModal').modal({show: true});
	}
	else
	{
		$.toast({
		    heading: 'Note',
		    text: "No record found. Please check your data.",
		    icon: 'error',
		    loader: false,   
		    stack: false,
		    position: 'top-center',  
		    bgColor: '#FFA500',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 5000          
		});
	}
}

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function isNumberKeyOnly(evt) {    
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	return true;
}