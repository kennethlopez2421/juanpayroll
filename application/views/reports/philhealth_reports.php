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
<div class="content-inner" id="pageActive" data-num="10" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Philhealth Reports</li>
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
                     <option value="by_month">Month</option>
                   </select>
                 </div>

                 <div class="col-md-5">
                   <div id="divMonth" class = "filter_div active">
                     <label for="Month" class="form-control-label col-form-label-sm">Month</label>
                     <input id = "month" type="text" class="form-control past_month_picker" placeholder="mm-yyyy">
                   </div>
                 </div>
                 <div class="col-md-4 text-right">
                   <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button>
                   <button id = "btnExport" class="btn btn-sm btn-primary">Export to Excel</button>
                 </div>
               </div>

               <div class="form-group row">
                 <div class="col-md-3">
                   <select name="" id="filter_by" class="form-control">
                     <option value="">------</option>
                     <option value="by_id">Employee Id</option>
                     <option value="by_name">Employee Name</option>
                     <option value="by_dept">Department</option>
                     <option value="by_company">Company</option>
                   </select>
                 </div>

                 <div class="col-md-5">
                   <div id="divEmpty" class = "filter_div2 active">

                   </div>

                   <div id="divEmpID" class = "filter_div2" style="display:none;">
                     <input type="text" class="form-control searchArea" placeholder="Employee Id">
                   </div>

                   <div id="divName" class = "filter_div2" style = "display:none;">
                     <input type="text" class="form-control searchArea" placeholder="Ex. John Doe">
                   </div>

                   <div id="divDept" class="filter_div2" style = "display:none;">
                     <select class = "form-control searchArea select2">
                       <option value="">------</option>
                       <?php if($departments->num_rows() > 0):?>
                         <?php foreach($departments->result_array() as $dept):?>
                           <option value="<?=$dept['departmentid']?>"><?=$dept['description']?></option>
                         <?php endforeach;?>
                       <?php endif?>
                     </select>
                   </div>

                   <div id="divCompany" class="filter_div2" style = "display:none;">
                     <select class = "form-control searchArea select2">
                       <option value="">------</option>
                       <?php if($companies->num_rows() > 0):?>
                         <?php foreach($companies->result_array() as $company):?>
                           <option value="<?=$company['id']?>"><?=$company['company']?></option>
                         <?php endforeach;?>
                       <?php endif;?>
                     </select>
                   </div>

                 </div>
                 <div class="col-md-4 text-right">

                 </div>
               </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered" id = "philhealth_reports_tbl">
                    <thead>
                      <th>Philhealth No.</th>
                      <th>Employee Id</th>
                      <th>Employee Name</th>
                      <th>Date</th>
                      <th>Company</th>
                      <th>Department</th>
                      <th>EE</th>
                      <th>ER</th>
                      <th>Total</th>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\reports\philhealth_reports.js')?>"></script>
