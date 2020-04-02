<?php // matching the token url and the token session
   if($this->session->userdata('token_session') != en_dec("dec", $token)){
       header("Location:".base_url('Main/logout')); /* Redirect to login */
       exit();
   }

   //022818
   $position_access = $this->session->userdata('get_position_access');
   $access_nav = $position_access->access_nav;
?>
<style>
  .content-inner{
    padding: 0px 20px 40px 20px !important;
  }

  .bg-primary-dark{
    background-color: #72716f !important;
  	color: #fff !important;
  }

  .bg-primary{
    background-color: #ffffff !important;
    color: #72716f !important;
  }

  .highcharts-figure, .highcharts-data-table table {
    min-width: 360px;
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-figure, .highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}


</style>

<div class="content-inner" id="pageActive" data-num="1" data-namecollapse="" data-labelname="Home">
  <div class="row mb-3">
    <div class="col-xl-3 col-md-6">
      <div class="card flex-row align-items-center align-items-stretch border-0">
        <div class="col-4 py-4 d-flex align-items-center bg-primary-dark justify-content-center rounded-left">
          <em class="fa fa-id-badge fa-3x"></em>
        </div>
        <div class="col-8 py-4 bg-primary rounded-right">
          <div class="h1 mt-0">1700</div>
          <div class="text-uppercase">Applicants</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card flex-row align-items-center align-items-stretch border-0">
        <div class="col-4 py-4 d-flex align-items-center bg-primary-dark justify-content-center rounded-left">
          <em class="fa fa-users fa-3x"></em>
        </div>
        <div class="col-8 py-4 bg-primary rounded-right">
          <div class="h1 mt-0">1700</div>
          <div class="text-uppercase">Employees</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-12">
      <div class="card flex-row align-items-center align-items-stretch border-0">
        <div class="col-4 py-4 d-flex align-items-center bg-primary-dark justify-content-center rounded-left">
          <em class="fa fa-university fa-3x"></em>
        </div>
        <div class="col-8 py-4 bg-primary rounded-right">
          <div class="h1 mt-0">1700</div>
          <div class="text-uppercase">Evaluation</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-12">
      <div class="card flex-row align-items-center align-items-stretch border-0">
        <div class="col-3 pt-2 d-flex px-4 align-items-center bg-primary-dark justify-content-center rounded-left">
          <div class="text-center">
            <div class="text-sm mb-3" data-now data-format="MMMM">April</div>
            <div class="h1 mt-0" data-now data-format"D">1</div>
          </div>
        </div>
        <div class="col-8 py-3 bg-primary rounded-right">
          <div class="text-uppercase mb-3" data-now data-format="dddd">WEDNESDAY</div>
          <div class="h1 mt-0 text-muted text-sm" data-now data-format="a">11:02 am</div>
        </div>
      </div>
    </div>
  </div>
  <!-- ATTENDANCE SECTION -->
  <div class="row">
    <div class="col-xl-9 col-lg-9 col-md-12">
      <div class="card">
        <div class="card-body">
          <script src="https://code.highcharts.com/highcharts.js"></script>
          <script src="https://code.highcharts.com/modules/exporting.js"></script>
          <script src="https://code.highcharts.com/modules/export-data.js"></script>
          <script src="https://code.highcharts.com/modules/accessibility.js"></script>

          <figure class="highcharts-figure">
              <div id="container"></div>
              <p class="highcharts-description">
                  A basic column chart compares rainfall values between four cities.
                  Tokyo has the overall highest amount of rainfall, followed by New York.
                  The chart is making use of the axis crosshair feature, to highlight
                  months as they are hovered over.
              </p>
          </figure>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-9 col-md-12">
      <div class="flex-row align-items-center align-items-stretch border-0">
        <div class="col-xl-lg-12 col-lg-12 col-md-6">

        </div>
      </div>
    </div>
  </div>

<?php $this->load->view('includes/footer'); ?>
<script>
  $(function(){
    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Monthly Average Rainfall'
    },
    subtitle: {
        text: 'Source: WorldClimate.com'
    },
    xAxis: {
        categories: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Rainfall (mm)'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Tokyo',
        data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

    }, {
        name: 'New York',
        data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]

    }, {
        name: 'London',
        data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]

    }, {
        name: 'Berlin',
        data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]

    }]
});
  });
</script>
