<?php
  if(isset($this->session->admin_user_id) && isset($this->session->admin_username)){
    $username = en_dec('dec',$this->session->username);
    $user = $this->admin_model->get_admin_user($username);
    if($user->num_rows() == 0){
      header("Location:".base_url('Main/logout'));
    }
  }else{
    header("Location:".base_url('Main/logout'));
  }
?>
<style>
  .strong{ font-weight: bold;}
  ._120px{ width: 120px !important; text-align: center;}
  ._50px{width:50px !important;}
</style>
<link rel="stylesheet" href="<?=base_url('assets/css/custom_checkbox.css')?>">
<div class="content-inner" id="pageActive" data-num="26" data-namecollapse="" data-labelname="Transfer Data">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token);?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Transfer Data</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="form-group row">
                  <div class="col-md-4 mb-3">
                    <label for="Transfer_data" class="form-control-label col-form-label-sm">Transfer_data <span class="asterisk"></span></label>
                    <select name="transfer_data" id="transfer_data" class="form-control">
                      <option value="employee_record">Employee Record</option>
                      <option value="applicant_record">Applicant Record</option>
                    </select>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="Data Origin:" class="form-control-label col-form-label-sm">Data Origin: <span class="asterisk"></span></label>
                    <select name="data_origin" id="data_origin" class="form-control select2 rq">
                      <option value="">------</option>
                      <?php if($branches !== false):?>
                        <?php foreach($branches as $branch):?>
                          <option value="<?=en_dec('en',$branch['database_name'])?>"><?=$branch['branch_name']?></option>
                      <?php endforeach;?>
                      <?php else:?>
                        <option value="">No available branch</option>
                      <?php endif?>
                    </select>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="Transfer To:" class="form-control-label col-form-label-sm">Transfer To: <span class="asterisk"></span></label>
                    <select name="transfer_to" id="transfer_to" class="form-control select2 rq">
                      <option value="">------</option>
                      <?php if($branches !== false):?>
                        <?php foreach($branches as $branch):?>
                          <option value="<?=en_dec('en',$branch['database_name'])?>"><?=$branch['branch_name']?></option>
                        <?php endforeach;?>
                      <?php else:?>
                        <option value="">No available branch</option>
                      <?php endif?>
                    </select>
                  </div>
                </div>
              </div>
              <form id = "transfer_form">
              <div class="card-body">
                  <div class="table-responsive">
                  <table class="table table-bordered" style = "border-top:1px solid gainsboro;" id = "data_origin_tbl">
                    <thead>
                      <th class = "strong _50px">Select</th>
                      <th class = "strong">Name</th>
                      <th class = "strong _120px">Employee Record</th>
                      <th class = "strong _120px">Contract Record</th>
                      <th class = "strong _120px">Clock In/Out Record</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td colspan = "5" class = "text-center">No data available</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type = "submit" class="btn btn-primary" id = "btn_transfer">Transfer</button>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\transfer\transfer.js')?>"></script>
