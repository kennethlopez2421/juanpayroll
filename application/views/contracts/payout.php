<?php
//071318
//this code is for destroying session and page if they access restricted page

$position_access = $this->session->userdata('get_position_access');
$access_content_nav = $position_access->access_content_nav;
$arr_ = explode(', ', $access_content_nav); //string comma separated to array
$get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();

$url_content_arr = array();
foreach ($get_url_content_db as $cun) {
    $url_content_arr[] = $cun['cn_url'];
}
$content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/';

if (in_array($content_url, $url_content_arr) == false){
    header("location:".base_url('Main/logout'));
}
//071318
?>
<!-- <style>
  .input-group-text{
    background-color: #607d8b!important;
    color: #fff;
  }
</style> -->
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Entity">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/employees_home/'.$token);?>">Entity</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Payout Information</li>
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
                  <div class="col-md-3">
                    <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                    <select name="" id="filter_by" class="form-control">
                      <option value="by_id">Employee Id</option>
                      <option value="by_name">Employee Name</option>
                      <option value="by_medium">Payout Medium</option>
                      <option value="by_bank">Bank</option>
                      <!-- <option value="by_amount">Amount</option> -->
                    </select>
                  </div>

                  <div class="col-md-6">
                    <div id="divEmpID" class = "filter_div active">
                      <label for="Employee ID" class="form-control-label col-form-label-sm">Employee ID</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divName" class = "filter_div" style = "display:none;">
                      <label for="Employee Name" class="form-control-label col-form-label-sm">Employee Name</label>
                      <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                    </div>

                    <div id="divMedium" class="filter_div" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Payout Medium</label>
                      <select class = "form-control searchArea">
                        <option value="">------</option>
                        <?php if($payout_mediums->num_rows() > 0):?>
                          <?php foreach($payout_mediums->result_array() as $pm):?>
                            <option value="<?=$pm['payoutmediumid']?>"><?=$pm['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divBank" class = "filter_div" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Bank</label>
                      <select name="p_bank" id="p_bank" class="form-control searchArea">
                        <option value="">------</option>
                        <?php if($banks->num_rows() > 0):?>
                          <?php foreach($banks->result_array() as $bank):?>
                            <option value="<?=$bank['bank_id']?>"><?=$bank['bank_name']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                  </div>
                  <div class="col-md-3 text-right">
                    <!-- <a href="<?= base_url('transactions/Additionalpays/add/'.$token) ?>"><button class="btn btn-primary btnClickAddArea">Add</button></a> -->
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered" id = "payout_tbl">
                    <thead>
                      <th>Employee Id</th>
                      <th>Employee Name</th>
                      <th>Payout Medium</th>
                      <th>Bank</th>
                      <th width = "100">Action</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id = "payout_modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Payout Information</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">

            <div class="form-group row" >
              <div class="col-md-12">
                <label for="Payout Medium" class="form-control-label col-form-label-sm ">Payout Medium <span class="asterisk"></span></label>
                <select name="p_medium" id="p_medium" class="form-control rq">
                  <option value="">---</option>
                  <?php if($payout_mediums->num_rows() > 0):?>
                    <?php foreach($payout_mediums->result_array() as $pm):?>
                      <option value="<?=$pm['payoutmediumid']?>"><?=$pm['description']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
            </div>

            <div class="form-group row card_info_div" style = "display:none;">

              <div class="col-md-12 mb-3">
                <label for="Bank" class="form-control-label col-form-label-sm">Bank Account Information</label>
                <select name="p_bank" id="p_bank2" class="form-control rq">
                  <option value="">------</option>
                  <?php if($banks->num_rows() > 0):?>
                    <?php foreach($banks->result_array() as $bank):?>
                      <option value="<?=$bank['bank_id']?>"><?=$bank['bank_name']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
                <small class="form-text">Bank <span class="asterisk"></span></small>
              </div>

              <div class="col-md-6">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-credit-card-alt"></i></span>
                  </div>
                  <input type="text" class="form-control card-input rq" value = "" id = "p_card_number" placeholder="1XXX XXXX XXXX X143" aria-label="Card Number" >
                </div>
                <small class="form-text">Card Number <span class="asterisk"></span></small>
              </div>

              <div class="col-md-6">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-credit-card-alt"></i></span>
                  </div>
                  <input type="text" class="form-control card-input rq" id = "p_account_number" placeholder="1XXX XXXX XXXX X143" aria-label="Account Number" >
                </div>
                <small class="form-text">Account Number <span class="asterisk"></span></small>
              </div>

            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary btn_save_payout">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/contracts/payout.js')?>"></script>
