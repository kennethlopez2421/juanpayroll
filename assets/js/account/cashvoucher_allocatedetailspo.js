$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
    var supid = $("#supid").val();
    var token = $("#token").val();
    var cvno = $("#cvno").val();


	var dataTable = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
        "bDeferRender": true,
		"destroy":true,
		"ajax":{
			url :base_url+"Main_account/table_popayment_allocate", // json datasource
			type: "post",  // method  , by default get
			data: {"supid" : supid, 'token': token, 'cvno': cvno},
            beforeSend:function(data)
            {
                $.LoadingOverlay("show"); 
            },
            complete: function()
            {
                $.LoadingOverlay("hide"); 
            },
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid_processing").css("display","none");
			}
		},

	});
    dataTable.destroy();

    //start
    $("#allocatecvBtn").click(function(e){
        e.preventDefault();
        var popaybalance = $("#popaybalance").val();
        var token = $("#token").val();
        var grandtotal = $("#grandtotal").val();
        var cvno = $("#cvno").val();
        var isGood = $("#isGood").val();
        var supid = $("#supid").val();
        var checker = 0;
        var totalpay = 0;
        if(isGood > 0)
        {
            var supponoarray = [];
            var poamtarray = [];
            var cvamtarray = [];
            var ponoarray = [];


            supponoarray = [];
            poamtarray = [];
            cvamtarray = [];
            ponoarray = [];


            if(grandtotal > 0)
            {
                for(i=0; i < grandtotal; i++ )
                {
                    var suppono = $("#suppono"+i).val();
                    var unpaid = $("#unpaid"+i).val();
                    var poamt = $("#po"+i).val();
                    var pono = $("#pono"+i).val();

                    if(poamt > 0)
                    {
                        supponoarray.push(suppono);
                        poamtarray.push(unpaid);
                        cvamtarray.push(poamt);
                        ponoarray.push(pono);

                        totalpay += parseFloat(poamt);
                    }
                }
                checker=1;
            }
        }


        if(checker==1)
        {
            $.ajax({
            type: 'post',
            url: base_url+'Main_account/save_allocatepo_cashvoucherpo',
            data:{'cvno':cvno, 
                  'popaybalance': popaybalance,
                  'grandtotal': grandtotal,
                  'supponoarray': supponoarray,
                  'poamtarray': poamtarray,
                  'cvamtarray': cvamtarray,
                  'totalpay': totalpay,
                  'supid': supid,
                  'ponoarray': ponoarray,

                },
                beforeSend:function(data)
                {
                    $.LoadingOverlay("show"); 
                    // $(".btnCVComfirm").text("Please wait...");
                    // $('.btnCVComfirm').prop('disabled', true);
                    // $('#Modalloadingbar').modal('show');
                },
                complete: function()
                {
                    $.LoadingOverlay("hide"); 
                },
                success:function(data)
                {

                    if (data.success == 1) 
                    {
                        $(".btnCVComfirm").text("Allocated"); 
                        $.toast({
                            heading: 'Success',
                            text: "CV #"+ cvno +" has been successfully allocated to expenses.",
                            icon: 'success',
                            loader: false,  
                            stack: false,
                            position: 'top-center', 
                            bgColor: '#5cb85c',
                            textColor: 'white',
                            allowToastClose: false,
                            hideAfter: 2000,
                        });
                        window.setTimeout(function(){
                            window.location.href=base_url+"Main_account/cashvoucher_transaction/" + token;
                        })
                        

                    }
                }

            });
        }
        




    });   
    //end  

});

function amountAllocateCollection(count,unpaidamount)
{
    
    var fieldID = 'po'+count;
    var fieldrun = 'po';
    var popaybalance = $("#popaybalance").val();
    var grandtotal = $("#grandtotal").val();
    var recAmount = $("#po"+count).val();

    var diff=0;

    checker=1;
    if (recAmount == "")
    {
        $("#po"+count).val(0);
    }
    else
    {
        if(parseFloat(recAmount) || (recAmount==0))
        {
           if (recAmount>unpaidamount)
           {
                checker = 3;
                $("#isGood").val(0);
           }
        }
        else
        {
            $("#po"+count).val(0);
            $("#isGood").val(0);
        }
    }
  
    if (checker==1)
    {      
        var totalamt=0;
        for(var a=0; a<=grandtotal-1; a++)
        {
            var val = $("#po"+a).val();
            if(val == "")
            {
                val=0;
            }
            totalamt += parseFloat(val);
        }
        
        diff = (popaybalance*1)-(totalamt*1);
        if (diff>=0)
        {
            if(totalamt==0)
            {
                document.getElementById("allocatecvBtn").disabled = true;
            }
            else
            {
                document.getElementById("allocatecvBtn").disabled = false;
                $("#isGood").val(1);
            }
            
            totalamt = formatMoney(totalamt); 
            setText('allocLabel',totalamt);
        }
        else
        {
            $.toast({
                heading: 'Warning',
                text: 'You have insufficient balance for allocation.',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                bgColor: '#ffc107',
                textColor: 'white',
                allowToastClose: false,
                hideAfter: 3000
             });
            $("#po"+count).val("");
            $("#isGood").val(0);
        }
    }
    else if (checker==3) 
    {
        $.toast({
                heading: 'Warning',
                text: 'Allocated amount is exceeds the unpaid amount.',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                bgColor: '#ffc107',
                textColor: 'white',
                allowToastClose: false,
                hideAfter: 3000
            });
        $("#po"+count).val("");
    }
    else
    {
            $.toast({
                heading: 'Warning',
                text: 'Please make sure all values entered are correct.',
                icon: 'error',
                loader: false,  
                stack: false,
                position: 'top-center', 
                bgColor: '#ffc107',
                textColor: 'white',
                allowToastClose: false,
                hideAfter: 3000
            });
        $("#po"+count).val("");
    }  
}    

function setText(id, txt)
{
 var elem;
   
 if( document.getElementById  && (elem=document.getElementById(id)) )
 {
  if( !elem.firstChild )
   elem.appendChild( document.createTextNode( txt ) );
  else
   elem.firstChild.data = txt;
 }
 
 return false;
}

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}

 function blockSpecialChar(e)
 {
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
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

function formatMoney(n,c, d, t)
{
    c = isNaN(c = Math.abs(c)) ? 2 : c;
    d = d == undefined ? "." : d;
    t = t == undefined ? "," : t; 
    s = n < 0 ? "-" : "";
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
    j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}



