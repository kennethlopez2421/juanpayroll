$('#addAOrderItemModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

$('#addAPurchaseItemModal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
});

function goBack() {
    window.history.back();
}

$(".btn.disabled").toggleClass('disable');

$(window).load(function(){

$('.btnassignShip').attr('disabled', true);
$('#shipping').on('keyup',function() {
    if($(this).val() != '') {
        $('.btnassignShip').attr('disabled' , false);
    }else{
        $('.btnassignShip').attr('disabled' , true);
    }
});
 
});//]]> 

function isNumberKeyOnly(evt)   
{    
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
             return false;
          return true;
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

$(".btnassignShip").click(function(e){
e.preventDefault();
    var ship_grandtotal=0;
    var shipamt = $("#shipping").val();
    var grand_total1 = $("#grand_total2").val();
    var shippinginput =  $("#shippingamt").val();

    if((shippinginput > 0) && (shippinginput != 0)) 
    {
         var ship_grandtotal1  =  parseFloat(shipamt) + parseFloat(grand_total1);
         ship_grandtotal = parseFloat(ship_grandtotal1) - parseFloat(shippinginput) ;
    }
    else
    {
        ship_grandtotal  =  parseFloat(shipamt) + parseFloat(grand_total1);
    }

    $("#shippingamt").val(shipamt);
    // document.getElementById('btnShipping').innerText = "Shipping: " + formatMoney(shipamt,2, ".", ",");

    $(".btnShipping").text("Shipping : "+ formatMoney(shipamt,2, ".", ","));

     $("#grand_total2").val(ship_grandtotal);
     $(".grand_total").text("Total : "+formatMoney(ship_grandtotal,2, ".", ","));
     $("#shippingamt").val(shipamt);
     
});

$(".btn.disabled").toggleClass('disable');

    $(document).ready(function() { 
        $('#valbox').change(function() {
          $('#shipping_cost').val($('#valbox').val());
          $('#shipping_cost1').val(accounting.formatMoney($('#valbox').val()));
        });
    });

var putThousandsSeparators;

$(function(){
    $(".valid_number").each(function () {

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


putThousandsSeparators = function(value, sep) {
  if (sep == null) {
    sep = ',';
  }
  // check if it needs formatting
  if (value.toString() === value.toLocaleString()) {
    // split decimals
    var parts = value.toString().split('.')
    // format whole numbers
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep);
    // put them back together
    value = parts[1] ? parts.join('.') : parts[0];
  } else {
    value = value.toLocaleString();
  }
  return value;
};


function myFunctionReloadPage() {
    location.reload();
}
                      
$(window).load(function(){
   $('#shipping_cost1').change(function() {
       $('#shipping_cost').val($(this).val());
   });
   });

function reloadBack() {
    location.reload();
}

$('.searchCustomer', '.address').each(function() {
    var default_value = this.value;
    $(this).focus(function() {
        if(this.value == default_value) {
            this.value = '';
        }
    });
    $(this).blur(function() {
        if(this.value == '') {
            this.value = default_value;
        }
    });
});

function show_info() {
    var list = "";
     
      list += '<h6 class="secondary-bg px-4 py-3 white-text">Sales Order Information</h6>'; 

      list += '<div class="p-4">';

      list += '<h4 class="text-uppercase font-weight-bold"> '+ $('#name_only').val() + '</h4>';
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Date of Delivery:</div><div class="col-md-9 col-12">' + $('#sales_date').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Branch:</div><div class="col-md-9 col-12">   '+ $('#branchname').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Mode of Payment:</div><div class="col-md-9 col-12">   ' + $('#term_credit').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Contact #:</div><div class="col-md-9 col-12">   '+ $('#contact_no').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Address:</div><div class="col-md-9 col-12">   '+ $('#address').val() + '</div>';
      list += '</div>'

      list += '</div">';

  $("#showInfo").html(list);
}

function show_info_return() {
    var list = "";
     
      list += '<h6 class="secondary-bg px-4 py-3 white-text">Sales Return Information</h6>'; 

      list += '<div class="p-4">';

      list += '<h4 class="text-uppercase font-weight-bold"> '+ $('#membername').val() + '</h4>';
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Date of Delivery:</div><div class="col-md-9 col-12">' + $('#sales_date').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Branch:</div><div class="col-md-9 col-12">   '+ $('#branchname').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Mode of Payment:</div><div class="col-md-9 col-12">   ' + $('#term_credit').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Contact #:</div><div class="col-md-9 col-12">   '+ $('#contact_no').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Address:</div><div class="col-md-9 col-12">   '+ $('#address').val() + '</div>';
      list += '</div>'

      list += '</div">';

  $("#showInforeturn").html(list);
}

function show_info_purchasereturn() {
    var list = "";
     
      list += '<h6 class="secondary-bg px-4 py-3 white-text">Purchase Return Information</h6>'; 

      list += '<div class="p-4">';
      
      list += '<h4 class="text-uppercase font-weight-bold"> '+ $('#suppliername').val() + '</h4>';
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Date of Delivery:</div><div class="col-md-9 col-12">' + $('#purchase_date').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Contact Person:</div><div class="col-md-9 col-12">   '+ $('#contact_person').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Mode of Payment:</div><div class="col-md-9 col-12">   ' + $('#term_credit').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Contact #:</div><div class="col-md-9 col-12">   '+ $('#supp_contact').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Address:</div><div class="col-md-9 col-12">   '+ $('#address').val() + '</div>';
      list += '</div>'

      list += '</div">';

  $("#showInforet").html(list);
}

function show_infofsr() {
    var list = "";
     
      list += '<h6 class="secondary-bg px-4 py-3 white-text">FSR Information</h6>'; 

      list += '<div class="p-4">';

      list += '<h4 class="text-uppercase font-weight-bold"> '+ $('#name_only').val() + '</h4>';
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Date of Delivery:</div><div class="col-md-9 col-12">' + $('#sales_date').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Branch:</div><div class="col-md-9 col-12">   '+ $('#branchname').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Mode of Payment:</div><div class="col-md-9 col-12">   ' + $('#term_credit').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Contact #:</div><div class="col-md-9 col-12">   '+ $('#contact_no').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Address:</div><div class="col-md-9 col-12">   '+ $('#address').val() + '</div>';
      list += '</div>'

      list += '</div">';

  $("#showInfo").html(list);
}

function show_name() {
    var list = "";
      list += '<h4> '+ $('#name_only').val() + '</h4>';

  $("#showInfo").html(list);
}

function show_info_supplier() {
      var list = "";
      list += '<h4 class="text-uppercase font-weight-bold"> '+ $('#suppliername').val() + '</h4>'; 
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Date:</div><div class="col-md-9 col-12">' + $('#purchase_date').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Contact Person:</div><div class="col-md-9 col-12">' + $('#contact_person').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Mode of Payment:</div><div class="col-md-9 col-12">' + $('#term_credit').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Contact No.:</div><div class="col-md-9 col-12">' + $('#supp_contact').val() + '</div>';
      list += '</div>'
      list += '<div class="row">'
      list += '<div class="col-md-3 col-12 font-weight-bold">Address:</div><div class="col-md-9 col-12">' + $('#address').val() + '</div>';
      list += '</div>'
  $("#showInfo").html(list);
}

function show_franchise_service() {
    var list = "";
      list += '<h4 style="text-transform: uppercase;"> '+ $('#name_only').val() + '</h4>'; 
      list += '<b style="margin-right: 28px;">Delivery:</b>   ' + $('#trandate').val() + '<br />';
      list += '<b style="margin-right: 17px;">Mode of Payment:</b>   ' + $('.selpayment').val() + '<br />';
  $("#show_franchise_service").html(list);
}

$(window).load(function(){

$('#enable').click(function (e) {
  e.preventDefault();
    $('#fsr_price').removeAttr("disabled");
});

});
