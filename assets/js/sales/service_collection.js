$(function(){
	var base_url = $("body").data('base_url'); //url
	var datas = $("body").data('datas'); // data for query
	var search_label = $("body").data('label'); //label search

    var member_name = "";

       $(document).on("change",".searchCustomer",function(e){
           
           var idno = $("#searchCustomer").val();

            if (idno != "") {
                        $.ajax({
                            type:'post',
                            url: base_url+'Main_sales/show_supplier_info',
                            data:{"idno": idno},
                           //  data:{"qty": qty},
                            success:function(data){
                                // alert(dataTable.draw());
                                if (data.success == 1) {
                                    var res = data.result;
                                    $("#txtResult").html(res.data[1]);
                                }
                            }

                        });
                    }
        });

    var payment_name = "";
    var options1 = {

        url: base_url+'Main_sales/autocomplete_payment',
        getValue: "name",
        list: {

            onSelectItemEvent: function() {
                payment_id = $(".sel_payment").getSelectedItemData().code;
                customer_name = $(".sel_payment").getSelectedItemData().name;

                $("#payment_id").val(payment_id);
            },
            hideAnimation: {
                type: "slide", //normal|slide|fade
                time: 300,
                callback: function() {}
            },
            match: {
                enabled: true
            }

        },
        theme: "square"
    };
    $(".sel_payment").easyAutocomplete(options1);

    $(".BtnSaveCollection").click(function(e){
        e.preventDefault();
        var text_label = $('.step_label').text();
        if (text_label == 'Service Collection Information') {

            //$(".label-top_up").text('E-Wallet Encashment');
            $(".BtnSaveCollection").prop("hidden",true);

        }else if(text_label == 'Message') {
            
            $(".BtnSaveCollection").prop("hidden",false);
        }

        setTimeout(function(data){         
            $(".BtnSaveCollection").prop("disabled",false); 
        },2000);
    });

    function makeProgress(from, to){ //increase

        if(from < to){
            from = from + .20;
            $(".progress-bar").css("width", from + "%");    
        }
        // Wait for sometime before running this script again
        setTimeout(function(){
            makeProgress(from, to);
        }, 1);
    }

   $(".BtnSaveCollection").click(function(e){
         e.preventDefault();

            var idno = $("#idno").val(); // validation
            var trandate = $("#trandate").val(); // validation
            var payment_id = $("#payment_id").val();
            var amount = $("#amount").val();
            var ref_no = $("#ref_no").val();

            var currentdate = new Date();
                           
                        $.ajax({
                            type:'post',
                            url:base_url+'Main_sales/service_collection_save',
                            data:{
                            "idno": idno,
                            "trandate": trandate,
                            "payment_id": payment_id,
                            "amount": amount,
                            "ref_no": ref_no
                            },
                            success:function(data){
                            if(data.success == 1)
                                {

                                    makeProgress(33.3,66.6);
                                    $('.step_label').text('Message'); //step 2
                                    $('.step1').css('overflow',"hidden");
                                    $('.step1').css('position',"absolute");
                                    $('.step1').hide('slide', {direction: 'left'}, 1000);
                                    $('.step2').stop().show('slide', {direction: 'right'}, 1000);

                                    $('#collectionid').val(data.collectionid);
                                    $('#customerid').val(data.customer);

                                    // $(".BtnNext").prop("disabled",true);
                                    setTimeout(function(){
                                        $('.step1').css('overflow',"visible");
                                        $('.step1').css('position',"static");

                                    },2000);

                                    $.toast({
                                        heading: 'Success',
                                        text: 'Collection has been successfully saved.',
                                        icon: 'success',
                                        loader: false,  
                                        stack: false,
                                        position: 'top-center', 
                                        bgColor: '#5cb85c',
                                        textColor: 'white',
                                        allowToastClose: false,
                                        hideAfter: 3000
                                     });

                                    $( '.amount' ).val("");
                                    $( '.payment_id' ).val("");
                                    $('.trandate').val($.datepicker.formatDate('mm/d/yy', currentdate));
                                    $( '.ref_no' ).val("");
                                    $( '.sel_payment' ).val("");
                                    $( '.searchCustomer' ).val("");

                                }else{
                                    $.toast({
                                        heading: 'Note',
                                        text: 'Please fill out required fields.',
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
                                
                            }
                        });
              //  }
      //  }        
});

       $('.trandate').datepicker({
            format: 'mm/dd/yyyy',
            startDate: '-2d',
            endDate: '+60d'
       // dataTable.columns(i).search(v).draw();
    });

});

