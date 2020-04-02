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
  .underline{
    border-bottom: 1px solid #222;
  }
  .strong{
    font-weight: bold;
  }
  th, td{
    vertical-align: middle !important;
  }
  .middle{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  },
  .textbox2{
    border: 0 !important;
    outline: 0 !important;
    background: transparent !important;
    border-bottom: 1px solid black !important;
    /* border-bottom: 1px solid gainsboro !important; */
  }
  .sm_btn{
    padding: .7rem 0.7rem !important;
    margin: 5px 0px;
  }
</style>
<div class="content-inner" id="pageActive" data-num="23" data-namecollapse="" data-labelname="Evaluations">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/evaluations_home/'.$token);?>">Evaluations</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Evaluation Settings</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body p-5">
                <div class="form-group row">
                  <div class="col-12 text-center mb-5">
                    <h2>Human Resource Department</h2>
                    <h2>Performance Evaluations</h2>
                  </div>
                  <div class="col-md-12 mb-4">
                    <h2>Part I: Employee Information</h2>
                  </div>
                  <!-- EMPLOYEE INFORMATION -->
                  <div class="col-md-6 mb-5">
                    <div class="row">
                      <div class="col-md-4">
                        <h5>Name of Employee:</h5>
                        <h5>Position:</h5>
                        <h5>Period of Time in current position:</h5>
                        <h5>Evaluation Date:</h5>
                      </div>
                      <div class="col-md-8">
                        <h5 class = "underline">John Doe</h5>
                        <h5 class = "underline">Sofware Developer</h5>
                        <h5 class = "underline">1 Year</h5>
                        <h5 class = "underline">June 18, 2019</h5>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6 mb-5">
                    <div class="row">
                      <div class="col-md-4">
                        <h5>Department:</h5>
                        <h5>Date Hired:</h5>
                        <h5>Employee Tenure:</h5>
                        <h5>Covered Period:</h5>
                      </div>

                      <div class="col-md-8">
                        <h5 class="underline">IT Department</h5>
                        <h5 class="underline">May 7, 2018</h5>
                        <h5 class="underline">1</h5>
                        <h5 class="underline">May 7, 2018 - June 18, 2019</h5>
                      </div>
                    </div>
                  </div>
                  <!-- INSTRUCTION -->
                  <div class="col-12 mb-4">
                    <h2 class = "mb-3">Instruction</h2>
                    <h5>Evaluate the individual’s performance based on the following factors by encircling the corresponding rating. In the space provided, brief notes, observations and comments can be written or attached objective evidence (documentation) to serve as reminders during the performance discussion.</h5>
                    <h5> Use a current job description and/or rate the person’s level of performance on the assigned task given.</h5>
                  </div>
                  <!-- PERFORMACE ASSESMENT -->
                  <div class="col-md-12 mb-3">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center" style = "border-top:1px solid gainsboro;">
                        <thead>
                          <th class = "strong" style = "width:120px;">Rating</th>
                          <th class = "strong">Performance Assessment</th>
                          <th class = "strong">Equivalent Rating</th>
                          <th class = "strong">Action</td>
                        </thead>
                        <tbody>
                          <?php if($eval_ratings->num_rows() > 0):?>
                            <?php foreach($eval_ratings->result_array() as $ratings):?>
                              <?php if($ratings['eval_type'] == 'type_1'):?>
                                <tr>
                                  <td class = "strong"><?=$ratings['rating_2']?></td>
                                  <td><?=$ratings['description']?></td>
                                  <td><?=$ratings['equivalent_rating']?></td>
                                  <td>
                                    <!-- EDIT -->
                                    <button class="btn btn-sm btn-primary btn_assess_tbl"
                                      data-action = "edit"
                                      data-id = "<?=$ratings['id']?>"
                                      data-rating = "<?=$ratings['rating_2']?>"
                                      data-desc = "<?=$ratings['description']?>"
                                      data-equivalent_rating = "<?=$ratings['equivalent_rating']?>"
                                    >
                                      <i class="fa fa-pencil"></i>
                                    </button>
                                    <!-- DELETE -->
                                    <button class="btn btn-sm btn-danger btn_assess_tbl"
                                      data-action = "delete"
                                      data-equivalent_rating = "<?=$ratings['equivalent_rating']?>"
                                      data-id = "<?=$ratings['id']?>"
                                    >
                                      <i class="fa fa-trash"></i>
                                    </button>
                                    <!-- ADD -->
                                    <button class="btn btn-sm btn-info btn_assess_tbl"
                                      data-action = "add"
                                      data-id = "<?=$ratings['id']?>"
                                    >
                                      <i class="fa fa-plus"></i>
                                    </button>
                                  </td>
                                </tr>
                              <?php endif;?>
                            <?php endforeach;?>
                          <?php else:?>
                            <tr><td colspan = "4">No available ratings</td></tr>
                          <?php endif;?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- SCORING GUIDE -->
                  <div class="col-12">
                    <h3>Scoring Guide</h3>
                  </div>
                  <div class="col-md-12 mb-3">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center" style = "border-top:1px solid gainsboro;">
                        <thead>
                          <th class = "strong"> <= 5 </th>
                          <th class = "strong"> <= 4 </th>
                          <th class = "strong"> <= 3 </th>
                          <th class = "strong"> <= 2 </th>
                          <th class = "strong"> <= 1 </th>
                        </thead>
                        <tbody>
                          <tr>
                            <td> 100 % </td>
                            <td> 90 % </td>
                            <td> 80 % </td>
                            <td> 70 % </td>
                            <td> < 60 % </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- PART II: Performance Evaluation  -->
                  <!-- SECTION A -->
                  <div class="col-md-12 mb-3">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center" style = "border-top:1px solid gainsboro;">
                        <thead>
                          <th class="strong text-left">A. Technical Factors (80)</th>
                          <th class="strong">Weights</th>
                          <th class="strong">Rating</th>
                          <th class="strong">Weighted Score</th>
                        </thead>
                        <tbody>
                          <?php $sec_a = $eval_questions;?>
                          <?php if($sec_a->num_rows() > 0):?>
                            <?php foreach($sec_a->result_array() as $quest):?>
                              <?php if($quest['template'] == 2 && ($quest['section'] == 'A' || $quest['section'] == 'C')):?>
                                <tr>
                                  <td>
                                    <h4 ><strong><?=$quest['title']?></strong></h4>
                                    <?=$quest['description']?>
                                  </td>
                                  <td rowspan = "2"><?=$quest['weights']?> %</td>
                                  <td rowspan = "2" style = "width:120px;"><input type="text" class = "form-control number-input-3"></td>
                                  <td rowspan = "2" style = "width:120px !important;"><input type="text" class = "form-control" readonly></td>
                                </tr>
                                <tr>
                                  <td>
                                    <h5 style = "text-align:left;font-weight: bold;">Remarks:</h5>
                                    <input type="text" class="form-control">
                                  </td>
                                </tr>
                              <?php endif;?>
                            <?php endforeach;?>
                          <?php else:?>
                            <tr><td>No available questions</td></tr>
                          <?php endif;?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- SECTION B -->
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center" style = "border-top:1px solid gainsboro;">
                        <thead>
                          <tr>
                            <th class = "strong text-left" colspan = "5">
                              B. VALUES OF THE PERSON (Leadership, Job Knowledge, Skills, Work Attitude )
                            </th>
                          </tr>
                          <tr>
                            <th class = "strong">5 - Models the way</th>
                            <th class = "strong">4- Always exhibits competency</th>
                            <th class = "strong">3- Exhibits competency most of the time</th>
                            <th class = "strong">2- Exhibits competency half of the time or occasionally</th>
                            <th class = "strong">1 - Does not exhibit competency</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center" style = "border-top:1px solid gainsboro;">
                        <thead>
                          <th class = "strong text-left">CORE VALUES (20%)</th>
                          <th class = "strong">Weights</th>
                          <th class = "strong">Rating</th>
                          <th class = "strong">Weighted Score</th>
                        </thead>
                        <tbody>
                          <?php $sec_b = $eval_questions;?>
                          <?php if($sec_b->num_rows() > 0):?>
                            <?php foreach($sec_b->result_array() as $quest2):?>
                              <?php if($quest2['template'] == 2 && $quest2['section'] == 'B'):?>
                                <tr>
                                  <td>
                                    <h4 ><strong><?=$quest2['title']?></strong></h4>
                                    <?=$quest2['description']?>
                                  </td>
                                  <td rowspan = "2"><?=$quest2['weights']?> %</td>
                                  <td rowspan = "2" style = "width:120px;"><input type="text" class = "form-control number-input-3"></td>
                                  <td rowspan = "2" style = "width:120px !important;"><input type="text" class = "form-control" readonly></td>
                                </tr>
                                <tr>
                                  <td>
                                    <h5 style = "text-align:left;font-weight: bold;">Remarks:</h5>
                                    <input type="text" class="form-control">
                                  </td>
                                </tr>
                              <?php endif;?>
                            <?php endforeach;?>
                          <?php endif;?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ASSESSMENT TABLE MODAL -->
    <div class="modal fade" id = "assessment_tbl_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <!-- ADD SECTION  -->
            <div class="form-group row sections" id = "add_section" style = "display:none;">
              <div class="col-12">
                <label for="Rating" class="form-control-label col-form-label-sm">Rating <span class="asterisk"></span></label>
                <input type="number" id = "add_rating" name = "add_rating" class = "form-control rq_add">
              </div>
              <div class="col-md-12">
                <label for="Description" class="form-control-label col-form-label-sm">Description <span class="asterisk"></span></label>
                <textarea name="add_desc" id = "add_desc" rows="5" cols="80" class = "form-control rq_add"></textarea>
              </div>
              <div class="col-md-6">
                <label for="Equivalent Rating" class="form-control-label col-form-label-sm">Equivalent Rating <span class="asterisk"></span></label>
                <input type="text" id = "add_equivalent_rating" name = "add_equivalent_rating" class="form-control rq_add">
              </div>
              <div class="col-md-6">
                <label for="Score" class="form-control-label col-form-label-sm">Score <span class="asterisk"></span></label>
                <input type="text" id = "add_score" name = "add_score" class="form-control rq_add">
              </div>
            </div>
            <!-- EDIT SECTION -->
            <div class="form-group row sections" id = "edit_section" style = "display:none;">
              <div class="col-12">
                <label for="Rating" class="form-control-label col-form-label-sm">Rating <span class="asterisk"></span></label>
                <input type="number" id = "edit_rating" name = "edit_rating" class = "form-control rq_edit">
                <input type="hidden" id = "edit_id" name = "edit_id">
              </div>
              <div class="col-md-12">
                <label for="Description" class="form-control-label col-form-label-sm">Description <span class="asterisk"></span></label>
                <textarea name="edit_desc" id = "edit_desc" rows="5" cols="80" class = "form-control rq_edit"></textarea>
              </div>
              <div class="col-md-6">
                <label for="Equivalent Rating" class="form-control-label col-form-label-sm">Equivalent Rating <span class="asterisk"></span></label>
                <input type="text" id = "edit_equivalent_rating" name = "edit_equivalent_rating" class="form-control rq_edit">
              </div>
              <div class="col-md-6">
                <label for="Score" class="form-control-label col-form-label-sm">Score <span class="asterisk"></span></label>
                <input type="text" id = "edit_score" name = "edit_score" class="form-control rq_edit">
              </div>
            </div>
            <!-- DELETE SECTION  -->
            <div class="form-group row sections px-5" id = "delete_section" style = "display:none;">
              <h4>Are you sure you want to delete this ( <span id="delete_item"></span> )?</h4>
            </div>

          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_save">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ASSESSMENT QUESTIONS MODAL -->
    <div class="modal fade" id = "assessment_questions_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <!-- ADD -->
            <div class="form-group row sections2" id = "add_sections2" style = "display:none;">
              <div class="col-md-6">
                <label for="Title" class="form-control-label col-form-label-sm">Title: <span class="asterisk"></span></label>
                <input type="text" id = "add_title2"  name = "add_title" class="form-control rq2_add">
              </div>
              <div class="col-md-6">
                <label for="Section" class="form-control-label col-form-label-sm">Section: <span class="asterisk"></span></label>
                <select name="add_section" id="add_section2" class="form-control rq2_add">
                  <option value="">------</option>
                  <?php if($eval_sections->num_rows() > 0):?>
                    <?php foreach($eval_sections->result_array() as $row):?>
                      <option value="<?=$row['section']?>"><?=$row['title']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
              <div class="col-md-12">
                <label for="Description" class="form-control-label col-form-label-sm">Description: <span class="asterisk"></span></label>
                <textarea name="name" id = "add_desc2" rows="5" cols="80" class = "form-control rq2_add"></textarea>
              </div>
            </div>
            <!-- EDIT -->
            <div class="form-group row sections2" id = "edit_sections2" style = "display:none;">
              <div class="col-md-6">
                <label for="Title" class="form-control-label col-form-label-sm">Title: <span class="asterisk"></span></label>
                <input type="text" id = "edit_title2"  name = "edit_title" class="form-control rq2_edit">
                <input type="hidden" id = "edit_id2" name = "edit_id" class = "form-control">
              </div>
              <div class="col-md-6">
                <label for="Section" class="form-control-label col-form-label-sm">Section: <span class="asterisk"></span></label>
                <select name="edit_section" id="edit_section2" class="form-control rq2_edit">
                  <option value="">------</option>
                  <?php if($eval_sections->num_rows() > 0):?>
                    <?php foreach($eval_sections->result_array() as $row):?>
                      <option value="<?=$row['section']?>"><?=$row['title']?></option>
                    <?php endforeach;?>
                  <?php endif;?>
                </select>
              </div>
              <div class="col-md-12">
                <label for="Description" class="form-control-label col-form-label-sm">Description: <span class="asterisk"></span></label>
                <textarea name="name" id = "edit_desc2" rows="5" cols="80" class = "form-control rq2_edit"></textarea>
              </div>
            </div>
            <!-- DELETE SECTION  -->
            <div class="form-group row sections2 px-5" id = "delete_sections2" style = "display:none;">
              <h4>Are you sure you want to delete this ( <span id="delete_item2"></span> )?</h4>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_save2">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- RECOMMENDATIONS AND DEVELOPMENT MODAL -->
    <div class="modal fade" id = "recommend_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <!-- ADD -->
            <div class="form-group row sections3" id = "add_sections3" style = "display:none;">
              <div class="col-md-12">
                <label for="Description:" class="form-control-label col-form-label-sm">Description:</label>
                <textarea name="add_desc3" id = "add_desc3" rows="5" cols="80" class = "form-control rq3_add"></textarea>
              </div>
            </div>
            <!-- EDIT -->
            <div class="form-group row sections3" id = "edit_sections3" style = "display:none;">
              <div class="col-md-12">
                <label for="Description:" class="form-control-label col-form-label-sm">Description:</label>
                <textarea name="edit_desc3" id = "edit_desc3" rows="5" cols="80" class = "form-control rq3_edit"></textarea>
                <input type="hidden" id = "edit_id3">
              </div>
            </div>
            <!-- DELETE SECTION  -->
            <div class="form-group row sections3 px-5" id = "delete_sections3" style = "display:none;">
              <h4>Are you sure you want to delete this ( <span id="delete_item3"></span> )?</h4>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_save3">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- SELF ASSESSMENT MODAL -->
    <div class="modal fade" id = "assessment_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <!-- ADD -->
            <div class="form-group row sections4" id = "add_sections4" style = "display:none;">
              <div class="col-md-12">
                <label for="Description:" class="form-control-label col-form-label-sm">Description:</label>
                <textarea name="add_desc4" id = "add_desc4" rows="5" cols="80" class = "form-control rq4_add"></textarea>
              </div>
            </div>
            <!-- EDIT -->
            <div class="form-group row sections4" id = "edit_sections4" style = "display:none;">
              <div class="col-md-12">
                <label for="Description:" class="form-control-label col-form-label-sm">Description:</label>
                <textarea name="edit_desc4" id = "edit_desc4" rows="5" cols="80" class = "form-control rq4_edit"></textarea>
                <input type="hidden" id = "edit_id4">
              </div>
            </div>
            <!-- DELETE SECTION  -->
            <div class="form-group row sections4 px-5" id = "delete_sections4" style = "display:none;">
              <h4>Are you sure you want to delete this ( <span id="delete_item4"></span> )?</h4>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_save4">Save</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- EVALUATION FORMULA MODAL -->
    <div class="modal fade" id = "eval_formula_modal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Evaluation Formula</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-12">
                <label for="Formula:" class="form-control-label col-form-label-sm">Formula: <span class="asterisk"></span></label>
                <input type="text" id = "eval_formula" name = "eval_formula" class="form-control">
              </div>
            </div>
          </div>
          <div class="modal-footer text-right">
            <button class="btn btn-sm btn-primary" id = "btn_update_formula">Update</button>
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>


<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\evaluations\evaluations_settings_2.js')?>"></script>
