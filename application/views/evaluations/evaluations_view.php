
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
  .certified{
    pointer-events: none;
  }
  .evaluated{
    pointer-events: none;
  }
  .ongoing{
    pointer-events: none;
  }
  .input {
    display: table-cell;
    vertical-align: middle
  }
  @media print {
  #print_div{page-break-after: always;}
  }
</style>
<div class="content-inner" id="pageActive" data-num="23" data-namecollapse="" data-labelname="Evaluations">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/evaluations_home/'.$token);?>">Evaluations</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('evaluations/Evaluations/index/'.$token)?>">Evaluations History</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Evaluations</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section id = "print_div" class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body p-5 <?=($eval_status == 'certified') ? "certified": ""?>">
                <?php if($evaluations->num_rows() > 0):?>
                  <?php $eval = $evaluations->row_array();?>
                  <?php $quests = json_decode($eval['eval_remarks']);?>
                  <?php $recommends = json_decode($eval['eval_recommendations']);?>
                  <?php $eval_assessment = json_decode($eval['eval_assessment']);?>
                  <!-- <?php print_r($eval_assessment);?> -->
                  <input type="hidden" id = "eval_type" value = "<?=$eval['eval_type']?>">
                  <div class="form-group row p-5" id = "eval_form">
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
                          <h5 class = "underline"><?=$eval['fullname']?></h5>
                          <h5 class = "underline"><?=$eval['position']?></h5>
                          <h5 class = "underline"><?=$current_pos_stay?></h5>
                          <h5 class = "underline"><?=$eval['eval_date']?></h5>
                          <input type="hidden" id = "eval_id" name = "eval_id" value = "<?=$eval['eval_id']?>">
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
                          <h5 class="underline"><?=$eval['department']?></h5>
                          <h5 class="underline"><?=$eval['date_hired']?></h5>
                          <h5 class="underline"><?=$eval['date_diff']?></h5>
                          <h5 class="underline"><?=$eval['covered_period']?></h5>
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
                          <div class="col-md-12 mb-3 <?=($eval_status != 'ongoing') ? "evaluated" : ""?>">
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
                                          <select name="eval_quest[]" id="eval_quest<?=$row2['id']?>" data-id = "<?=$row2['id']?>" class="eval_quest form-control text-center rq">
                                            <option value="">------</option>
                                            <?php if($eval_ratings->num_rows() > 0):?>
                                              <?php foreach($eval_ratings->result_array() as $rate):?>
                                                <?php if($rate['eval_type'] == 'type_1'):?>
                                                  <?php if(count((array)$quests) > 0):?>
                                                    <?php foreach($quests as $quest):?>
                                                      <?php if($quest->id == $row2['id']):?>
                                                        <option value="<?=$rate['rating']?>" <?=($quest->value == $rate['rating']) ? "SELECTED" : ""?>><?=$rate['rating']?></option>
                                                      <?php else:?>
                                                        <option value="<?=$rate['rating']?>"><?=$rate['rating']?></option>
                                                      <?php endif;?>
                                                    <?php endforeach;?>
                                                  <?php else:?>
                                                    <option value="<?=$rate['rating']?>"><?=$rate['rating']?></option>
                                                  <?php endif;?>
                                                <?php endif;?>
                                              <?php endforeach;?>
                                            <?php endif;?>
                                          </select>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                          <strong>Remarks:</strong>
                                          <?php if(count((array)$quests) > 0):?>
                                            <?php foreach($quests as $quest):?>
                                              <?php if($quest->id == $row2['id']):?>
                                                <input type="text" id = "remark<?=$row2['id']?>" data-id = "<?=$row2['id']?>" name = "remarks[]" class = "eval_remarks form-control" value = "<?=$quest->remarks?>">
                                              <?php endif;?>
                                            <?php endforeach;?>
                                          <?php else:?>
                                            <input type="text" id = "remark<?=$row2['id']?>" data-id = "<?=$row2['id']?>" name = "remarks[]" class = "eval_remarks form-control">
                                          <?php endif;?>
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
                          <!-- SECTION D -->
                          <?php if($row['section'] == "D"):?>
                            <?php if($eval['eval_type'] == 'type_2'):?>
                              <div class="col-md-12 mb-3 <?=($eval_status != 'ongoing') ? "evaluated" : ""?>">
                                <h4><?=$row['section']?>. <?=$row['title']?> </h4>
                                <div class="form-group px-4">
                                  <p>Knowledge and capabilities affecting Leadership skills/style, performance, output and his/her team. </p>
                                  <div class="table-responsive">
                                    <table class="table table-striped table-bordered text-center" style = "border: 1px solid gainsboro;">
                                      <thead>
                                        <th width = "80">Rating</th>
                                        <th>Equivalent Rating</th>
                                      </thead>
                                      <tbody>
                                        <?php if($eval_ratings->num_rows() > 0):?>
                                          <?php foreach($eval_ratings->result_array() as $rate):?>
                                            <?php if($rate['eval_type'] == 'type_2'):?>
                                              <tr>
                                                <td><?=$rate['rating']?></td>
                                                <td><?=$rate['description']?></td>
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
                                                  <select name="eval_questions[]" id="eval_questions<?=$question['id']?>" data-id = "<?=$question['id']?>" class="form-control text-center eval_quest">
                                                    <option value="">------</option>
                                                    <?php if($eval_ratings2->num_rows() > 0):?>
                                                      <?php foreach($eval_ratings2->result_array() as $rate):?>
                                                        <?php if(count((array)$quests) > 0 ):?>
                                                          <?php foreach($quests as $quest):?>
                                                            <?php if($quest->id == $question['id']):?>
                                                              <option value="<?=$rate['rating']?>" <?=($quest->value == $rate['rating']) ? "SELECTED" : ""?>><?=$rate['rating']?></option>
                                                            <?php else:?>
                                                              <option value="<?=$rate['rating']?>"><?=$rate['rating']?></option>
                                                            <?php endif;?>
                                                          <?php endforeach;?>
                                                        <?php else:?>
                                                          <option value="<?=$rate['rating']?>"><?=$rate['rating']?></option>
                                                        <?php endif;?>
                                                      <?php endforeach;?>
                                                    <?php endif;?>
                                                  </select>
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
                          <?php endif;?>
                          <?php if($row['section'] == "E"):?>
                            <div class="col-md-12 mb-3">
                              <h4><?=$row['section']?>. <?=$row['title']?></h4>
                              <div class="form-group px-4">
                                <?php if($eval['eval_type'] == 'type_1'):?>
                                  <h5 class = "d-inline mr-2"><strong>METHOD OF CALCULATING SCORE = <input type="type" id = "eval_formula" name = "eval_formula" class = "form-control col-md-4 d-inline" value = "<?=$eval_formula->formula?>" readonly></strong></h5>
                                <?php else:?>
                                  <h5 class = "d-inline mr-2"><strong>METHOD OF CALCULATING SCORE = <input type="type" id = "eval_formula" name = "eval_formula" class = "form-control col-md-4 d-inline" value = "<?=$eval_formula2->formula?>" readonly></strong></h5>
                                <?php endif;?>
                                <p class = "mb-4"><strong>NOTE:</strong> Each factor is given a maximum of 5 points, in the event where one or several factors have been rated “N/A”, 5 points for each factor shall be subtracted from the denominator. (e.g, if 2 factors are N/A, then deduct 10 points from 60, hence the denominator shall be 35 points only.)  </p>
                                <div class="row">
                                  <div class="col-md-3 mb-">
                                    <h5 class = "middle"><strong>EMPLOYEE SCORE = </strong></h5>
                                  </div>
                                  <div class="col-md-3 mb-3">
                                    <input type="text" id = "eval_total_score" class="form-control" value = "<?=$eval['eval_score']?>" readonly>
                                  </div>
                                  <div class="col-md-3 mb- text-center">
                                    <h5 class = "middle"><strong>  X   100   =   </strong></h5>
                                  </div>
                                  <div class="col-md-3 mb-">
                                    <input type="text" id = "eval_score_percent" class="form-control" value = "<?=$eval['eval_score_percent']?>" readonly>
                                  </div>
                                  <div class="col-md-3">
                                    <h5 class = "middle"><strong>EQUIVALENT RATING =</strong></h5>
                                  </div>
                                  <div class="col-md-6">
                                    <input type="text" id = "eval_equivalent_rate" class="form-control" value = "<?=$eval['eval_equivalent_rate']?>" readonly>
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
                          <select name="purpose" id="purpose" class="form-control rq">
                            <option value="">------</option>
                            <?php if($eval_purpose->num_rows() > 0):?>
                              <?php foreach($eval_purpose->result_array() as $row):?>
                                <option value="<?=$row['id']?>" <?=($eval['eval_purpose_type'] == $row['id'])? "SELECTED" : ""?>><?=$row['title']?></option>
                              <?php endforeach;?>
                            <?php endif;?>
                          </select>
                        </div>

                        <div class="col-md-6">
                          <input type="text" id = "purpose_value" value = "<?=$eval['eval_purpose']?>" name = "purpose_value" class="form-control rq">
                        </div>

                        <!-- <div class="col-md-2 p-0 text-right pr-4">
                          <button class="btn btn-sm btn-primary sm_btn"><i class="fa fa-pencil"></i></button>
                          <button class="btn btn-sm btn-info sm_btn"><i class="fa fa-plus"></i></button>
                          <button class="btn btn-sm btn-danger sm_btn"><i class="fa fa-trash"></i></button>
                        </div> -->
                      </div>
                    </div>
                    <!-- PART III -->
                    <div class="col-md-12 mb-4 <?=($eval_status != 'ongoing') ? "evaluated" : ""?>">
                      <h4>PART III: OVERALL RECOMMENDATION AND DEVELOPMENT</h4>
                      <?php if($eval_recommendations->num_rows() > 0):?>
                        <ol>
                          <?php foreach($eval_recommendations->result_array() as $row):?>
                            <li>
                              <?=$row['description']?>
                            </li>
                            <?php if(count((array)$recommends) > 0):?>
                              <?php foreach($recommends as $recom):?>
                                <?php if($recom->id == $row['id']):?>
                                  <textarea name="eval_recommend[]" id = "recommend<?=$row['id']?>" data-id = "<?=$row['id']?>" rows="3" cols="80" class = "eval_recommend form-control mb-3 rq"><?=$recom->value?></textarea>
                                <?php endif;?>
                              <?php endforeach;?>
                            <?php else:?>
                              <textarea name="eval_recommend[]" id = "recommend<?=$row['id']?>" data-id = "<?=$row['id']?>" rows="3" cols="80" class = "eval_recommend form-control mb-3 rq"></textarea>
                            <?php endif;?>

                          <?php endforeach;?>
                        </ol>
                      <?php else:?>
                        <h1 class="text-center">No available recommendations</h1>
                      <?php endif;?>
                    </div>
                    <!-- EVALUATED BY -->
                    <div class="col-md-12 mb-5 <?=($eval_status != 'ongoing') ? "evaluated" : ""?>">
                      <h4 class = "d-inline mr-2"><strong>EVAUATED BY :</strong></h4>
                      <input type="text" id = "evaluator" name = "evaluator" class="form-control col-md-4 textbox2 d-inline" value = "<?=$eval['evaluator']?>" readonly>
                    </div>
                    <!-- SELF ASSESSMENT -->
                    <?php if($eval['eval_type'] == 'type_2'):?>
                      <div class="col-md-12 mb-5 <?=($eval_status != 'ongoing') ? "evaluated" : ""?>">
                        <h4 class = "d-inline">SELF ASSESSMENT</h4> <small>( Only Applicable to Managers or Above Position )</small>
                        <ol>
                          <?php if($eval_self_assessment->num_rows() > 0):?>
                            <?php foreach($eval_self_assessment->result_array() as $assessment):?>
                              <li>
                                <?=$assessment['question']?>
                              </li>
                              <?php if(count((array)$eval_assessment) > 0):?>
                                <?php foreach($eval_assessment as $assess):?>
                                  <?php if($assessment['id'] == $assess->id):?>
                                    <textarea name="eval_assessment[]" id="eval_assessment<?=$assessment['id']?>" data-id = "<?=$assessment['id']?>" cols="30" rows="3" class="form-control mb-4 eval_assessment"><?=$assess->value?></textarea>
                                  <?php endif;?>
                                <?php endforeach;?>
                              <?php else:?>
                                <textarea name="eval_assessment[]" id="eval_assessment<?=$assessment['id']?>" data-id = "<?=$assessment['id']?>" cols="30" rows="3" class="form-control mb-4 eval_assessment">dsdsdsd</textarea>
                              <?php endif;?>
                            <?php endforeach;?>
                          <?php endif;?>
                        </ol>
                      </div>
                    <?php endif;?>
                    <!-- Confirmation of Discussion -->
                    <div class="col-md-12 mb-3">
                      <h4><strong>Confirmation of Discussion</strong></h4>
                      <h5>This evaluation has been fully discussed and explained to me and I understand all the information stated in this Performance Evaluation. </h5>
                    </div>
                    <!-- Comments of Employee -->
                    <div class="col-md-12 mb-4 <?=($eval_status != 'ongoing' && $this->session->emp_idno == $eval['employee_idno']) ? "" : "evaluated"?> <?=($eval_status == 'ongoing') ? "ongoing" : ""?>">
                      <h5>Comments of Employee:</h5>
                      <textarea id = "emp_comment" name="emp_comment" rows="3" cols="80" class = "form-control"><?=($eval['eval_comments'] != NULL) ? $eval['eval_comments'] : ''?></textarea>
                    </div>
                    <!-- NOTED BY -->
                    <div class="col-md-12 mb-4 <?=($eval_status == 'ongoing') ? "ongoing" : ""?> <?=($eval_status != 'ongoing' && $this->session->deptId == hr_id() && $this->session->login_type == 'admin') ? "" : "evaluated"?>">
                      <h5 style = "font-weight:bold;">ACTIONS TAKEN BY HRD (if any) </h5>
                      <textarea id = "action_hr" name="action_hr" rows="3" cols="80" class = "form-control mb-5" ><?=$eval['eval_action_hr']?></textarea>
                      <h4 class = "d-inline mr-2"><strong>NOTED BY : </strong></h4>
                      <input type="text" id = "noted_hr" value = "<?=$eval['certify_by']?>" name = "noted_hr" class="form-control d-inline col-md-4 <?=($eval_status != 'ongoing') ? "evaluated" : ""?>">
                    </div>
                    <!-- APPENDIX -->
                    <?php if($eval['eval_type'] == 'type_1'):?>
                      <div class="col-md-12 mb-3 <?=($eval_status != 'ongoing' && $this->session->emp_idno == $eval['employee_idno']) ? "" : "evaluated"?> <?=($eval_status == 'ongoing') ? "ongoing" : ""?>">
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
                                  <td><input type="text" class = "form-control projects" name = "project<?=$x?>" id = "project<?=$x?>" data-id ="<?=$x?>"></td>
                                  <td class = "text-center"><input type = "radio" name = "task<?=$x?>" value = "1" class = "input"></td>
                                  <td class = "text-center"><input type = "radio" name = "task<?=$x?>" value = "2" class = "input"></td>
                                  <td class = "text-center"><input type = "radio" name = "task<?=$x?>" value = "3" class = "input"></td>
                                  <td class = "text-center"><input type = "radio" name = "task<?=$x?>" value = "4" class = "input"></td>
                                  <td class = "text-center"><input type = "radio" name = "task<?=$x?>" value = "5" class = "input"></td>
                                  <td class = "text-center"><input type = "radio" name = "task<?=$x?>" value = "N/A" class = "input"></td>
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
                    <?php endif;?>
                  </div>
                <?php else:?>
                  <div class="form-group row">
                    <div class="col-md-12 text-center" style = "padding: 12% 0 12% 0;">
                      <h1><i class="fa fa-inbox mr-3"></i>No Data Available</h1>
                    </div>
                  </div>
                <?php endif;?>
              </div>

              <div class="card-footer text-right">
                <?php if($this->session->position_lvl <= hr_or_above()):?>
                  <?php if($this->session->emp_idno == $eval['management_id'] && $eval_status == "ongoing"):?>
                    <button class="btn btn-primary" id = "btn_back" style = "display:none;">Back</button>
                    <button class="btn btn-primary" id = "btn_next" style = "display:<?=($eval_status == 'ongoing')? 'inline-block': 'none'?>">Next</button>
                    <button class="btn btn-sm btn-primary" id = "btn_save" style = "display:none;">Save</button>
                  <?php else:?>
                    <button class="btn btn-primary" id = "btn_certify" style = "display:<?=($eval_status == 'evaluated') ? 'inline-block': 'none'?>">Certify</button>
                    <button class="btn btn-primary" id = "btn_print" style = "display:<?=($eval_status == 'certified')? 'inline-block' : 'none'?>">Print</button>
                  <?php endif;?>
                  <!-- <a href="<?=base_url('evaluations/Evaluations/print/'.$token.'/'.$raw_id)?>" class="btn btn-primary" style = "display:<?=($eval_status == 'certified')? 'inline-block' : 'none'?>">Print</a> -->
                <?php else:?>
                  <button class="btn btn-primary" id = "btn_back" style = "display:none;">Back</button>
                  <button class="btn btn-primary" id = "btn_next" style = "display:<?=($eval_status == 'ongoing')? 'inline-block': 'none'?>">Next</button>
                  <button class="btn btn-sm btn-primary" id = "btn_save" style = "display:none;">Save</button>
                <?php endif;?>

                <!-- <?php if($this->session->login_type != 'admin' && $this->session->emp_idno == $eval['employee_idno']):?>
                  <button class="btn btn-sm btn-primary" id = "btn_save">Save</button>
                <?php endif;?> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>



<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets\js\evaluations\evaluations_view.js')?>"></script>
