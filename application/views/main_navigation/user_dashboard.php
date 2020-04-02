<?php // matching the token url and the token session
   if($this->session->userdata('token_session') != en_dec("dec", $token)){
       header("Location:".base_url('Main/logout')); /* Redirect to login */
       exit();
   }

   //022818
   $position_access = $this->session->userdata('get_position_access');
   $access_nav = $position_access->access_nav;
?>
<link rel="stylesheet" href="<?=base_url('assets\css\user_dashboard.css')?>">
<div class="content-inner" id="pageActive" data-num="1" data-namecollapse="" data-labelname="Home">
<div class="row">
  <div class="col-xl-8 col-lg-8 col-md-12">
    <div class="card">
      <div class="card-body">
        <input type="hidden" id = "month" value = "">
        <input type="hidden" id = "days" value = "">
        <input type="hidden" id = "lates" value = "">
        <input type="hidden" id = "undertimes" value = "">
        <input type="hidden" id = "overbreaks" value = "">
        <input type="hidden" id = "total_mins" value = "">
        <figure class="highcharts-figure">
          <div id="container"></div>
          <p class="highcharts-description">
          </p>
        </figure>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src = "<?=base_url('assets\js\user_dashboard.js')?>"></script>
