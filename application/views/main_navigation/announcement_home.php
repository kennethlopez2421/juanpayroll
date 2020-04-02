<!--
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->

<div class="content-inner" id="pageActive" data-num="17" data-namecollapse="" data-labelname="Announcement">
    <div class="bc-icons-2 card mb-4">

        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Announcement</li>
        </ol>

    </div>
    <input type="hidden" id='token' value="<?= $token ?>">
    <!-- Page Header-->
    <?php if($announcements->num_rows() > 0):?>
      <div class="announce_container">
      <?php foreach($announcements->result_array() as $announce):?>
        <?php
          $d = new DateTime($announce['announce_start']);
          $date = $d->format('M d, Y');
        ?>
        <div class="card mb-3">
          <div class="container">
            <div class="card-body">
              <a href="<?=base_url('settings/Announcement/announcement_view/'.$announce['announce_id'].'/'.$token)?>"><h1 class = "text-primary"><?=$announce['announce_title']?></h1></a>
              <p class = "mb-4"><?=$date?> By <span class = "text-primary" style = "text-decoration:underline"><?=$announce['name']?></span></p>
              <?=truncateHtml($announce['announce_body'])?>
            </div>
          </div>
        </div>
      <?php endforeach;?>
      </div>

      <?php if($total_announce > 5):?>
        <div class="form-group text-center">
          <button class="btn btn-info" id = "btn_loadmore_announce" data-count = "5">Load More</button>
        </div>
      <?php endif;?>
    <?php else:?>
      <div class="card">
        <div class="card-body text-center" style = "padding-top:15%;padding-bottom:15%;">
          <i class="fa fa-bullhorn d-block mb-2" style = "font-size: 80px;"></i>
          <h5>No announcement as of the moment.</h5>
        </div>
      </div>
    <?php endif;?>



    <?php $this->load->view('includes/footer'); ?>
    <script src = "<?=base_url('assets/js/settings/announcement.js')?>"></script>
