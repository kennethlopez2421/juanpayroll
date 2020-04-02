$(document).ready(function(){


	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	$('.chkdatediv').show('slow');
	var searchtype = "chkdatediv";
	var token = $('#token').val();



$(".saveBtnEncode").click(function(e){
    e.preventDefault();

    var date = $("#date1").val();
    var payto = $("#payto").val();
    var amt = $("#amt").val();
    var chkno = $("#chkno").val();

    if(date == "" || payto == "" || amt == "" || amt == 0 ||  chkno == "" || chkno == 0){
		$.toast({
            heading: 'Note',
            text: 'Please fill required fields.',
            icon: 'error',
            loader: false,  
            stack: false,
            position: 'top-center', 
            bgColor: '#f0ad4e',
            textColor: 'white',
            allowToastClose: false,
       });
    }else{

        $.ajax({
            url: base_url+"Main_account/updateCheck_save",
            type: 'post',
            data: { 
				'date':date,
				'payto':payto,
				'amt':amt,
				'chkno':chkno
            },

            beforeSend: function() {
                $.LoadingOverlay("show");
            },

            success:function(data){
                $.LoadingOverlay("hide");
                if(data.success == 1){      
                window.setTimeout(function(){
                         window.location.href=base_url+"Main_account/check_transaction_history/" + token;
                  },500);
                    $.toast({
                        heading: 'Success',
                        text: 'Check has been successfully updated.',
                        icon: 'success',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        bgColor: '#5cb85c',
                        textColor: 'white',
                        allowToastClose: false,
                     });
                }else{
                    $.toast({
                        heading: 'Note',
                        text: 'Please fill required fields.',
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        bgColor: '#f0ad4e',
                        textColor: 'white',
                        allowToastClose: false,
                   });
                }    
            }
        });
    }
    });   

});

