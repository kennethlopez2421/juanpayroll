<style type="text/css">
	.font-attr {
		/*font-weight: bold;*/
		font-size:15px;
	}
	.font-attr-10{
		font-size:10px;
	}
	.size-td{
		font-size:10px;
		width: 70px;
	}
	.header-attr{
		background-color: #b2b2b2;
		font-size: 10px;
	}
	.center{
		text-align: center;
	}
	.font-height {
		height:10px;
		font-size: 10px;
	}
	.right{
		text-align: right;
	}
	.font-attr-12{
		font-size:12px;
	}
	.left{
		text-align: left;
	}
	.width-3{
		width:260px;
	}
	.width-2{
		width:100px;
	}
</style>
<br>
<div style="margin-left: 50px;line-height: 8px;">
  <h3 class="center">One Payroll</h3>
  <p class="line-height-two center">Address:   <?php echo company_address(); ?></p>
  <p class="line-height-two center">Website:   <?php echo base_url(); ?></p>
  <p class="line-height-two center">Contact #: <?php echo company_phone(); ?></p>
</div>
<hr>

	<table class = "table_top">
		<tr>
			<td><h2 class="center">Pay Slip</h2></td>
		</tr>
		<tr>
			<td class = "size-td">ID No :</td>
			<td><span class = "font-attr-10"><?=$employee_idno?></span></td>
		</tr>
		<tr>
			<td class = "size-td">Name : </td>
			<td><span class = "font-attr-10"><b><?=$name?></b></span></td>
		</tr>
		<tr>
			<td class = "size-td">Date : </td>
			<td><span class = "font-attr-10"><?=$date?></span></td>
		</tr>
	</table>
	<br>
	<br>

	<table class = "table_top">
		<tr class = "header-attr">
				<th class = "font-attr-12" width = "25%">Gross Salary</th>
				<th class = "font-attr-12 right" width = "30%">Time Value</th>
				<th class = "font-attr-12 right" width = "35%">Amount</th>
				<th class = "font-attr-12" width = "10%"></th>
		</tr>
		<tr>
			<td class = "font-height"></td>
		</tr>
		<tr>
			<td class = "font-height">Days (days)</td>
			<td class = "font-height right"><?=$wdays?></td>
			<td class = "font-height right"><?=$currency?> <?=$gross_pay?></td>

		</tr>
		<!-- <tr>
			<td class = "font-height">OT (mins) </td>
			<td class = "font-height right"><?=$getmanhours_log->ot?></td>
			<td class = "font-height right">
				<?=number_format($getadditional_log->overtimepay,2)?>
			</td>

		</tr> -->
		<!-- <tr>
			<td class = "font-height">Additionals </td>
			<td class = "font-height right">--</td>
			<td class = "font-height right">
				<?=number_format($getpayroll_log->additionals,2)?>
			</td>

		</tr> -->
		<tr>
			<td class = "font-height">Regular Holiday (days) </td>
			<td class = "font-height right"><?=$reg_holiday?></td>
			<td class = "font-height right"><?=$currency?> <?=$reg_holiday_pay?></td>

		</tr>
		<tr>
			<td class = "font-height">Special NW Holiday (days)</td>
			<td class = "font-height right"><?=$spl_holiday?></td>
			<td class = "font-height right"><?=$currency?> <?=$spl_holiday_pay?></td>
		</tr>
		<tr>
			<td class = "font-height">Sunday (days)</td>
			<td class = "font-height right"><?=$sunday?></td>
			<td class = "font-height right"><?=$currency?> <?=$sunday_pay?></td>

		</tr>
	</table>
	<br>
	<div style="margin-top:150px">
		<table class = "table_top">
			<tr class = "header-attr">
				<th class = "font-attr-12" width = "25%">Penalties</th>
				<th class = "font-attr-12 right" width = "30%">Time Value</th>
				<th class = "font-attr-12 right" width = "35%">Amount</th>
				<th class = "font-attr-12" width = "10%"></th>
			</tr>
			<tr>
				<td class = "font-height"></td>
			</tr>
			<tr>
				<td class = "font-height">Absent (days)</td>
				<td class = "font-height right"><?=$absent?></td>
				<td class = "font-height right"><?=$currency?> <?=$absent_deduction?></td>
			</tr>
			<tr>
				<td class = "font-height">Late (mins) </td>
				<td class = "font-height right"><?=$late?></td>
				<td class = "font-height right"><?=$currency?> <?=$late_deduct?></td>
			</tr>
			<tr>
				<td class = "font-height">Undertime (mins)</td>
				<td class = "font-height right"><?=$ut?></td>
				<td class = "font-height right"><?=$currency?> <?=$ut_deduct?></td>
			</tr>
			<tr>
				<td class = "font-height"></td>
			</tr>
			<tr>
				<td class = "font-height"><b>Gross Salary:</b></td>
				<td class = "font-height"></td>
				<td class = "font-height right"><b><?=$currency?> <?=$gross_pay_less?></b></td>
			</tr>
		</table>
		<br>
		<br>
		<table  bolder = "1" class = "table_top">
			<tr class = "header-attr">
				<th class = "font-attr-12" width = "25%">Salary Deductions</th>
				<th class = "font-attr-12" width = "25%"></th>
				<th class = "font-attr-12 right" width = "40%">Amount</th>
				<th class = "font-attr-12" width = "10%"></th>
			</tr>
			<tr>
				<td class = "font-height"></td>
			</tr>
			<tr>
				<td class = "font-height">SSS</td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$sss?></td>
			</tr>
      <tr>
				<td class = "font-height">SSS Loan</td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$sss_loan?></td>
			</tr>
			<tr>
				<td class = "font-height">Philhealth </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$philhealth?></td>
			</tr>
			<tr>
				<td class = "font-height">Pag-ibig </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$pagibig?></td>
			</tr>
			<tr>
				<td class = "font-height">Pag-ibig Loan </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$pagibig_loan?></td>
			</tr>
			<tr>
				<td class = "font-height">Cash Advance</td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$cashadvance?></td>
			</tr>
			<tr>
				<td class = "font-height">Salary Deductions </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$sal_deduct?></td>
			</tr>
			<tr>
				<td class = "font-height">Total Deductions: </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=$currency?> <?=$total_deduct?></td>
			</tr>
			<tr>
				<td class = "font-height"></td>
			</tr>

		</table>
		<br>
		<br>
    <table class="table_top" bolder = "1">
      <tr class = "header-attr">
				<th class = "font-attr-12" width = "25%">Additionals</th>
				<th class = "font-attr-12 right" width = "30%">Time Value</th>
				<th class = "font-attr-12 right" width = "35%">Amount</th>
				<th class = "font-attr-12" width = "10%"></th>
			</tr>
			<tr>
				<td class = "font-height"></td>
			</tr>
      <tr>
        <td class = "font-height">Additional Pay</td>
        <td class = "font-height"></td>
        <td class = "font-height right"><?=$currency?> <?=$add_pay?></td>
      </tr>
      <tr>
        <td class = "font-height">Overtime(mins)</td>
        <td class = "font-height right"><?=$ot_min?></td>
        <td class = "font-height right"><?=$currency?> <?=$ot_pay?></td>
      </tr>
      <tr>
				<td class = "font-height"></td>
			</tr>
      <tr>
				<td class = "font-height"><b>Net Pay:</b></td>
				<td class = "font-height"><b></b></td>
				<td class = "font-height right"><b><?=$currency?> <?=$net_pay?></b></td>

			</tr>
			<tr>
				<td height="100px"></td>
			</tr>
    </table>
		<hr>
	</div>
<style type="text/css">
	.table_top{
		margin-top: 300px;
	}
</style>
