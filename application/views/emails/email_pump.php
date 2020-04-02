<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Email</title>
    <link rel="shortcut icon" href="<?=base_url('assets/img/juanpayroll-logo-05.png');?>">
    <style>
      img{
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50%;
        height: auto;
      }

      h4{
        position: fixed;
        top: 60%;
        left: 50%;
        font-family: calibri;
        font-size: 60%;
        transform: translate(-40%, 60%);
      }
    </style>
  </head>
  <body data-base_url = "<?=base_url()?>">
    <img src="<?=base_url('assets/img/email_send2.gif')?>" alt="">
    <h4>Sending Email . . .</h4>
    <input type="hidden" id = "edata" value = "<?=$email_data?>">
    <input type="hidden" id = "token" value = "<?=$token?>">
  </body>
</html>
<script src="<?=base_url('assets/js/jquery.min.js');?>"></script>
<script>
  $(function(){
    let base_url = $('body').data('base_url');
    let token = $('#token').val();
    let edata = $('#edata').val();

    $.ajax({
      url: base_url+'emails/Transaction_email/email_pump',
      type: 'post',
      data:{edata},
      success: function(data){
        window.close();
        // alert(data);
        // console.log(data);
      }
    });
  });
</script>
