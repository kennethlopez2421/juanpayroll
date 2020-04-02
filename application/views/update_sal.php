<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?=base_url('assets/css/bootstrap.min.css');?>">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link rel="stylesheet" href="<?=base_url('assets/css/style.blue.css');?>" id="theme-stylesheet">
    <link rel="stylesheet" href="<?=base_url('assets/css/select2-materialize.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/custom.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/style.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets/css/jquery.toast.css');?>">
    <link rel="stylesheet" href="<?=base_url('assets\css\css_loader\css-loader.css')?>">


    <title></title>
  </head>
  <body data-base_url = "<?=base_url()?>">
    <div class="modal fade" id = "update_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Salary</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form id="update_sal_form">
            <div class="modal-body">
              <div class="form-group row">
                <div class="col-12">
                  <label for="Company Code" class="form-control-label col-form-label-sm">Company Code</label>
                  <input type="text" class="form-control" id = "c_code" name = "c_code">
                </div>
              </div>
            </div>
            <div class="modal-footer text-right">
              <button type = "submit" id = "btn_submit" class="btn btn-sm btn-primary">Submit</button>
              <button type = "button" class="btn blue-grey" data-dismiss = "modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
<script src="<?=base_url('assets/js/jquery.min.js');?>"></script>
<script src="<?=base_url('assets/js/bootstrap.min.js');?>"></script>
<script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>
<script src="<?=base_url('assets/js/notification.js');?>"></script>
<script src="<?=base_url('assets\js\marky\custom-loader.js');?>"></script>

<script>
  $(function(){
    var base_url = $("body").data('base_url');
    // console.log('hellow world');
    $('#update_modal').modal();
    $(document).on('submit', '#update_sal_form', function(e){
      e.preventDefault();
      $.ajax({
        url: base_url+'Main/update_sal_form',
        type: 'post',
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: function(){
          $.LoadingOverlay('show');
          $('#btn_submit').attr('disabled', true);
        },
        success: function(data){
          $.LoadingOverlay('hide');
          $('#btn_submit').prop('disabled', false);
          if(data.success == 1){
            alert("Success");
            setTimeout(() => {location.reload(true)},1000);
          }else{
            alert("Failed");
          }
        }
      });
    });
  });
</script>
