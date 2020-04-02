<link href="<?=base_url('assets/reports/fontsapi.css');?>" media="all" rel="stylesheet" type="text/css">
<link href="<?=base_url('assets/reports/bootstrap.min.css');?>" media="all" rel="stylesheet" type="text/css">
<link href="<?=base_url('assets/reports/style.css');?>" media="all" rel="stylesheet" type="text/css">
<style>
.line-height-one{
  line-height: 1px;
}
.line-height-two{
  line-height: 2px;
}
.center{
    text-align:center;
}
</style>
<!-- <input type="hidden" id = "token" value = "<?=$token?>"> -->
<div style="margin-left: 50px;line-height: 8px;">
  <h3 class="center">One Payroll</h3>
  <p class="line-height-two center">Address:   <?php echo company_address(); ?></p>
  <p class="line-height-two center">Website:   <?php echo company_website(); ?></p>
  <p class="line-height-two center">Contact #: <?php echo company_phone(); ?></p>
</div>
