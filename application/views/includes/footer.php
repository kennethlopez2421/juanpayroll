                <!-- Page Footer-->
            </div>
        </div>
        </div>
        </div>
    </div>
    </div>
    </main>
                <footer class="main-footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <p>JP | JuanPayroll &copy; <?php echo year_only(); ?></p>
                            </div>
                            <div class="col-sm-6">
                                <p><?=powered_by();?></p>
                            </div>
                        </div>
                    </div>
                </footer>
<!-- Javascript files-->
<script src="<?=base_url('assets/js/jquery.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery-ui.js');?>"></script>
<script src="<?=base_url('assets/js/tether.min.js');?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="<?=base_url('assets/js/bootstrap.min.js');?>"></script>
<script src="<?=base_url('assets/js/mdb.min.js');?>"></script>
<script src="<?=base_url('assets/js/jquery.cookie.js');?>"> </script>
<script src="<?=base_url('assets/js/jquery.validate.min.js');?>"></script>
<!-- <script src="<?=base_url('assets/js/jquery.dataTables.js');?>"></script> -->
<script src="<?=base_url('assets/js/datatables.min.js');?>"></script>
<script src="https://cdn.datatables.net/scroller/1.5.1/js/dataTables.scroller.min.js"></script>
<script src="<?=base_url('assets/js/select2.min.js');?>"></script>
<script src="<?=base_url('assets/js/bootstrap-datepicker.min.js');?>"></script>
<script src="<?=base_url('assets/js/accounting.min.js');?>"></script>
<script src="<?=base_url('assets/js/moment.js');?>"></script>
<script src="<?=base_url('assets/js/jquery.easy-autocomplete.min.js');?>"></script>
<!-- custom script for your overall script -->
<script src="<?=base_url('assets/js/custom.js');?>"></script>
<script src="<?=base_url('assets/js/loadingoverlay.js');?>"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- uncomment this if you need charts -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="js/charts-home.js"></script> -->
<!-- uncomment this if you need charts -->
<script src="<?=base_url('assets/js/front.js');?>"></script>
<script src="<?= base_url('assets/js/jquery.toast.js'); ?>"></script>
<script src="<?=base_url('assets/js/jquery-code-scanner.js');?>"></script>
<script src="<?=base_url('assets/js/notification.js');?>"></script>
<script src = "<?=base_url('assets/js/cleavejs/cleave.min.js')?>"></script>
<script src = "<?=base_url('assets/js/cleavejs/addons/cleave-phone.ph.js')?>"></script>
<script src = "<?=base_url('assets/js/cleavejs/custom-cleave.js')?>"></script>
<script src = "<?=base_url('assets/js/jquery.alphanum.js')?>"></script>
<script src = "<?=base_url('assets\js\monthly_picker\MonthPicker.js')?>"></script>
<script src = "<?=base_url('assets/js/utility_helper.js')?>"></script>
<script src = "<?=base_url('assets\js\print\print.min.js')?>"></script>
<?php if(isset($this->session->superuser) && $this->session->superuser == true):?>
  <script src = "<?=base_url('assets\js\branch\branch.js')?>"></script>
<?php endif;?>

<!--plugin for datatable sum-->

<script type="text/javascript" src = "https://cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>

<!-- <script src = "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src = "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src = "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src = "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script> -->

</script>
<!-- Google Analytics: change UA-XXXXX-X to be your site's ID.-->
<!---->
<script>
(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
e=o.createElement(i);r=o.getElementsByTagName(i)[0];
e.src='<?=base_url('assets/js/analytics.js');?>';
r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
ga('create','UA-XXXXX-X');ga('send','pageview');
</script>
</body>
</html>
