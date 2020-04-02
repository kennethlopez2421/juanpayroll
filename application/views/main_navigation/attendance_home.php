<!--
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->

<div class="content-inner" id="pageActive" data-num="22" data-namecollapse="" data-labelname="Attendance">
  <div class="bc-icons-2 card mb-4">

    <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
      <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
      <li class="breadcrumb-item active">Attendance</li>
    </ol>

  </div>
  <input type = "hidden" id = "token" value = "<?=$token?>">
  <input type="hidden" id = "emp_idno" value = "<?=en_dec('en',$this->session->userdata('emp_idno'))?>">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <div class="form-group row">
              <div class="col-md-3">
                <label for="Filter" class="form-control-label col-form-label-sm">Filter by</label>
                <select name="" id="filter_by" class="form-control">
                  <option value="this_month">This Month</option>
                  <option value="last_3months">Last 3 Months</option>
                  <option value="last_6months">Last 6 Months</option>
                  <!-- <option value="by_date">Date Issued</option> -->
                  <!-- <option value="by_amount">Amount</option> -->
                </select>
              </div>

              <div class="col-md-9 text-right">
                <input type="hidden" id = "total_whours" value = "<?=$total_whours?>">
                <input type="hidden" id = "total_bhours" value = "<?=$total_bhours?>">
                <input type="hidden" id = "sched_type" value = "<?=$sched_type?>">
                <input type="hidden" id = "worksched" value = '<?=$worksched?>'>
                <input type="hidden" id = "month" value = "<?=$month?>">
                <input type="hidden" id = "days" value = "<?=$days?>">
                <input type="hidden" id = "lates" value = "<?=$lates?>">
                <input type="hidden" id = "undertimes" value = "<?=$undertimes?>">
                <input type="hidden" id = "overbreaks" value = "<?=$overbreaks?>">
                <input type="hidden" id = "total_mins" value = "<?=$total_mins?>">
                <button class="btn btn-primary btn-sm" id = "btn_back" style = "display:none;">Back</button>
                <!-- <button data-toggle="modal" id="searchButton" class="btn btn-primary btnClickAddArea">Search</button> -->
              </div>
            </div>
          </div>

          <div class="card-body">
            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php $this->load->view('includes/footer'); ?>
<script src = "<?=base_url('assets\js\highchart\highcharts.js')?>"></script>
<script src = "<?=base_url('assets\js\highchart\export-data.js')?>"></script>
<script src = "<?=base_url('assets\js\highchart\exporting.js')?>"></script>
<script src = "<?=base_url('assets\js\attendance_chart\attendance.js')?>"></script>
