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
<style>
  .basic-addon1{
    width: 150px;
  }
  .custom-font{
    font-size:12px;
  }
</style>
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Cash Advance Payment History</li>
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
                      <option value="by_date">Cutoff Date</option>
                      <option value="by_amount">Amount</option>
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


                    <div id="divDate" class = "filter_div" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="text" id = "date_from" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">From</small>
                        </div>
                        <div class="col-md-6">
                          <input type="text" id = "date_to" class="form-control date_input" placeholder="Ex. yyyy-mm-dd">
                          <small class="form-text">To</small>
                        </div>
                      </div>
                    </div>

                    <div id="divAmount" class = "filter_div" style = "display:none;">
                      <label for="Amount" class="form-control-label col-form-label-sm">Amount</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="number" id = "amount_from" class="form-control">
                          <small class="form-text">From</small>
                        </div>

                        <div class="col-md-6">
                          <input type="text" id = "amount_to" class="form-control">
                          <small class="form-text">To</small>
                        </div>
                      </div>
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
                  <table class="table table-striped table-bordered" id = "ca_payment_tbl">
                    <thead>
                      <th>Employee Id</th>
                      <th>Employee Name</th>
                      <th>Cutoff Date</th>
                      <th>Payment</th>
                      <th width = "190">Action</th>
                      <!-- <th>Remaining Balance</th> -->
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id = "ca_payment_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Cash Advance Informations</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row mb-4">
              <div class="col-md-12">
                <label for="Cuttoff Date" class="form-control-label col-form-label-sm">Cuttoff Date</label>
                <input type="text" id = "ca_cutoff" class="form-control" readonly>
              </div>

              <div class="col-md-12">
                <label for="Name:" class="form-control-label col-form-label-sm">Name:</label>
                <input type="text" id = "ca_emp_name" class="form-control" readonly>
              </div>

              <div class="col-md-12">
                <label for="Reason" class="form-control-label col-form-label-sm">Reason</label>
                <textarea name="" id="ca_reason" cols="30" rows="3" class="form-control" readonly></textarea>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-lg-12 text-right">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text basic-addon1"><small>Total Cash Advance</small></span>
                  </div>
                  <input type="text" id = "ca_total" class="form-control text-right" placeholder="0.00" aria-label="0.00" aria-describedby="basic-addon1" readonly>
                </div>
              </div>

              <div class="col-lg-12 text-right">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text basic-addon1"><small>Cash Advance Payment</small></span>
                  </div>
                  <input type="text" id = "ca_payment" class="form-control text-right" placeholder="0.00" aria-label="0.00" aria-describedby="basic-addon1" readonly>
                </div>
              </div>

              <div class="col-lg-12 text-right">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text basic-addon1"><small>Remaining Balance</small></span>
                  </div>
                  <input type="text" id = "ca_balance" class="form-control text-right" placeholder="0.00" aria-label="0.00" aria-describedby="basic-addon1" readonly>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/transactions/cashadvance_payment_history.js')?>"></script>
