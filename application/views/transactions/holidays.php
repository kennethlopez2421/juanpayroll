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
<div class="content-inner" id="pageActive" data-num="14" data-namecollapse="" data-labelname="Transactions">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/transaction_home/'.$token);?>">Transactions</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Holidays</li>
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
                      <option value="by_desc">Description</option>
                      <option value="by_htype">Holiday Type</option>
                      <option value="by_date">Date</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <div id="divDesc" class = "filter_div active">
                      <label for="Description" class="form-control-label col-form-label-sm">Description</label>
                      <input type="text" class="form-control searchArea" value = "">
                    </div>

                    <div id="divHtype" class = "filter_div" style = "display:none;">
                      <label for="Holiday Type" class="form-control-label col-form-label-sm searchArea">Holiday Type</label>
                      <select name="h_type_filter" class="form-control searchArea" id = "h_type_filter">
                        <!-- <option value="">------</option> -->
                        <?php if($holiday_type->num_rows() > 0):?>
                          <?php foreach($holiday_type->result() as $ho):?>
                            <option value="<?=$ho->holidaytypeid?>"><?=$ho->description?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divDate" class="filter_div" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                      <input type="text" class="form-control date_input searchArea">
                    </div>

                  </div>
                  <div class="col-md-3 offset-md-2 text-right">
                    <button class="btn btn-sm btn-primary" id = "btn_add_holiday_modal">Add</button>
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped" id = "holiday_tran_tbl">
                    <thead>
                      <th>Description</th>
                      <th>Holiday Type</th>
                      <th>Date</th>
                      <th width = "190">Action</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id = "add_holiday_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New Holiday</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="Description" class="form-control-label col-form-label-sm">
                Description <span class="asterisk"></span>
              </label>
              <input type="text" name = "holiday_desc" id = "holiday_desc" class = "form-control rq">
            </div>

            <div class="form-group">
              <label for="Holiday Type" class="form-control-label col-form-label-sm">
                Holiday Type <span class="asterisk"></span>
              </label>
              <select name="holiday_type" id="holiday_type" class="form-control rq">
                <option value="">------</option>
                <?php if($holiday_type->num_rows() > 0):?>
                  <?php foreach($holiday_type->result() as $ho):?>
                    <option value="<?=$ho->holidaytypeid?>"><?=$ho->description?></option>
                  <?php endforeach;?>
                <?php endif;?>
              </select>
            </div>

            <div class="form-group">
              <label for="Date" class="form-control-label col-form-label-sm">
                Date <span class="asterisk"></span>
              </label>
              <input type="text" class="form-control date_input rq" name = "holiday_date" id = "holiday_date">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_save_holiday">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "edit_holiday_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Edit Holiday</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="Description" class="form-control-label col-form-label-sm">
                Description <span class="asterisk"></span>
              </label>
              <input type="text" name = "holiday_desc" id = "edit_holiday_desc" class = "form-control rq2">
              <input type="hidden" id = "current_h_desc">
              <input type="hidden" id = "current_h_date">
            </div>

            <div class="form-group">
              <label for="Holiday Type" class="form-control-label col-form-label-sm">
                Holiday Type <span class="asterisk"></span>
              </label>
              <select name="holiday_type" id="edit_holiday_type" class="form-control rq2">
                <option value="">------</option>
                <?php if($holiday_type->num_rows() > 0):?>
                  <?php foreach($holiday_type->result() as $ho):?>
                    <option value="<?=$ho->holidaytypeid?>"><?=$ho->description?></option>
                  <?php endforeach;?>
                <?php endif;?>
              </select>
            </div>

            <div class="form-group">
              <label for="Date" class="form-control-label col-form-label-sm">
                Date <span class="asterisk"></span>
              </label>
              <input type="text" class="form-control date_input rq2" name = "holiday_date" id = "edit_holiday_date">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_update_holiday">Update</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id = "del_holiday_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Delete Holiday</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-lg-12">
                <p>Are you sure you want to delete this record (<bold class="info_desc"></bold>) ?</p>
                <input type="hidden" class="employeeid">
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_yes">Yes</button>
            <button class="btn blue-grey" data-dismiss = "modal">No</button>
          </div>
        </div>
      </div>
    </div>

<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/js/transactions/holidays.js')?>"></script>
