
$(function(){

    var base_url = $("body").data('base_url'); //url
    var datas = $("body").data('datas'); // data for query
    var search_label = $("body").data('label'); //label search
    var fpno = $(".fpno").val(); //label search

loadTables();

    function loadTables(){
        $.LoadingOverlay("show");
        var payment_id = $(".fpno").val(); //label search

        $.ajax({
            type: 'post',
            url: base_url+'Main_franchise_accounting/franchise_payment_view_table',
            data:{"payment_id":payment_id},
            
            success:function(data){
                $.LoadingOverlay("hide");
                if(data.success == 1){
                    var count = data.count;   
                    var fpnoData = data.fpno;
                    var paymentData = data.payment;
                    var amountData = data.amount;

                    //clearTable();

                    try{
                        for(i=0;i<count;i++){
                            editArray = [fpnoData[i],paymentData[i],amountData[i]];
                            populateTable(editArray,1);
                        }
                    }catch(e){
                        // console.log("Error msg",e);
                    }
                    
                    //$(".lblItemnameedit").text(data.itemname);

                }else{
                    $.toast({
                        heading: 'Note',
                        text: data.message,
                        icon: 'error',
                        loader: false,  
                        stack: false,
                        position: 'top-center', 
                        allowToastClose: false,
                        bgColor: '#f0ad4e',
                        textColor: 'white'  
                    });
                }
            }
        });       
    }

var table = $('#table-grid').DataTable({ //declaring of table
    "destroy": true,
    columnDefs: [{ targets: [0], sClass: 'text-left'},
    { targets: [1], sClass: 'text-left'},
    { targets: [2], sClass: 'text-left'}],
    //columnDefs: [{ targets: [0], sClass: 'td_id'}],
   //columnDefs: [{targets: [ 0 ],visible: false,searchable: false},{targets: [ 1 ],visible: false,searchable: false}]
});//data table

table.destroy();

//insert data to table
function populateTable(data,val){
    if(val == 1){
        table.row.add(editArray);         
        table.draw();           
    }else if(val == 2){
        table.row.add(selectedDataarray);         
        table.draw();
    }else{
        alert('There was an error populating table, Please check material balance and material edit table codes');
    }
}

});
