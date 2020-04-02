<?php
// 071318
// this code is for destroying session and page if they access restricted page
//
// $position_access = $this->session->userdata('get_position_access');
// $access_content_nav = $position_access->access_content_nav;
// $arr_ = explode(', ', $access_content_nav); //string comma separated to array
// $get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();
//
// $url_content_arr = array();
// foreach ($get_url_content_db as $cun) {
//     $url_content_arr[] = $cun['cn_url'];
// }
// $content_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/';
//
// if (in_array($content_url, $url_content_arr) == false){
//     header("location:".base_url('Main/logout'));
// }
// 071318
?>
<div class="content-inner" id="pageActive" data-num="17" data-namecollapse="" data-labelname="Announcement">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <!-- <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li> -->
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/announcement_home/'.$token);?>">Announcement</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?=$announce['announce_title']?></li>
        </ol>
    </div>
    <input type = "hidden" id = "token" value = "<?=$token?>">
    <section class="tables">
      <div class="card">
        <div class="card-body">
          <div class="container">
            <div class="form-group">
              <?php
                $d = new DateTime($announce['announce_start']);
                $date = $d->format('M d, Y');
              ?>
              <h1 class = "text-primary"><?=$announce['announce_title']?></h1>
              <p class = "mb-4"><?=$date?> By <span class = "text-primary" style = "text-decoration:underline"><?=$announce['name']?></span></p>
              <div class="col-12 p-0">
                <embed type = "application/pdf" src="<?=base_url($announce['announce_body'])?>" width="100%" height="600">
              </div>
              <!-- <?=$announce['announce_body']?> -->
            </div>
          </div>
        </div>
      </div>
    </section>
<?php $this->load->view('includes/footer');?>
<!-- <script src = "<?=base_url('assets/js/settings/announcement.js')?>"></script> -->
