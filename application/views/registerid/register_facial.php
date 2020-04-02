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
  .time_img:hover{
    cursor: pointer;
    border: 1px solid #72716f ;
  }

  .time_img, img{
    object-fit: 'contain';
  }
</style>
<div class="content-inner" id="pageActive" data-num="24" data-namecollapse="" data-labelname="Register Id">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/registerid_home/'.$token);?>">Register Id</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Facial Features</li>
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
                      <option value="by_dept">Department</option>
                      <option value="by_pos">Position</option>
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

                    <div id="divDept" class="filter_div" style = "display:none;">
                      <label for="Department" class="form-control-label col-form-label-sm">Department</label>
                      <select class = "form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($departments->num_rows() > 0):?>
                          <?php foreach($departments->result_array() as $dept):?>
                            <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>

                    <div id="divPos" class = "filter_div" style = "display:none;">
                      <label for="Date" class="form-control-label col-form-label-sm">Position</label>
                      <select name="" id="" class="form-control searchArea select2">
                        <option value="">------</option>
                        <?php if($positions->num_rows() > 0):?>
                          <?php foreach($positions->result_array() as $pos):?>
                            <option value="<?=$pos['positionid']?>"><?=$pos['description']?></option>
                          <?php endforeach;?>
                        <?php endif;?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 text-right">
                    <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                    <a href="<?= base_url('registerid/Register_facial/add/'.$token) ?>"><button class="btn btn-primary btnClickAddArea">Add</button></a>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table id="facial_feature_tbl" class = "table table-bordered table-striped">
                    <thead>
                      <th>Image</th>
                      <th>Employee Id</th>
                      <th>Employee Name</th>
                      <th>Department</th>
                      <th>Position</th>
                      <th>Action</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- VIEW IMAGE MODAL -->
    <div class="modal fade" id = "view_image_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Registered Facial Recognition Image</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="img-thumbnail view_image" style = "height:350px;width:100%;">

            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- DELETE MODAL -->
    <div class="modal fade" id = "delete_fr_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Delete Facial Recognition Data</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <h5>Are you sure you want to delete the Facial Recognition Data of (<span id = "del_name"></span>)</h5>
            <input type="hidden" id = "del_id">
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_del_fr">Delete</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\registerid\register_facial.js')?>"></script>
