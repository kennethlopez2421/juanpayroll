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
                <div class="form-group row p-5">
                  <div class="col-md-12 text-center mt-5 mb-5">
                    <h5 class="">Human Resource Department</h5>
                    <u><h1>Performance Evaluation</h1></u>
                  </div>
                  <!-- PART I -->
                  <div class="col-md-12 mb-3">
                    <h4>PART I: EMPLOYEE INFORMATION</h4>
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
                  <!-- PART II -->
                  <div class="col-md-12 mb-3">
                    <h4>PART II: PERFORMANCE EVALUATION</h4>
                  </div>

                  <div class="col-md-12 mb-2">
                    <h6>Please evaluate the individual’s performance based on the following factors by encircling the corresponding rating. The “Remarks” portion should be used to elaborate on any rating, if necessary otherwise indicate N/A (not applicable). The following shall be used for evaluation. </h6>
                  </div>
                  <!-- PERFORMANCE ASSESSMENT TABLE -->
                  <div class="col-md-12 mb-3 parent_div" data-section = "assessment_tbl">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center" style = "border-top:1px solid gainsboro;">
                        <thead>
                          <th class = "strong">RATING</th>
                          <th class = "strong">PERFORMANCE ASSESSMENT</th>
                          <th class = "strong">EQUIVALENT RATING</th>
                          <th class = "strong" style = "width:120px !important;">SCORE</th>
                          <th class = "strong">ACTION</th>
                        </thead>
                        <tbody>
                          <?php if($eval_ratings->num_rows() > 0):?>
                              <?php foreach($eval_ratings->result_array() as $row):?>
                                <?php if($row['eval_type'] == 'type_1'):?>
                                  <tr>
                                  <td><?=$row['rating']?></td>
                                  <td><?=$row['description']?></td>
                                  <td><?=$row['equivalent_rating']?></td>
                                  <td><?=$row['score']?></td>
                                  <td>
                                    <!-- EDIT -->
                                    <button class="btn btn-sm btn-primary btn_assess_tbl"
                                      data-action = "edit"
                                      data-id = "<?=$row['id']?>"
                                      data-rating = "<?=$row['rating']?>"
                                      data-desc = "<?=$row['description']?>"
                                      data-equivalent_rating = "<?=$row['equivalent_rating']?>"
                                      data-score = "<?=$row['score']?>"
                                    >
                                      <i class="fa fa-pencil"></i>
                                    </button>
                                    <!-- DELETE -->
                                    <button class="btn btn-sm btn-danger btn_assess_tbl"
                                      data-action = "delete"
                                      data-equivalent_rating = "<?=$row['equivalent_rating']?>"
                                      data-id = "<?=$row['id']?>"
                                    >
                                      <i class="fa fa-trash"></i>
                                    </button>
                                    <!-- ADD -->
                                    <button class="btn btn-sm btn-info btn_assess_tbl"
                                      data-action = "add"
                                      data-id = "<?=$row['id']?>"
                                    >
                                      <i class="fa fa-plus"></i>
                                    </button>
                                  </td>
                                </tr>
                                <?php endif;?>
                              <?php endforeach;?>
                          <?php else:?>
                            <tr><td class="text-center"><h1>No available Ratings.</h1></td></tr>
                          <?php endif;?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <!-- SECTIONS -->
                  <?php if($eval_sections->num_rows() > 0):?>
                    <?php foreach($eval_sections->result_array() as $row):?>
                      <!-- SECTION A - C -->
                      <?php if($row['section'] != "D" && $row['section'] != "E"):?>
                        <div class="col-md-12 mb-3">
                          <h4>
                            <?=$row['section']?>. <?=$row['title']?>
                            <!-- <button class="btn btn-sm btn-info"><i class="fa fa-plus"></i></button> -->
                          </h4>
                          <ol>
                            <?php if($eval_questions->num_rows() > 0):?>
                              <?php foreach($eval_questions->result_array() as $row2):?>
                                <?php if($row['section'] === $row2['section']):?>
                                  <li class = "mb-3">
                                    <div class="row">
                                      <div class="col-md-10">
                                        <p><strong><?=$row2['title']?></strong> - <?=$row2['description']?></p>
                                      </div>
                                      <div class="col-md-2 text.center">
                                        <select name="" id="" class="form-control text-center">
                                          <?php if($eval_ratings->num_rows() > 0):?>
                                            <?php foreach($eval_ratings->result_array() as $rate):?>
                                              <?php if($rate['eval_type'] == "type_1"):?>
                                                <option value="<?=$rate['rating']?>"><?=$rate['rating']?></option>
                                              <?php endif;?>
                                            <?php endforeach;?>
                                          <?php endif;?>
                                        </select>
                                        <div class="col-md-12 text-center p-0">
                                          <button class="btn btn-sm btn-primary sm_btn btn_assess_question"
                                            data-action = "edit"
                                            data-id = "<?=$row2['id']?>"
                                            data-section = "<?=$row2['section']?>"
                                            data-title = "<?=$row2['title']?>"
                                            data-desc = "<?=$row2['description']?>"
                                          >
                                            <i class="fa fa-pencil"></i>
                                          </button>
                                          <button class="btn btn-sm btn-danger sm_btn btn_assess_question"
                                            data-action = "delete"
                                            data-id = "<?=$row2['id']?>"
                                            data-title = "<?=$row2['title']?>"
                                          >
                                            <i class="fa fa-trash"></i>
                                          </button>
                                          <button class="btn btn-sm btn-info sm_btn btn_assess_question"
                                            data-action = "add"
                                          >
                                            <i class="fa fa-plus"></i>
                                          </button>
                                        </div>
                                      </div>
                                      <div class="col-md-10 mb-3">
                                        <strong>Remarks:</strong>
                                        <input type="text" class = "form-control">
                                      </div>
                                    </div>
                                  </li>
                                <?php else:?>
                                  <!-- <li class="text-center"><h1>No available assessment questions.</h1></li> -->
                                <?php endif;?>
                              <?php endforeach;?>
                            <?php else:?>
                              <li class="text-center"><h1>No available assessment questions.</h1></li>
                            <?php endif;?>
                          </ol>
                        </div>
                      <?php else:?>
                        <!-- SECTION D AND E -->
                        <?php if($row['section'] == "D"):?>
                          <div class="col-md-12 mb-3">
                            <h4><?=$row['section']?>. <?=$row['title']?> (Only Applicable to Managers or Above Position)</h4>
                            <div class="form-group px-4">
                              <p>Knowledge and capabilities affecting Leadership skills/style, performance, output and his/her team. </p>
                              <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center" style = "border: 1px solid gainsboro;">
                                  <thead>
                                    <th width = "80">Rating</th>
                                    <th>Equivalent Rating</th>
                                    <th width = "80">Action</th>
                                  </thead>
                                  <tbody>
                                    <?php if($eval_ratings->num_rows() > 0):?>
                                      <?php foreach($eval_ratings->result_array() as $rate):?>
                                        <?php if($rate['eval_type'] == 'type_2'):?>
                                          <tr>
                                            <td><?=$rate['rating']?></td>
                                            <td><?=$rate['description']?></td>
                                            <td>
                                              <!-- EDIT -->
                                              <button class="btn btn-sm btn-primary btn_assess_tbl"
                                              data-action = "edit"
                                              data-id = "<?=$rate['id']?>"
                                              data-rating = "<?=$rate['rating']?>"
                                              data-desc = "<?=$rate['description']?>"
                                              data-equivalent_rating = "<?=$rate['equivalent_rating']?>"
                                              data-score = "<?=$rate['score']?>"
                                              >
                                              <i class="fa fa-pencil"></i>
                                            </button>
                                            <!-- DELETE -->
                                            <button class="btn btn-sm btn-danger btn_assess_tbl"
                                            data-action = "delete"
                                            data-equivalent_rating = "<?=$rate['equivalent_rating']?>"
                                            data-id = "<?=$rate['id']?>"
                                            >
                                            <i class="fa fa-trash"></i>
                                          </button>
                                          <!-- ADD -->
                                          <button class="btn btn-sm btn-info btn_assess_tbl"
                                          data-action = "add"
                                          data-id = "<?=$rate['id']?>"
                                          >
                                          <i class="fa fa-plus"></i>
                                        </button>
                                      </td>
                                    </tr>
                                  <?php endif;?>
                                <?php endforeach;?>
                                    <?php endif;?>
                                  </tbody>
                                </table>
                              </div>
                            </div>

                            <div class="form-group px-4">
                              <?php if($eval_questions->num_rows() > 0):?>
                                <ol>
                                <div class="row">
                                <?php foreach($eval_questions->result_array() as $question):?>
                                  <?php if($question['section'] == "D"):?>
                                    <div class="col-md-6">
                                    <li class="mb-3">
                                        <div class="col-md-12 mb-3">
                                          <?=$question['title']?>
                                        </div>
                                        <div class="col-md-12">
                                          <div class="row">
                                            <div class="col-md-6">
                                              <select name="" id="" class="form-control text-center">
                                                <?php if($eval_ratings2->num_rows() > 0):?>
                                                  <?php foreach($eval_ratings2->result_array() as $rate):?>
                                                    <option value="<?=$rate['rating']?>"><?=$rate['rating']?></option>
                                                  <?php endforeach;?>
                                                <?php endif;?>
                                              </select>
                                            </div>
                                            <div class="col-md-6">
                                              <div class="col-md-12 text-center p-0">
                                                <button class="btn btn-sm btn-primary sm_btn btn_assess_question"
                                                  data-action = "edit"
                                                  data-id = "<?=$question['id']?>"
                                                  data-section = "<?=$question['section']?>"
                                                  data-title = "<?=$question['title']?>"
                                                  data-desc = "<?=$question['description']?>"
                                                >
                                                  <i class="fa fa-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger sm_btn btn_assess_question"
                                                  data-action = "delete"
                                                  data-id = "<?=$question['id']?>"
                                                  data-title = "<?=$question['title']?>"
                                                >
                                                  <i class="fa fa-trash"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info sm_btn btn_assess_question"
                                                  data-action = "add"
                                                >
                                                  <i class="fa fa-plus"></i>
                                                </button>
                                              </div>
                                            </div>
                                          </div>


                                        </div>
                                    </li>
                                    </div>
                                  <?php endif;?>
                                <?php endforeach;?>
                                </div>
                                </ol>
                              <?php endif;?>
                            </div>
                          </div>
                        <?php endif;?>
                        <?php if($row['section'] == "E"):?>
                          <div class="col-md-12 mb-3">
                          <h4><?=$row['section']?>. <?=$row['title']?></h4>
                          <div class="form-group px-4">
                            <div class="col-md-12 mb-3">
                              <h5 class = "d-inline mr-2"><strong>METHOD OF CALCULATING SCORE = <input type="type" class = "form-control col-md-4 d-inline" value = "<?=$eval_formula->formula?>"></strong></h5>
                              <button class="btn btn-sm btn-primary sm_btn d-inline btn_formula"
                                data-id = "<?=$eval_formula->id?>"
                                data-formula = "<?=$eval_formula->formula?>"
                              >
                                <i class="fa fa-pencil"></i>
                              </button>
                            </div>

                            <div class="col-md-12 mb-3">
                              <h5 class = "d-inline mr-2"><strong>METHOD OF CALCULATING SCORE = <input type="type" class = "form-control col-md-4 d-inline" value = "<?=$eval_formula2->formula?>"></strong></h5>
                              <button class="btn btn-sm btn-primary sm_btn d-inline btn_formula"
                                data-id = "<?=$eval_formula2->id?>"
                                data-formula = "<?=$eval_formula2->formula?>"
                              >
                                <i class="fa fa-pencil"></i>
                              </button>
                              <small>(Only Applicable to Managers or Above Position)</small>
                            </div>
                            <p class = "mb-4"><strong>NOTE:</strong> Each factor is given a maximum of 5 points, in the event where one or several factors have been rated “N/A”, 5 points for each factor shall be subtracted from the denominator. (e.g, if 2 factors are N/A, then deduct 10 points from 60, hence the denominator shall be 35 points only.)  </p>
                            <div class="row">
                              <div class="col-md-3 mb-">
                                <h5 class = "middle"><strong>EMPLOYEE SCORE = </strong></h5>
                              </div>
                              <div class="col-md-3 mb-3">
                                <input type="text" class="form-control">
                              </div>
                              <div class="col-md-3 mb- text-center">
                                <h5 class = "middle"><strong>  X   100   =   </strong></h5>
                              </div>
                              <div class="col-md-3 mb-">
                                <input type="text" class="form-control">
                              </div>
                              <div class="col-md-3">
                                <h5 class = "middle"><strong>EQUIVALENT RATING =</strong></h5>
                              </div>
                              <div class="col-md-3">
                                <input type="text" class="form-control">
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php endif;?>
                      <?php endif;?>
                    <?php endforeach;?>
                  <?php else:?>
                    <div class="col-md-12 mb-2 text-center">
                      <h1>No available assessment questions</h1>
                    </div>
                  <?php endif;?>
                  <!-- PURPOSE -->
                  <div class="col-md-12 mb-5">
                    <h4>PURPOSE:</h4>
                    <div class="form-group row">
                      <div class="col-md-4">
                        <select name="" id="" class="form-control">
                          <option value="">------</option>
                          <?php if($eval_purpose->num_rows() > 0):?>
                            <?php foreach($eval_purpose->result_array() as $row):?>
                              <option value="<?=$row['id']?>"><?=$row['title']?></option>
                            <?php endforeach;?>
                          <?php endif;?>
                        </select>
                      </div>

                      <div class="col-md-6">
                        <input type="text" class="form-control">
                      </div>

                      <!-- <div class="col-md-2 p-0 text-right pr-4">
                        <button class="btn btn-sm btn-primary sm_btn"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-sm btn-info sm_btn"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-sm btn-danger sm_btn"><i class="fa fa-trash"></i></button>
                      </div> -->
                    </div>
                  </div>
                  <!-- PART III -->
                  <div class="col-md-12 mb-4">
                    <h4>PART III: OVERALL RECOMMENDATION AND DEVELOPMENT</h4>
                    <?php if($eval_recommendations->num_rows() > 0):?>
                      <ol>
                        <?php foreach($eval_recommendations->result_array() as $row):?>
                          <li>
                            <?=$row['description']?>
                            <span class="float-right pr-2">
                              <button class="btn btn-sm btn-primary sm_btn btn_recommend"
                                data-action = "edit"
                                data-id = "<?=$row['id']?>"
                                data-desc = "<?=$row['description']?>"
                              >
                                <i class="fa fa-pencil"></i>
                              </button>
                              <button class="btn btn-sm btn-danger sm_btn btn_recommend"
                                data-action = "delete"
                                data-id = "<?=$row['id']?>"
                                data-desc = "<?=$row['description']?>"
                              >
                                <i class="fa fa-trash"></i>
                              </button>
                              <button class="btn btn-sm btn-info sm_btn btn_recommend"
                                data-action = "add"
                              >
                                <i class="fa fa-plus"></i>
                              </button>
                            </span>
                          </li>
                          <textarea name="name" rows="3" cols="80" class = "form-control mb-3"></textarea>
                        <?php endforeach;?>
                      </ol>
                    <?php else:?>
                      <h1 class="text-center">No available recommendations</h1>
                    <?php endif;?>
                  </div>
                  <!-- EVALUATED BY -->
                  <div class="col-md-12 mb-5">
                    <h4 class = "d-inline mr-2"><strong>EVAUATED BY :</strong></h4>
                    <input type="text" class="form-control col-md-4 textbox2 d-inline">
                  </div>
                  <!-- SELF ASSESSMENT -->
                  <div class="col-md-12 mb-5">
                    <h4 class = "d-inline">SELF ASSESSMENT</h4> <small>( Only Applicable to Managers or Above Position )</small>
                    <ol>
                      <?php if($eval_self_assessment->num_rows() > 0):?>
                        <?php foreach($eval_self_assessment->result_array() as $assessment):?>
                          <li>
                            <?=$assessment['question']?>
                            <span class="float-right pr-2">
                              <button class="btn btn-sm btn-primary sm_btn btn_assessment"
                                data-action = "edit"
                                data-id = "<?=$assessment['id']?>"
                                data-desc = "<?=$assessment['question']?>"
                              >
                                <i class="fa fa-pencil"></i>
                              </button>
                              <button class="btn btn-sm btn-danger sm_btn btn_assessment"
                                data-action = "delete"
                                data-id = "<?=$assessment['id']?>"
                                data-desc = "<?=$assessment['question']?>"
                              >
                                <i class="fa fa-trash"></i>
                              </button>
                              <button class="btn btn-sm btn-info sm_btn btn_assessment"
                                data-action = "add"
                              >
                                <i class="fa fa-plus"></i>
                              </button>
                            </span>
                          </li>
                          <textarea name="" id="" cols="30" rows="3" class="form-control mb-4"></textarea>
                        <?php endforeach;?>
                      <?php endif;?>
                    </ol>
                  </div>
                  <!-- Confirmation of Discussion -->
                  <div class="col-md-12 mb-3">
                    <h4><strong>Confirmation of Discussion</strong></h4>
                    <h5>This evaluation has been fully discussed and explained to me and I understand all the information stated in this Performance Evaluation. </h5>
                  </div>
                  <!-- Comments of Employee -->
                  <div class="col-md-12 mb-4">
                    <h5>Comments of Employee:</h5>
                    <textarea name="name" rows="3" cols="80" class = "form-control"></textarea>
                  </div>
                  <!-- NOTED BY -->
                  <div class="col-md-12 mb-4">
                    <h5 style = "font-weight:bold;">ACTIONS TAKEN BY HRD (if any) </h5>
                    <textarea name="name" rows="3" cols="80" class = "form-control mb-5"></textarea>
                    <h4 class = "d-inline mr-2"><strong>NOTED BY : </strong></h4>
                    <input type="text" class="form-control d-inline col-md-4">
                  </div>
                  <!-- APPENDIX -->
                  <div class="col-md-12 mb-3">
                    <h4><strong>Appendix</strong></h4>
                    <h6>List of Major Tasks</h6>
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped" style = "border-top: 1px solid gainsboro;">
                        <thead>
                          <th width = "70%"><strong>PROJECT / MODULES / TASKS - brief discussion and status</strong></th>
                          <th><strong>1</strong></th>
                          <th><strong>2</strong></th>
                          <th><strong>3</strong></th>
                          <th><strong>4</strong></th>
                          <th><strong>5</strong></th>
                          <th><strong>N/A</strong></th>
                        </thead>
                        <tbody>
                          <?php for($x = 1; $x <= 10 ; $x++):?>
                            <tr>
                              <td><input type="text" class = "form-control" name = "project<?=$x?>" id = "project<?=$x?>"></td>
                              <td class = "text-center"><input type = "radio" name = "task" value = "1"></td>
                              <td class = "text-center"><input type = "radio" name = "task" value = "2"></td>
                              <td class = "text-center"><input type = "radio" name = "task" value = "3"></td>
                              <td class = "text-center"><input type = "radio" name = "task" value = "4"></td>
                              <td class = "text-center"><input type = "radio" name = "task" value = "5"></td>
                              <td class = "text-center"><input type = "radio" name = "task" value = "N/A"></td>
                            </tr>
                          <?php endfor;?>
                          <tr>
                            <td colspan = "7">
                              <label for="Comments:" class="form-control-label col-form-label-sm">Comments:</label>
                              <textarea name="proj_comment" id="proj_comment" cols="30" rows="5" class = "form-control"></textarea>
                            </td>
                          </tr>
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
<script src = "<?=base_url('assets\js\evaluations\evaluations_settings.js')?>"></script>
