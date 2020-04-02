$(function(){
var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$("#btnShipping").prop('disabled',true);
	$("#isActive").val(1);
	$("#notes").prop('disabled',true);

	var sono_id = $("#sono_id").val();

	drItems = [];
	grandtotal = 0;

	// get all items of the sales order
	// gathered data will be stored in drItems array and the will be used to populate the datatable
	$.ajax({
  		type: 'post',
  		url: base_url + 'sales/Sales_drconvert/getDrItems',
  		data:{ 'sono_id':sono_id },
  		beforeSend:function(data) {
  			$.LoadingOverlay("show"); 
		},
  		success:function(data) {
  			$.each(eval(data), function(key, value){
  				data = {
					itemid: value.itemid,
					itemname: value.itemname,
					qty: value.qty,
					releaseqty: 0,
					diffqty: parseFloat(value.qty) - 0,
					uomid: value.uomid,
					unit: value.unit,
					price: value.price,
					discamt: value.discamt,
					disctype: value.discount_type,
					subtotal: value.total,
					total: parseFloat(value.price) * 0
				}
				
				drItems.push(data);
			});
			populateTable();
			updateTotal();
			$(".btnManualItem").prop('disabled',true);
			$.LoadingOverlay("hide");
  		}
  	});

	// initialize datatable
  	var table = $('#table-grid').DataTable({ //declaring of table
  		destroy: true,
        columnDefs: [{ targets: [12], visible: true, orderable: false, sClass: 'text-center'}],
        columnDefs: [{ targets: [0], sClass: 'td_id'}],
        columnDefs: [{ "targets": [ 0 ], "visible": false, "searchable": false }, { "targets": [ 5 ], "visible": false }, { "targets": [ 9 ], "visible": false }, { "targets": [ 10 ], "visible": false }]
    });//data table

  	// function for binding and refreshing datatable data
    function populateTable(){
    	table.clear();
    	for(var a = 0; a < drItems.length; a++){
    		if (drItems[a].disctype == 2) {
    			discount = drItems[a].discamt + "%";
    		}
    		else {
    			discount = accounting.formatMoney(drItems[a].discamt)
    		}
			selectedDataarray = [
                drItems[a].itemid,
                drItems[a].itemname.toUpperCase(),
                accounting.formatMoney(drItems[a].qty),
                accounting.formatMoney(drItems[a].releaseqty),
                accounting.formatMoney(drItems[a].diffqty),
                drItems[a].uomid,
                drItems[a].unit,
                accounting.formatMoney(drItems[a].price),
                discount,
                drItems[a].disctype,
                accounting.formatMoney(drItems[a].subtotal),
                accounting.formatMoney(drItems[a].total),
                "<center><button class='btn btn-success btnManualItem' data-toggle='modal' data-value='" + a + "' data-backdrop='static' data-keyboard='false' data-target='#editdrModal'>Release</button></center>"
            ];// adding selected data to array 

        	table.row.add(selectedDataarray);   
		}        
        table.draw();
    }

    // function for updating total
    // mostly called when changing the release quantity and shipping amount
    function updateTotal() {
		grandTotal = 0;
		gendiscount = $("#gendiscount").val();
		gendisctype = $("#gendiscounttype").val();
		shippingamt = $("#shippingamt").val();

		$.each(drItems, function(index, value) { 
		    var total = parseFloat(value.total);
	        grandTotal += total;
		});
		$("#totalamt").val(grandTotal);

		if (gendisctype == 2) {
			grandDiscount = parseFloat(grandTotal) * (parseFloat(gendiscount) / 100);
		}
		else {
			grandDiscount = gendiscount;
		}

		discountedGrandTotal = (parseFloat(grandTotal) - parseFloat(grandDiscount)) + parseFloat(shippingamt);

		if (discountedGrandTotal <= 0) {
			discountedGrandTotal = 0;
		}

		if (grandTotal > 0) {
			$(".btnDeliveryComfirm").prop('disabled',false);
		}
		else {
			$(".btnDeliveryComfirm").prop('disabled',true);
		}

		$(".btnGrandtotal").html("TOTAL: " + formatMoney(discountedGrandTotal));
	}

	// manual release of a specific item in the datatable
	$('#table-grid').delegate(".btnManualItem", "click", function(){
		var i = $(this).data('value');
		$( "#releaseqty" ).val(drItems[i].qty);
		$( "#releaseqty" ).attr({"max" : drItems[i].qty});
		$("#invname").val(drItems[i].itemname);
		$("#itemid_value").val(drItems[i].itemid);
	});

	// increasing or decreasing the number of release quantity
	$(".btnManualRelease").click(function(e){
		releaseqty = $( "#releaseqty" ).val();
		itemid = $( "#itemid_value" ).val();
		itemname = $( "#invname" ).val();

		if (releaseqty == "") {
			$.toast({
			    heading: 'Note:',
			    text: "No record found. Please input release quantity.",
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
		else {
			for(var a = 0; a < drItems.length; a++){
				if (drItems[a].itemid == itemid) {
					diffqty = parseFloat(drItems[a].qty) - parseFloat(releaseqty);
					total = parseFloat(drItems[a].price) * parseFloat(releaseqty);
					discount = 0;

					if (drItems[a].disctype == 2) {
						discount = parseFloat(total) * (parseFloat(drItems[a].discamt) / 100);
					}
					else {
						discount = drItems[a].discamt;
					}

					discountedTotal = parseFloat(total) - parseFloat(discount);

					if (discountedTotal < 0) {
						discountedTotal = 0;
					}

					data = {
						itemid: itemid,
						itemname: itemname,
						qty: drItems[a].qty,
						releaseqty: releaseqty,
						diffqty: diffqty,
						uomid: drItems[a].uomid,
						unit: drItems[a].unit,
						price: drItems[a].price,
						discamt: drItems[a].discamt,
						disctype: drItems[a].disctype,
						subtotal: drItems[a].total,
						total: discountedTotal
					}

					drItems.splice(a, 1);
					drItems.push(data);
				}
			}

			populateTable();
			updateTotal();
		}
	});

	// user cannot input release quantity higher than the SO Quantity or lower than 0
	$('#releaseqty, #bc-releaseqty').on('keydown keyup', function(e){
        if ($(this).val() > parseInt($(this).attr('max')) 
            && e.keyCode !== 46 // keycode for delete
            && e.keyCode !== 8 // keycode for backspace
           	) {
           	e.preventDefault();
           	$(this).val($(this).attr('max'));
        }
    });

	// clears the shipping amount input field
	$("#btnShipping").click(function(e){
		$("#shipping").val($("#shippingamt").val());
	});
	
	// update the shipping amount and grand total
	$(".btnassignShip").click(function(e){
		e.preventDefault();

		$('#code-scan').focus();

		var shipamt = $("#shipping").val();

		if(shipamt == "") {
			shipamt = 0;
		}

		$("#shippingamt").val(shipamt);
		$(".btnShipping").text("Shipping : " + formatMoney(shipamt,2, ".", ","));
		updateTotal();
	});
	//end

	$( ".btnConvert" ).click(function(e){
		e.preventDefault();

		if (grandTotal == 0) {
			$.toast({
			    heading: 'Note:',
			    text: "No record found. Atleast one release is needed.",
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
		else {
			$.ajax({
		  		type: 'post',
		  		url: base_url + 'sales/Sales_drconvert/save_item_releaseDetails',
		  		data:{
		  			'sono_id':sono_id, 
		  			'drItems': drItems,
		  		  	'idno': $("#idno_id").val(),
		  		   	'locid': $("#location_id").val(),
		  		    'shippingid': $("#shipping_id").val(),
		  			'sales_date': formatDate($(".sales_date").val()),
		  			'totalamt': grandTotal,
		  			'notes': $("#notes").val(),
		  			'gendiscount': $("#gendiscount").val(),
					'gendisctype': $("#gendiscounttype").val(),
					'shippingamt': $("#shippingamt").val()
		  		},
		  		beforeSend:function(data) {
					$(".cancelBtn").prop('disabled', true); 
					$(".btnDeliveryComfirm").text("Please wait...");
					$(".btnConvert").prop('disabled', true); 
  					$.LoadingOverlay("show"); 
				},
		  		success:function(data) {
		  			$.LoadingOverlay("hide"); 
		  			if (data.success == 1) {
		  				$(".btnDeliveryComfirm").prop('disabled', true); 
		  				$(".btnDeliveryComfirm").text("Converted DR");
							$.toast({
							    heading: 'Success',
							    text: "DR #"+ data.drno +" successfully added for release.",
							    icon: 'success',
							    loader: false,  
							    stack: false,
							    position: 'top-center', 
							    bgColor: '#5cb85c',
								textColor: 'white',
								allowToastClose: false,
								hideAfter: 10000,
							});
						//dataTable.draw();
						window.setTimeout(function() {
							window.location.href = base_url+"Main_sales/sales_dr/" + $("#token").val();
						},500)
						$(".btnConvert").prop('disabled', true); 
		  			}
		  			else {
		  				$(".btnConvert").prop('disabled', false);
		  			}
		  		}

		  	});
		}
	});

	// Barcode Functions
	$('#code-scan').codeScanner({
		onScan: function ($element, code) {
			$.ajax({
				type: 'post',
				url: base_url + 'sales/Sales_drconvert/getItemDetailsByBarcode',
				data:{ 'barcode': code, 'sono': sono_id },
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
			  	},
				success:function(data) {
					if (data) {
						console.log(data);
						$('#code-scan').val(data.barcode);
						$('#bc-invname').val(data.itemname);
						$('#bc-itemid_value').val(data.itemid);
						$("#bc-releaseqty").prop('disabled', false);
						$("#bc-releaseqty").val(data.qty);
						$("#bc-releaseqty").attr({"max" : data.qty});
					}
					else {
						$('#code-scan').val(code);
						$("#bc-releaseqty").prop('disabled', true);
						$("#bc-releaseqty").val("");
						$('#bc-invname').val("");
						$('#bc-itemid_value').val("");
						$.toast({
							heading: 'Note:',
							text: "Item is not part of Delivery Receipt.",
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
						
					$.LoadingOverlay("hide");
				}

			});
		}
	});

	$(".btnBarcodeRelease").click(function(e){
		releaseqty = $( "#bc-releaseqty" ).val();
		itemid = $( "#bc-itemid_value" ).val();
		itemname = $( "#bc-invname" ).val();

		if (releaseqty == "") {
			$.toast({
			    heading: 'Note:',
			    text: "No record found. Please input release quantity.",
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
		else {
			for(var a = 0; a < drItems.length; a++){
				if (drItems[a].itemid == itemid) {
					diffqty = parseFloat(drItems[a].qty) - parseFloat(releaseqty);
					total = parseFloat(drItems[a].price) * parseFloat(releaseqty);
					discount = 0;

					if (drItems[a].disctype == 2) {
						discount = parseFloat(total) * (parseFloat(drItems[a].discamt) / 100);
					}
					else {
						discount = drItems[a].discamt;
					}

					discountedTotal = parseFloat(total) - parseFloat(discount);

					if (discountedTotal < 0) {
						discountedTotal = 0;
					}

					data = {
						itemid: itemid,
						itemname: itemname,
						qty: drItems[a].qty,
						releaseqty: releaseqty,
						diffqty: diffqty,
						uomid: drItems[a].uomid,
						unit: drItems[a].unit,
						price: drItems[a].price,
						discamt: drItems[a].discamt,
						disctype: drItems[a].disctype,
						subtotal: drItems[a].total,
						total: discountedTotal
					}

					drItems.splice(a, 1);
					drItems.push(data);
				}
			}

			populateTable();
			updateTotal();
		}
	});
	
	$(".btnActiveBarcode").click(function(e) {
		e.preventDefault();
		$("#btnShipping").prop('disabled',true); 
		$(".btnManualItem").prop('disabled',true);
		$("#isActive").val(1);
		$("#notes").prop('disabled',true);
		$(".btnManualItem").prop('disabled',true);
		$("#barcodeModal").modal("toggle");
		$("#code-scan").focus();

		$(".btnActiveBarcode").prop("class","btn btn-success btnActiveBarcode");
		$(".btnActiveManual").prop("class","btn btn-secondary btnActiveManual");
	});

	$(".btnActiveManual").click(function(e) {
		e.preventDefault();
		$("#btnShipping").prop('disabled',false); 
		$(".btnManualItem").prop('disabled',false); 
		$("#isActive").val(0);
		$("#notes").prop('disabled',false);

		$(".btnActiveManual").prop("class","btn btn-success btnActiveManual");
		$(".btnActiveBarcode").prop("class","btn btn-secondary btnActiveBarcode");
	});

	//allowing numeric with decimal 
    $(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    //allowing numeric without decimal 
    $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
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
	document.getElementById('btnShipping').innerText = "Shipping: " + formatMoney(shippingcharge, 2, ".", ",");
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
    
    if (shippingcharge == "") {
      	document.getElementById("shippingcharge").style.border='1px solid red';
      	checker=2;
    }
    else {
      	if(parseFloat(shippingcharge) || shippingcharge==0) {
          	document.getElementById("shippingcharge").style.border='1px solid #c8c8c8';
      	}
      	else {
          	document.getElementById("shippingcharge").style.border='1px solid red';
          	checker=2;
      	}
    }
    
    if (checker==1) {
        document.getElementById('shippingcharge').value = "";
        var newtotal = (total*1)-(shipping*1)+(shippingcharge*1);
        document.getElementById('shippingBtn').innerText = "Shipping: " + formatMoney(shippingcharge,2, ".", ",");
        document.getElementById('totalBtn').innerText = "Total: " + formatMoney(newtotal,2, ".", ",");
        document.getElementById('shippingcharge').value = shippingcharge;
    }
    else {
        alert("ERROR: Please make sure all values entered are correct.");
    }
    
    return 1;
}

function ClearFieldsshipping() {
	$("#shipping").val("");
}

function dispalyNotif(rowcount) {
	var totalcount = $("#release0").val();
	if(totalcount > 0) {
		$('#NotifInvModal').modal({show: true});
	}
	else {
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