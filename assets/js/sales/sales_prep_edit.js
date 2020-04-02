$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	var prepno = $("#sono_id_sec").data("sono");
	var idno = $(".idno").val();
	var sono1 = $("#sono").val();
	var token = $("#token").val();
    var iltno = $("#iltno").val();

    var dataTable = $('#table-grid').DataTable({

      "serverSide": true,
      "destroy":true,
      "ajax":{
			url :base_url+"Main_sales/table_salesprep_edit", // json datasource
			type: "post",  // method  , by default get
			data: {"sono1" : sono1, "prepno":prepno, "iltno":iltno},
            beforeSend:function(data){
                $("body").LoadingOverlay("show"); 
            },
			error: function(){  // error handling
				$("body").html("");
				// $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			},
            complete: function(){
                $("body").LoadingOverlay("hide"); 
            },
        },
    });

    dataTable.destroy();

    $("#salesprepform").submit(function(e){
        e.preventDefault();
        form = $(this);
        serializedForm = form.serializeArray();

        serializedForm.push({
            name: "totalrows", value: $("#totalrows").val(),
            name: "prep_date", value: $("#prep_date").val(),
            name: "locationTo", value: $("#locationTo").val(),
            name: "locationFrom", value: $("#locationFrom").val(),
            name: "sono", value: $("#sono").val(),
            name: "iltno", value: $("#iltno").val(),
            name: "prepno", value: $("#sono_id_sec").data("sono")
        });

        $.ajax({
            type:'post',
            url:base_url+'Main_sales/table_save_addsalepreparation',
            data: serializedForm,
            beforeSend: function() {
                $.LoadingOverlay("show");
                //$("#BtnCreateITL").prop("disabled", true);

            },
            complete: function(){
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                $.LoadingOverlay("hide");
                if(data.success == 1)
                {     
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
                //hideAfter: 3000
            });

               //   window.setTimeout(function(){
               //     window.location.href=base_url+"Main_sales/salesorder_prep_summary/" + token + '/' +prepno + '/' + sono1;
               // })
             } else{
                $.toast({
                    heading: 'Note',
                    text: data.message,
                    icon: 'error',
                    loader: false,  
                    stack: false,
                    position: 'top-center', 
                    bgColor: '#f0ad4e',
                    textColor: 'white',
                    allowToastClose: false,
                    //hideAfter: 3000
                });

            }     
        },
    });
    });

    $("#closePrep").click(function(e){
        e.preventDefault();

        // var token = $("#token").val();    
        // var count = $("#tdata").val(); // validation    
        
        // var checkbox_value = "";
        // $("input.prep_check[type=checkbox]").each(function () {
        //     var ischecked = $(this).is(":checked");
        //     if (ischecked) {
        //         checkbox_value +=  $(this).val() + ["|"];
        //     }
        // });

        $.ajax({
            type:'post',
            url:base_url+'Main_sales/closePreparation_salesorder',
            data:{
                "prepno":prepno,
            },
            beforeSend:function(data)
            {
                $("body").LoadingOverlay("show"); 
                $("#closePrep").prop("disabled", true);
            },
            complete: function(){
                $("body").LoadingOverlay("hide"); 
            },
            success:function(data){
                if(data.success == 1)
                {     
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
                        hideAfter: 3000
                    });
                    
                } else{
                    $.toast({
                        heading: 'Note',
                        text: data.message,
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
                window.setTimeout(function(){
                   window.location.href=base_url+"Main_sales/salesorder_prep_summary/" + token + '/' +prepno + '/' + sono1;
               })
            }   
        });    
    });
});

function rcvAllocate(count,qtybalance)
{

    var fieldID = 'qtytoreceive'+count;
    var fieldrun = 'qtytoreceive';
    var rcvQty = document.getElementById(fieldID).value;
    var totalrows = document.getElementById("totalrows").value;
    
    var diff=0;

    checker=1;
    if (rcvQty == "")
    {
        document.getElementById(fieldID).value=0;
    }
    else
    {
        if(parseFloat(rcvQty) || (rcvQty==0))
        {
         if (rcvQty>qtybalance)
         {
            checker = 3;
        }
    }
    else
    {
        document.getElementById(fieldID).value=0;
    }
}


if (checker==1)
{
    var totalamt=0;
    for(var a=1; a<=totalrows; a++)
    {
        fieldrun = 'qtytoreceive'+a;
        var val = document.getElementById(fieldrun).value;

        if (val=="x")
        {
            val=0;
        }
        totalamt = (totalamt*1)+(val*1);
    }

    if (totalamt>=0)
    {

        if(totalamt==0)
        {
            document.getElementById("BtnCreateITL").disabled = true;
        }
        else
        {
            document.getElementById("BtnCreateITL").disabled = false;
        }
    }
    else
    {
        $.toast({
            heading: 'Note',
            text: 'No Qty to Receive has been found.',
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
else if (checker==3) 
{
    $.toast({
        heading: 'Note',
        text: 'Qty to receive exceeds the unreceived quantity.',
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
else
{
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