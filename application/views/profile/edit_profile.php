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
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="16" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/profile/'.$token);?>">Profile</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>
    </div>
  <input type = "hidden" id = "employee_idno" value = "<?=$user_details['employee_idno']?>">

    <section class="tables">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" id = "profile_card">
                        <div class="card-header">
                            <div class="form-group row">
                              <div class="col-md-12 text-center d-lg-none d-md-none d-sm-block mb-3">
                                <img alt='' class='emp_qrcode' data-url = 'https://api.qrserver.com/v1/create-qr-code/?data=<?=$user_details['employee_idno']?>&amp;size=50x50' src='https://api.qrserver.com/v1/create-qr-code/?data=<?=$user_details['employee_idno']?>&amp;size=100x100'>
                              </div>
                                <div class = "col-md-4">
                                    <div class="form-group row">
                                        <div class="view overlay ml-2">
                                            <img src="<?=base_url()?>/assets/employee_photos/<?=$picture_extension?>" style = "width: 800px;" class="img-fluid img-thumbnail" id = "emp_pic" alt="smaple image">
                                            <div class="mask flex-center rgba-black-strong" id = "default_pic">
                                                <div class="form-group row">
                                                    <div class = "col-md-12">
                                                        <p class="white-text"><i class = "fa fa-camera fa-lg"></i> Tap to Update Photo</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class = "col-md-12">
                                            <div class = "ml-2">
                                                <h1><?php
                                                    echo $user_details['first_name']." ".$user_details['middle_name']." ".$user_details['last_name'];
                                                ?></h1>
                                                <h4 id="emailHelp" class="form-text text-muted"><?=$user_details['department']?> - <?=$user_details['worksite']?></h4>
                                            </div>
                                        </div>
                                        <div class = "col-md-12">
<!--                                             <div class = "ml-5">
                                                <input type = 'file' id = "image_upload" style="display: none;">
                                            </div>
                                            <div class = "ml-5">
                                                <button type = "submit" id = "image_upload_btn" class = "btn btn-success" style="display: none;">Change Employee Photo</button>
                                            </div> -->
                                            <form method="post" id="upload_form" enctype="multipart/form-data">
                                                <input type="file" name="image_file" id="image_file" style="display: none;" />
                                                <input type="submit" name="upload" id="upload_btn" value="Change Employee Photo" class="btn btn-success" style="display: none;" />
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class = "col-md-6">
                                  <div class="form-group row mt-5">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-user text-primary mr-1"></i><b>Employee ID Number:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['employee_idno']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                         <i class="fa fa-users mr-1"></i> <b>Position:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['department']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                         <i class="fa fa-building mr-1"></i> <b>Headquarters:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['worksite']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-phone text-success mr-1"></i><b>Contact Number:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['contact_no']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-transgender mr-1"></i><b>Gender:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['gender']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-birthday-cake text-danger mr-1"></i><b>Birth Date:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['birthday']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-envelope text-warning mr-1"></i><b>Email Address:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['email']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-map-marker text-danger mr-1"></i><b>Address 1:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['home_address1']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-map-marker text-success mr-1"></i><b>Address 2:</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['home_address2']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                      <div class="col-md-1">
                                      </div>
                                      <div class="col-md-3">
                                          <i class="fa fa-flag text-success mr-1"></i><b>Country</b>
                                      </div>
                                      <div class = "col-md-8">
                                          <span><?=$user_details['country']?></span>
                                      </div>
                                  </div>
                                  <div class="form-group row">
                                    <div class="col-md-1">
                                    </div>
                                    <div class="col-md-11">
                                        <button type = "button" class = "btn btn-info" id = "edit_info_toggle" data-target = "#Edit_information_modal" data-toggle = "modal"><i class = "fa fa-edit"></i>&nbsp;Edit Details</button>
                                  </div>
                                </div>
                            </div>
                            <div id = "qr_wrapper" class="col-md-2 pt-3 text-center d-none d-md-block d-lg-block" style = "cursor:pointer;">
                              <img alt='' class='emp_qrcode' data-url = 'https://api.qrserver.com/v1/create-qr-code/?data=<?=$user_details['employee_idno']?>&amp;size=50x50' src='https://api.qrserver.com/v1/create-qr-code/?data=<?=$user_details['employee_idno']?>&amp;size=100x100'>
                            </div>
                        </div>
                        <div class = "card-footer">
                      </div>
<!--                         <div class = "card-footer">
                          <div class = "row">
                            <div class = "col-md-8">
                                <div class = "row">
                                  <div class = "col-md-3">
                                    <p><b>Valid ID Type: </b>Pag-Ibig ID</p>
                                  </div>
                                  <div class = "col-md-3">
                                    <p><b>ID Number: </b>POR019734</p>
                                  </div>
                                  <div class = "col-md-3">
                                    <p><b>ID Value: </b><span class = "text-success">Primary</span></p>
                                  </div>
                                  <div class = "col-md-3">
                                    <p><b>Upload Date: </b>2019-03-15</p>
                                  </div>
                                </div>
                            </div>
                            <div class = "col-md-4">
                                  <div class="form-group row">
                                    <div class = "pull-right">
                                      <div class = "col-md-12">
                                          <button data-toggle="modal" id="view_pdf_btn" data-target = "#ViewImageModal" class="btn btn-primary text-right btnClickAddArea" style=""><i class="fa fa-eye"></i>&nbsp;View Image</button>
                                          <button data-toggle="modal" id="view_pdf_btn" data-target = "#EditImageModal" class="btn btn-warning text-right btnClickAddArea" style=""><i class="fa fa-edit"></i>&nbsp;Edit</button>
                                          <button data-toggle="modal" id="view_pdf_btn" data-target = "#DeleteImageModal" class="btn btn-danger text-right btnClickAddArea" style=""><i class="fa fa-trash"></i>&nbsp;Delete</button>
                                      </div>
                                    </div>
                                  </div>
                            </div>
                          </div>
                      </div> -->
                      <div id="AddIDModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-md modal-md-custom">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h4 id="exampleModalLabel" class="modal-title">Add New ID</h4>
                                      <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                                  </div>
                                      <div class="modal-body">
                                      <form method = "POST" id="add_upload_form" enctype="multipart/form-data">
                                          <div class="row">
                                              <div class="col-md">
                                                  <div class="form-group">
                                                  <div class="col-md-12">
                                                  <label for="" class="d-block">Valid ID Type <span class="asterisk"></span></label>
                                                  <input type = "text" class = "form-control" name = "valid_id_type" id = "valid_id_type">
                                                  <label for="" class="d-block">ID Number <span class="asterisk"></span></label>
                                                  <input type = "text" class = "form-control" name = "id_number" id = "id_number">
                                                  <label for="" class="d-block">ID Value <span class="asterisk"></span></label>
                                                  <select class = "form-control" name = "id_value" id = "id_value">
                                                    <option value = "Primary">Primary</option>
                                                    <option value = "Secondary">Secondary</option>
                                                    <option value = "Others">Others</option>
                                                  </select>
                                                  <label for="" class="d-block">Change Picture <span class="asterisk"></span></label>
                                                  <input type="file" name="add_image_file" id="add_image_file" style="" />
<!--                                                   <input type="submit" name="upload" id="upload_btn" value="Change Employee Photo" class="btn btn-success" style="display: none;" />   -->



                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <div class="form-group row">
                                              <div class="col-md-12">
                                                  <input type="submit" id = "add_save_btn" style="float:right; margin-right:10px;" value = "Save" class="btn btn-success"></form>
                                                  <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                                              </div>
                                          </div>
                                      </div>
                                      </form>
                                      </div>
                              </div>
                          </div>
                      </div>


                      <div id="ViewImageModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-md modal-md-custom">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h4 id="exampleModalLabel" class="modal-title">ID Image</h4>
                                      <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                                  </div>
                                      <div class="modal-body">
                                      <form id="addCA-form">
                                          <div class="row">
                                              <div class="col-md">
                                                  <div class="form-group">
                                                  <div class="col-md-12" id = "picture_base">
<!--                                                        <img src="<?=base_url()?>/assets/employee_ids/11542_national_id_active.jpg" style = "width: 800px;" class="img-fluid img-thumbnail" id = "emp_pic" alt="smaple image"> -->
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <div class="form-group row">
                                              <div class="col-md-12">
                                                  <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                                              </div>
                                          </div>
                                      </div>
                                      </form>
                                      </div>
                              </div>
                          </div>
                      </div>

                      <div id="EditModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-md modal-md-custom">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h4 id="exampleModalLabel" class="modal-title">Edit Details</h4>
                                      <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                                  </div>
                                      <div class="modal-body">
                                      <form method = "POST" id="edit_upload_form" enctype="multipart/form-data">
                                          <div class="row">
                                              <div class="col-md">
                                                  <div class="form-group">
                                                  <div class="col-md-12">
                                                  <input type="hidden" name="edit_valid_id_id" id = "edit_valid_id_id">
                                                  <label for="" class="d-block">Valid ID Type <span class="asterisk"></span></label>
                                                  <input type = "text" class = "form-control" id = "edit_valid_id_type" name = "edit_valid_id_type">
                                                  <label for="" class="d-block">ID Number <span class="asterisk"></span></label>
                                                  <input type = "text" class = "form-control" id = "edit_id_number" name = "edit_id_number">
                                                  <label for="" class="d-block">ID Value <span class="asterisk"></span></label>
                                                  <select class = "form-control" id = "edit_id_value" name = "edit_id_value">
                                                    <option value = "Primary">Primary</option>
                                                    <option value = "Secondary">Secondary</option>
                                                    <option value = "Others">Others</option>
                                                  </select>
                                                  <form method="post" id="upload_form" enctype="multipart/form-data">
                                                  <label for="" class="d-block">Change Picture <span class="asterisk"></span></label>
                                                  <div id = "disabled_textbox">
                                                  <input type = "text" class = "form-control" id = "edit_image_file_temp" style = "pointer-events:none;">
                                                  </div>
                                                  <input type="file" name="edit_image_file" id="edit_image_file" style="display:none" />
<!--                                                   <input type="submit" name="upload" id="upload_btn" value="Change Employee Photo" class="btn btn-success" style="display: none;" />   -->



                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <div class="form-group row">
                                              <div class="col-md-12">
                                                  <button type="submit" style="float:right; margin-right:10px;" class="btn btn-primary">Save</button>
                                                  </form>
                                                  <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                                              </div>
                                          </div>
                                      </div>
                                      </form>
                                      </div>
                              </div>
                          </div>
                      </div>
                      <div id="Edit_information_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-md modal-lg-custom">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h4 id="exampleModalLabel" class="modal-title">Edit Details<br>
                                        <span class = "text text-muted"><span class = "text text-danger">Note: </span>Informations edited to be approved by admins.</span></h4>
                                      <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                                  </div>
                                      <div class="modal-body">
                                          <div class="row">
                                              <div class="col-md">
                                                  <div class="form-group row">
                                                    <div class="col-md-6">
                                                      <input type="hidden" name="" id = "">
                                                      <label for="" class="d-block">First Name: <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_first_name" value = "<?=$user_details['first_name']?>" name = "">
                                                      <label for="" class="d-block">Middle Name: <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_middle_name" value = "<?=$user_details['middle_name']?>"  name = "">
                                                      <label for="" class="d-block">Last Name: <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_last_name" value = "<?=$user_details['last_name']?>" name = "">
                                                      <label for="" class="d-block">Contact Number: <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_contact_number" value = "<?=$user_details['contact_no']?>" name = "">
                                                       <label for="" class="d-block">Marital Status: <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_marital_status" value = "<?=$user_details['marital_status']?>" name = "">
                                                      <label for="" class="d-block">Gender: <span class="asterisk"></span></label>
                                                      <select id = "inf_gender" class = "form-control">
                                                        <?php
                                                          if($user_details['gender'] == "male" || $user_details['gender'] == "Male"){
                                                            echo "<option class = 'form-control' value = 'Male'>Male</option>
                                                                  <option class = 'form-control' value = 'Female'>Female</option>
                                                            ";
                                                          }else{
                                                            echo "<option class = 'form-control' value = 'Female'>Female</option>
                                                            <option class = 'form-control' value = 'Male'>Male</option>
                                                            ";
                                                          }
                                                        ?>
                                                      </select>
                                                    </div>
                                                    <div class = "col-md-6">
                                                      <label for="" class="d-block">Birth Date: <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control date_input_empty" autocomplete="off" id = "inf_birth_date" value = "<?=$user_details['birthday']?>" name = "">
                                                      <label for="" class="d-block">Active Email Address: <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_email" value = "<?=$user_details['email']?>" name = "">
                                                      <label for="" class="d-block">Address 1 <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_address1" value = "<?=$user_details['home_address1']?>" name = "">
                                                      <label for="" class="d-block">Address 2 <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_address2" value = "<?=$user_details['home_address2']?>" name = "">
                                                      <label for="" class="d-block">Country <span class="asterisk"></span></label>
                                                      <input type = "text" class = "form-control" autocomplete="off" id = "inf_country" value = "<?=$user_details['country']?>" name = "">
                                                    </div>
<!--                                                   <input type="submit" name="upload" id="upload_btn" value="Change Employee Photo" class="btn btn-success" style="display: none;" />   -->



                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <div class="form-group row">
                                              <div class="col-md-12">
                                                  <button type="button" id = "edit_info_btn" style="float:right; margin-right:10px;" class="btn btn-primary">Save</button>
                                                  <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                                              </div>
                                          </div>
                                      </div>
                                      </form>
                                      </div>
                              </div>
                          </div>
                      </div>

                      <div id="DeleteModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                          <div role="document" class="modal-dialog modal-md modal-md-custom">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h4 id="exampleModalLabel" class="modal-title">Delete</h4>
                                      <input type = "hidden" id = "delete_id">
                                      <!-- <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button> -->
                                  </div>
                                      <div class="modal-body">
                                      <form id="addCA-form">
                                          <div class="row">
                                              <div class="col-md">
                                                  <div class="form-group">
                                                  <div class="col-md-12">
                                                      <h3>Are you sure you want to delete <span class = "text text-danger" id = "id_type_delete"></span>?</h3>

                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <div class="form-group row">
                                              <div class="col-md-12">
                                                  <button type="button" id = "delete_btn" style="float:right; margin-right:10px;" class="btn btn-danger">Delete</button>
                                                  <button type="button" style="float:right; margin-right:10px;" class="btn blue-grey cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                                              </div>
                                          </div>
                                      </div>
                                      </form>
                                      </div>
                              </div>
                          </div>
                      </div>

                    </div>
                    <!-- DATATABLE CARD-->
                    <div class="card">
                        <div class="card-header">
                            <div class = "form-group row">
                              <div class = "col-md-8">
                                <h2>Valid IDs</h2>
                                <h5 id="emailHelp" class="form-text text-muted"><span class = "text text-danger">Note:</span> Please ensure that your IDs are valid for legitimate references</h5>
                              </div>
                              <div class = "col-md-4">
                                <div class = "pull-right">
                                  <button type = "button" class = "btn btn-primary" data-toggle = "modal" data-target = "#AddIDModal"><i class = "fa fa-plus"></i>&nbsp; Add Valid ID</button>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class = "card-body">
                            <div class="table-responsive">
                                <table class="table  table-striped table-hover table-bordered" id="id_table"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
                                    <thead>
                                        <tr>
                                            <th width = "15%">ID</th>
                                            <th width = "15%">Valid ID Type</th>
                                            <th width = "15%">ID Number</th>
                                            <th width = "15%">ID Value</th>
                                            <th width = "15%">Upload Date</th>
                                            <th width = "25%">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id = "view_modal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body tex-center" >
            <div class="col-12 text-center">
              <img alt='' style = "object-position: center center;" data-url = 'https://api.qrserver.com/v1/create-qr-code/?data=<?=$user_details['employee_idno']?>&amp;size=50x50' src='https://api.qrserver.com/v1/create-qr-code/?data=<?=$user_details['employee_idno']?>&amp;size=200x200'>
            </div>
          </div>
          <div class="modal-footer text-right">
            <!-- <button class="btn btn-sm btn-primary">Save</button> -->
            <button class="btn blue-grey" data-dismiss = "modal">Close</button>
          </div>
        </div>
      </div>
    </div>



<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="<?=base_url('assets/js/profile/profile.js');?>"></script>
