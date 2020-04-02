$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.btnregCancel').click(function(e){
		e.preventDefault();
		window.location.href = ''+base_url+'/Main/index'+'';
	});

	href="<?=base_url('Main/index');?>"
	var token = $(".page-header").data("token");
	var fis_app_id = $(".page-header").data("id");

	var formData = new FormData();

    $("#btnReg").click(function(e){
        e.preventDefault();
       
        var error = 0;
        $('.required_fields').each(function(){ //loop all input field then validate
            if ($(this).val() == ""){
                $(this).css("border-color", "#d9534f"); //change all empty to color red
            }else{
                $(this).css("border-color", "#eee");  //rollback when not empty
            }
        });

        $('.required_fields').each(function(){ //loop all input field then validate
            if ($(this).val() == ""){ // if empty show error
                error = 1; //update error to 1
                // $(this).css("border-color","#d9534f");
                $(this).focus();
                $.toast({
                    heading: 'Warning',
                    text: 'Please fill out this field',
                    icon: 'warning',
                    loader: false,   
                    stack: false,
                    position: 'top-center',     
                    bgColor: '#f0ad4e;',
                    textColor: 'white'
                });
                return false; //focus first empty fields
            }
        });

        var warning_message  = "";
        $("input[name='lsf_status']").each(function(){
            if(this.checked){
                var status = ""
                if(this.value == 1){
                    status = 'APPROVE';
                } else if(this.value == 2) {
                    status = 'DECLINE FOR WAIVER';
               }else{
                    status = 'DECLINE';
               }
                warning_message += "You are trying to set the status of this Location Study to <strong>" + status + '</strong>. Please note that applicant will be notified thru email and you will no longer be able to change the status once you click on the "Confirm" button. '                
                return false;
            }
        });

        if(error == 0){
            var hasRequirement = $('input[type=file]').val(); 
            if (hasRequirement == "") {
                $.toast({
                    heading: 'Warning',
                    text: "Please upload location images",
                    icon: 'warning',
                    loader: false,   
                    stack: false,
                    position: 'top-center',  
                    bgColor: '#f0ad4e',
                    textColor: 'white'        
                });
            }else{
                $("#warning_message").html(warning_message);
                $('#confirmModal').modal('show');
            }
        }
        
    });

	$(".confirmBtn").click(function(e){
		e.preventDefault();
		var serial = $("#location_study_form").serialize();
		var hasRequirement = $('input[type=file]').val(); 
		if (hasRequirement == "") {
		    $.toast({
			    heading: 'Warning',
			    text: "Please upload location images",
			    icon: 'warning',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#f0ad4e',
				textColor: 'white'        
			});
		}else{

            var error = 0;
            $('.required_fields').each(function(){ //loop all input field then validate
                if ($(this).val() == ""){
                    $(this).css("border-color", "#d9534f"); //change all empty to color red
                }else{
                    $(this).css("border-color", "#eee");  //rollback when not empty
                }
            });

            $('.required_fields').each(function(){ //loop all input field then validate
                if ($(this).val() == ""){ // if empty show error
                    error = 1; //update error to 1
                    // $(this).css("border-color","#d9534f");
                    $(this).focus();
                    $.toast({
                        heading: 'Warning',
                        text: 'Please fill out this field',
                        icon: 'warning',
                        loader: false,   
                        stack: false,
                        position: 'top-center',     
                        bgColor: '#f0ad4e;',
                        textColor: 'white'
                    });

                    return false; //focus first empty fields
                }
            });

            //adding qr code
            var reference_no = getReferenceNo(9);
            var qrcode_text ='';
            qrcode_text += reference_no;
            $('#qrcode').qrcode({
                width: 128,
                height: 128,
                text: qrcode_text
            });
            var canvas = $('#qrcode canvas');
            var blob = canvas.get(0).toDataURL("image/png");
            console.log(qrcode_text);
            console.log("ref_no"+reference_no);
            formData.append('reference_no',reference_no);
            formData.append('qr_code',blob);

            if(error == 0){

                var serial = $("#location_study_form").serializeArray();
                for(var i = 0; i < serial.length; i++){
                    var formDataItem = serial[i];
                    if(formDataItem.value != ""){
                        formData.append(formDataItem.name, formDataItem.value);
                    }
                }

                var fileInputs = $('.req_upload');
  
                $.each(fileInputs, function(i,fileInput){
                    if( fileInput.files.length > 0 ){
                        $.each(fileInput.files, function(k,file){
                            formData.append('images[]', file);
                        });
                    }
                });

                $.ajax({
                    method: 'post',
                    url: base_url+'Main_entity/save_location_study_form',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType:false,
                    beforeSend:function(data){
                        $('.btnReg').attr('disabled',true);
                        $('.btnReg').text('Please wait...');
                        $('.btnregCancel').attr('disabled',true);
                    },
                    success:function(data){
                        if(data.success == 1) {
                            $('.btnReg').attr('disabled',false);
                            $('.btnReg').text('Submit');
                            $(this).trigger("reset");
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
                            setTimeout(function () { 
                                window.location = base_url+'Main_entity/view_fis_transaction_history/'+fis_app_id+'/'+token;
                            }, 2 * 1000);
                        }else{
                            $('.btnReg').attr('disabled',false);
                            $('.btnReg').text('Submit');
                            $.toast({
                                heading: 'Warning',
                                text: data.message,
                                icon: 'warning',
                                loader: false,   
                                stack: false,
                                position: 'top-center',  
                                bgColor: '#f0ad4e',
                                textColor: 'white'   
                            });
                        }
                    }
                });

            }
		}

	});

	 $('#ADDFILE').click(function (event){
        event.preventDefault();
        var counter = $('.uploadFileContainer .req_upload').length;

        if(counter < 5){
            addFileInput();
        }else{
            $.toast({
                heading: 'Warning',
                text: 'You can only upload up to 5 location images',
                icon: 'warning',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: false,
                bgColor: '#f0ad4e;',
                textColor: 'white'  
            });
        }

    });

    $('#ADDASSESSMENT').click(function (event){
        event.preventDefault();
        addAssessment();
 
    });

    function addFileInput(){

        var html = '';
        html += '<div class = "alert alert-info">';
        html += '<button type = "button" class = "close" data-dismiss = "alert" aria-hidden = "true">&times;</button>';
        html += '<strong>Upload file</strong>';
        html += '<input type="file" name="images[]" class="req_upload">';
        html += '</div>';

        $(".uploadFileContainer").append(html);
    };

    function addAssessment(){

        var html = '';
        html += '<div class="col-md-12">';
        html += '<input class="form-control" rows="4" style="margin-bottom:20px" name ="final_assessment[]"></input>';
        html += '</div>';

        $(".assessment_div").append(html);
    };

    //Validated the photos to be uploaded
    $('.uploadFileContainer').delegate('.req_upload', 'change', function(e){
      
        var filesize = $(this)[0].files[0].size;
        if(!hasExtension($('.req_upload'),['.jpg', '.png','.JPG','.PNG']) || filesize > 2000000){
            $.toast({
                heading: 'Warning',
                text: 'Please select valid file to upload. Only PNG and JPG file are allowed',
                icon: 'warning',
                loader: false,  
                stack: false,
                position: 'top-center', 
                allowToastClose: false,
                bgColor: '#f0ad4e;',
                textColor: 'white'  
            });
           $(this).val("");
        }
    });

    function hasExtension(inputID, exts) {
        var fileName = inputID.val();
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }
    //Auto fill table rows
    function add_row_competitors_table(no_of_competitor){
    	
    	var html ="";
    	var noofcompetitor = no_of_competitor > 4 ? 4 : no_of_competitor;
    	for (var i = 0; i < noofcompetitor ; i++){
    		html += '<tr>';
			html += '<td><input type="text" name="competitor_'+i+'" class="form-control form-control-sm"></td>';
			html += '<td><input type="text" name="menu_'+i+'" class="form-control form-control-sm"></td>';
			html += '<td><input type="number" min="1" name="price_'+i+'" class="form-control form-control-sm"></td>';
			html += '<td><input type="number" min="1" name="highest_price_'+i+'" class="form-control form-control-sm"></td>';
			html += '<td><input type="number" min="1" id="lowest_price_'+i+'" name="lowest_price_'+i +'" class="form-control form-control-sm"></td>';
			html += '</tr>';
    	}
    	$(".competitors_row").append(html);
    }

    //Auto computation of Break Even Sales
    function compute_estimated_transaction_count(footcount_a, footcount_b,operation_days){

    	var no_of_competitor = $("#no_of_competitors").val() == "" ? 0 : $("#no_of_competitors").val();
    	if(no_of_competitor == 0){
            var lowestsrp = $("#bes_lowest_srp").val();
    		var sum = parseInt(footcount_a)+ parseInt(footcount_b);
			var expected_transaction_count = parseInt(sum / 2);
            var gross_sales = parseFloat(expected_transaction_count * lowestsrp);
    	}else{
    		var sum = parseFloat(footcount_a)+ parseFloat(footcount_b);

    		var lowest_srp = [];
    		if($("#lowest_price_0").val() > 0){
                lowest_srp.push($("#lowest_price_0").val());  		
    		}
    		if(	$("#lowest_price_1").val() > 0){
                lowest_srp.push($("#lowest_price_1").val());
    		} 
            if($("#lowest_price_2").val() > 0){
                lowest_srp.push($("#lowest_price_2").val());
            }
            if($("#lowest_price_3").val() > 0){
                lowest_srp.push($("#lowest_price_3").val());
            }
    		if($("#lowest_price_4").val() > 0){
                lowest_srp.push($("#lowest_price_4").val()); 
            } 
    		var lowestsrp = Math.min(...lowest_srp);
            var actual_gross_sales = $("#actual_gross_sales").val();

    		var estimated_transaction_count = parseInt(actual_gross_sales/lowestsrp);
            var tca = parseInt(sum * .2);
            var tcb = parseInt(estimated_transaction_count * .8);
            var tcsum = parseInt(tca) + parseInt(tcb);
			var expected_transaction_count = parseInt(tcsum / 2);
            var gross_sales = parseFloat(estimated_transaction_count * lowestsrp);
    	}

        $("#transact_count").val(expected_transaction_count);
		$("#expected_gross").val(accounting.formatNumber(gross_sales,2,""));

    }

    function compute_rent(monthly_rent,operation_days){
    	var rent = parseFloat(monthly_rent) / parseFloat(operation_days);
    	$("#break_even_rent").val(accounting.formatNumber(rent,2,""));
    }

    function compute_labor_costs(lc, no_of_crew, working_days, operation_days){
    	var labor_cost = (parseFloat(lc) * parseFloat(no_of_crew) * parseFloat(working_days)) / parseFloat(operation_days);
    	$("#break_even_lc").val(accounting.formatNumber(labor_cost,2,""));
    }

    function compute_electricity(ew, operation_days){
    	var ew = parseFloat(ew) / parseFloat(operation_days);
    	$("#break_even_ew").val(accounting.formatNumber(ew,2,""))
    }

    function compute_gov_permit(gov_permit){
    	var govpermit = parseFloat(gov_permit) / 365;
    	$("#break_even_gov_permit").val(accounting.formatNumber(govpermit,2,""));
    }

    function compute_marketing_fee(marketing_fee){
    	var marketingfee = parseFloat(marketing_fee) / 365;
    	$("#break_even_market_fee").val(accounting.formatNumber(marketingfee,2,""));
    }

    function compute_other_fee(other_fee,operation_days){
    	var otherfee = parseFloat(other_fee) / parseFloat(operation_days);
    	$("#break_even_other").val(accounting.formatNumber(otherfee,2,""));
    }

    function compute_total_daily_cost(bes_rent, bes_lc, bes_ew, bes_gov_permit, bes_marketing_fee, bes_other){
    	var daily_cost = parseFloat(bes_rent) + parseFloat(bes_lc) +  parseFloat(bes_ew) + parseFloat(bes_gov_permit) +  
    	parseFloat(bes_marketing_fee) +   parseFloat(bes_other);
    	$("#break_even_daily_cost").val(accounting.formatNumber(daily_cost,2,""));
    }

    function compute_bes(daily_cost,gp) {
        var bes = parseFloat(daily_cost) / gp;
        $("#break_even_bes").val(accounting.formatNumber(bes,2,""));
    }    

    function compute_tc(bes,lowest_srp) {
        var tc = parseFloat(bes) / lowest_srp;
        $("#break_even_tc").val(accounting.formatNumber(tc,2,""));
    }
    
    $("#peak_time_foot_count, #lean_time_foot_count, #operation_days, #no_of_competitors,#actual_gross_sales,#lowest_price_0, #lowest_price_1,lowest_price_2,lowest_price_3,lowest_price_4").blur(function(){
    	var footcount_a = $("#peak_time_foot_count").val() == "" ? 0 : $("#peak_time_foot_count").val();
    	var footcount_b = $("#lean_time_foot_count").val() == "" ? 0 : $("#lean_time_foot_count").val();
        var operation_days = $("#operation_days").val() == "" ? 1 : $("#operation_days").val();
    	compute_estimated_transaction_count(footcount_a,footcount_b,operation_days);
    });

    $("#operation_days, #monthly_rent").blur(function(){
    	var operation_days = $("#operation_days").val() == "" ? 1 : $("#operation_days").val();
    	var monthly_rent = $("#monthly_rent").val() == "" ? 0 : $("#monthly_rent").val();
    	compute_rent(monthly_rent,operation_days);
    });

    $("#labor_cost_per_crew, #recommended_crew, #working_days, #operation_days").blur(function(){

    	var lc = $("#labor_cost_per_crew").val() == "" ? 0 : $("#labor_cost_per_crew").val();
    	var no_of_crew = $("#recommended_crew").val() == "" ? 0 : $("#recommended_crew").val();
    	var working_days = $("#working_days").val() == "" ? 0 : $("#working_days").val();
    	var operation_days = $("#operation_days").val() == "" ? 1 : $("#operation_days").val();
    	compute_labor_costs(lc, no_of_crew, working_days, operation_days);
    });

    $("#operation_days, #ew").blur(function(){
    	var operation_days = $("#operation_days").val() == "" ? 1 : $("#operation_days").val();
    	var ew = $("#ew").val() == "" ? 0 : $("#ew").val(); 
    	compute_electricity(ew, operation_days);
    });

    $("#gov_permit").blur(function(){
    	var gov_permit = $(this).val();
    	compute_gov_permit(gov_permit);
    });

    $("#marketing_fee").blur(function(){
    	var marketing_fee = $(this).val();
    	compute_marketing_fee(marketing_fee);
    });

    $("#other_terms, #operation_days").blur(function(){
    	var operation_days = $("#operation_days").val() == "" ? 1 : $("#operation_days").val();
    	var other_fees = $("#other_terms").val() == "" ? 0 : $("#other_terms").val();
    	compute_other_fee(other_fees,operation_days);
    });

    $("#monthly_rent, #peak_time_foot_count, #lean_time_foot_count, #labor_cost_per_crew, #recommended_crew, #working_days,#ew ,#gov_permit, #marketing_fee,#other_terms, #operation_days, #other_terms").blur(function(){
    	
    	var bes_rent = $('#break_even_rent').val() == "" ? 0 : $('#break_even_rent').val();
    	var bes_lc = $('#break_even_lc').val() == "" ? 0 : $('#break_even_lc').val();
    	var bes_ew = $('#break_even_ew').val() == "" ? 0 : $('#break_even_ew').val();
    	var bes_gov_permit = $('#break_even_gov_permit').val() == "" ? 0 : $('#break_even_gov_permit').val();
    	var bes_marketing_fee = $('#break_even_market_fee').val() == "" ? 0 : $('#break_even_market_fee').val();
    	var bes_other = $('#break_even_other').val() == "" ? 0 : $('#break_even_other').val();

    	compute_total_daily_cost(bes_rent, bes_lc, bes_ew, bes_gov_permit, bes_marketing_fee, bes_other);

    });

    $('#no_of_competitors').change(function(){
    	var no_of_competitors = $(this).val() == "" ? 0 : $(this).val();
    	if(no_of_competitors > 0){
			$("#actual_gross_sales_div").css('display','block');
    	}else{
            $("#actual_gross_sales_div").css('display','none');
        }

    	var defaulthtml = '<tr><th>Competitor’s Name</th><th>Menu</th><th>Price</th><th>Highest</th><th>Lowest</th></tr>';
    	$(".competitors_row").html(defaulthtml);
    	add_row_competitors_table(no_of_competitors);
    });

    // dynamically disabled target market tables element
    $("input[name*='estimated_population']").prop('disabled',true);
    $("input[name*='market_type']").prop('disabled',true);


    $("select[name*='target_market']").change(function(e){
        var name = this.name;
        var index = name.replace( /\D+/g, '');
        var estimated_population = 'estimated_population'+'['+index+']';
        var market_type = 'market_type'+'['+index+']';
        if(this.value == 1){            
            $("input[name='"+estimated_population+"']").prop('disabled',false);
            $("input[name='"+estimated_population+"']").addClass('required_fields');

            $("input[name='"+market_type+"']").prop('disabled',false);
            $("input[name='"+market_type+"']").addClass('required_fields');

        }else if(this.value == 0){
            $("input[name='"+estimated_population+"']").prop('disabled',true);
            $("input[name='"+estimated_population+"']").removeClass('required_fields');
            $("input[name='"+market_type+"']").prop('disabled',true);
            $("input[name='"+market_type+"']").removeClass('required_fields');
        }
    });

    $("#break_even_gp").blur(function(){
        var thiss = $(this).val();
        var percentage = parseInt(thiss.replace('%', '')) / 100;
        var gp = percentage == 0 ? 1 : percentage;
        var daily_cost = $("#break_even_daily_cost").val();
        compute_bes(daily_cost,gp)
    });

     $("#bes_lowest_srp").blur(function(){
         var thiss = $(this).val();
         var lowest_srp = thiss == 0 ? 1 : thiss;
         var bes = $("break_even_bes").val();
         compute_tc(bes,lowest_srp) 
     });

    function getReferenceNo(length) {
        return Math.floor(Math.pow(10, length-1) + Math.random() * 9 * Math.pow(10, length-1));
    }



});