$(function(){
	var base_url = $("body").data('base_url'); //url

    //start
    $(".BtnSavetag").click(function(e){
        e.preventDefault();
    
        var drno = $("#drno").val();
        var drstat = $("#drstat").val();
        var driver = $("#driver").val();
        var noofbags = $("#noofbags").val();
        var crew1 = $('#crew1').val();
        var crew2 = $("#crew2").val();
        var notes = $("#notes").val();
        var checker = 0;
        if(drno != "")
        {
            if(drstat != "")
            {
                  if(driver != "")
                  {
                      if(noofbags != "")
                      {
                          if(crew1 != "")
                          {
                              if(crew2 != "")
                              {
                                checker=1;
                              }
                              else
                              {
                                checker=0;
                              }
                          }
                          else
                          {
                            checker=0;
                          }
                      }
                      else
                      {
                        checker=0;
                      }
                  }
                  else
                  {
                    checker=0;
                  }
            }
            else
            {
                checker=0;
            }
        }
        else
        {
            checker=0;
        }

        if(checker == 1)
        {
                $.ajax({
                type: 'post',
                url: base_url+'Main_sales/check_if_drexisted',
                data:{'drno':drno,
                    },
                success:function(data)
                {
                    if (data.success == 1) 
                    {
                          $.ajax({
                            type: 'post',
                            url: base_url+'Main_sales/save_drtagging',
                            data:{'drno':drno,
                                  'drstat': drstat,
                                  'driver': driver,
                                  'noofbags': noofbags,
                                  'crew1': crew1,
                                  'crew2': crew2,
                                  'notes': notes
                            },
                            success:function(data)
                            {
                                if (data.success == 1) 
                                {
                                    $.toast({
                                        heading: 'Success',
                                        text: "DR #"+ drno +" has been successfully tagged.",
                                        icon: 'success',
                                        loader: false,  
                                        stack: false,
                                        position: 'top-center', 
                                        bgColor: '#5cb85c',
                                        textColor: 'white',
                                        allowToastClose: false,
                                        hideAfter: 2000,
                                    });
                                    window.setTimeout(function(){location.reload()},2000)
                                }
                            }
                        });
                    }
                    else if (data.success == 2) 
                    {
                       $.toast({
                              heading: 'Note',
                              text: "DR number is already been tagged. Please check your data.",
                              icon: 'info',
                              loader: false,   
                              stack: false,
                              position: 'top-center',  
                              bgColor: '#FFA500',
                              textColor: 'white',
                              allowToastClose: false,
                              hideAfter: 4000          
                          });
                    } 
                    else
                    {
                        $.toast({
                            heading: 'Note',
                            text: "DR number does not existed. Please check your data.",
                            icon: 'info',
                            loader: false,   
                            stack: false,
                            position: 'top-center',  
                            bgColor: '#FFA500',
                            textColor: 'white',
                            allowToastClose: false,
                            hideAfter: 4000          
                        });
                    }
                }
            });
        }
        else
        {
            $.toast({
                    heading: 'Note',
                    text: "Please fill all required fields that has red marks.",
                    icon: 'info',
                    loader: false,   
                    stack: false,
                    position: 'top-center',  
                    bgColor: '#FFA500',
                    textColor: 'white',
                    allowToastClose: false,
                    hideAfter: 4000          
            });
        }


    });    

	
});

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
}

