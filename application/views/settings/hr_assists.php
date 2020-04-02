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
<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/settings_home/'.$token);?>">Settings</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">HR Assists</li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <div class="form-group row">
                  <div class="col-lg-12">
                    <textarea name="hrassists_body" id="hrassists_body" cols="30" rows="10" class="form-control rq ckeditor"><?=$hr_assists?></textarea>
                  </div>
                </div>
              </div>

              <div class="card-footer text-right">
                <button id = "btn_save_hrassists" class="btn btn-sm btn-primary">Save</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php $this->load->view('includes/footer');?>
<script src = "<?=base_url('assets/ckeditor_full/ckeditor.js')?>"></script>
<script>
  var base_url = $("body").data('base_url');
  CKEDITOR.replace( 'hrassists_body', {
    height: 400,
    filebrowserUploadUrl: base_url + 'settings/Announcement/announce_img_upload'
  });
</script>
<script src = "<?=base_url('assets/js/settings/hr_assists.js')?>"></script>
