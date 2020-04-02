<?php

 if(!isset($_SESSION['user_id']) && ($token != en_dec('en',$this->session->token_session))) {
   header(base_url('Main/logout'));
 }

?>
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Employees">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('employees/Employee/index/'.$token);?>">Employees</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?=$emp['last_name']?>, <?=$emp['first_name']?> <?=$emp['middle_name']?></li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-12 text-right">
                    <button id = "btn_newContract" class="btn btn-primary">New Contract</button>
                  </div>
                </div>
              </div>
              <div class="card-body">

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- NEW CONTRACT -->
    <div class="modal fade" id = "new_contract_modal">
      <div class="modal-dialog modal-lg-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Create new contract</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <!-- CREDENTIALS -->
            <div class="form-group row">
              <div class="col-12">
                <h4>Credentials</h4>
              </div>
              <div class="col-6">
                <label for="Username" class="form-control-label col-form-label-sm">Username</label>
                <input type="text" class="form-control">
              </div>
            </div>
            <!-- CONTRACT DETAILS -->
            <div class="form-group row">
              <div class="col-12">
                <h4>Cotract Details</h4>
              </div>
              <div class="col-md-4">
                <label for="Worksite" class="form-control-label col-form-label-sm">Worksite</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-md-4">
                <label for="Position" class="form-control-label col-form-label-sm">Position</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-md-4">
                <label for="Employement Status" class="form-control-label col-form-label-sm">Employement Status</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-md-6">
                <label for="Start Date" class="form-control-label col-form-label-sm">Start Date</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-md-6">
                <label for="End Date" class="form-control-label col-form-label-sm">End Date</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-md-6">
                <label for="Company" class="form-control-label col-form-label-sm">Company</label>
                <input type="text" class="form-control">
              </div>
              <div class="col-md-6">
                <label for="Contract Type" class="form-control-label col-form-label-sm">Contract Type</label>
                <input type="text" class="form-control">
              </div>
            </div>
            <!-- WORKSCHEDULES -->
            <div class="form-group row">
              <div class="col-12">
                <h4>Work Schedule</h4>
              </div>
              <div class="col-md-6 mb-3">
                <label for="Type" class="form-control-label col-form-label-sm">Type</label>
                <select name="" id="" class="form-control">
                  <option value="">------</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="Schedule" class="form-control-label col-form-label-sm">Schedule</label>
                <select name="" id="" class="form-control">
                  <option value="">------</option>
                </select>
              </div>
              <div class="col-12">
                <table class="table table-bordered tabled-striped">
                  <thead>
                    <th></th>
                    <th>Work Schedule</th>
                    <th>Break Schedule</th>
                    <th width = "50">Total</th>
                  </thead>
                  <tbody>
                    <?php
                      $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
                    ?>
                    <?php for($x = 0; $x < count((array)$days); $x++):?>
                      <tr>
                        <td><?=$days[$x]?></td>
                        <td>
                          <div class="row">
                            <input type="time" class="form-control offset-md-1 col-md-4">
                            <span class="col-md-2">:</span>
                            <input type="time" class="form-control col-md-4">
                          </div>
                        </td>
                        <td>
                          <div class="row">
                            <input type="time" class="form-control offset-md-1 col-md-4">
                            <span class="col-md-2">:</span>
                            <input type="time" class="form-control col-md-4">
                          </div>
                        </td>
                        <td>
                          <input type="text" class="form-control">
                        </td>
                      </tr>
                    <?php endfor;?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\employees\contracts\contract_new.js')?>"></script>
