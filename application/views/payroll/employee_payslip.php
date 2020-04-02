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
			<td class = "size-td">Pay Type : </td>	
			<td><span class = "font-attr-10"><?=$paytype_desc?></span></td>
		</tr>
		<tr>
			<td class = "size-td">From : </td>	
			<td><span class = "font-attr-10"><?=$date_from?></span></td>
		</tr>
		<tr>
			<td class = "size-td">To : </td>	
			<td><span class = "font-attr-10"><?=$date_to?></span></td>
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
			<td class = "font-height right"><?=$days_duration?></td>
			<td class = "font-height right"><?=number_format($gross_salary,2)?></td>		

		</tr>
		<tr>
			<td class = "font-height">OT (mins) </td>
			<td class = "font-height right"><?=$ot_duration?></td>	
			<td class = "font-height right"><?=number_format($overtime,2)?>
			</td>		

		</tr>
		<tr>
			<td class = "font-height">Additionals </td>
			<td class = "font-height right">--</td>	
			<td class = "font-height right"><?=number_format($additionals,2)?>
			</td>		

		</tr>
		<tr>
			<td class = "font-height">Regular Holiday (days) </td>	
			<td class = "font-height right"><?=$regular_holiday_duration?></td>
			<td class = "font-height right">
				<?=number_format($regular_holiday,2)?>
			</td>		

		</tr>
		<tr>
			<td class = "font-height">Special NW Holiday (days)</td>	
			<td class = "font-height right"><?=$special_holiday_duration?></td>
			<td class = "font-height right">
				<?=number_format($special_holiday,2)?>
			</td>		

		</tr>
		<tr>
			<td class = "font-height">Sunday (days)</td>	
			<td class = "font-height right"><?=$sunday_duration?></td>	
			<td class = "font-height right">
			<?=number_format($sundays,2)?>
			</td>		

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
				<td class = "font-height right"><?=$absent_duration?></td>	
				<td class = "font-height right">
					<?=number_format($absent,2)?>
				</td>
			</tr>
			<tr>
				<td class = "font-height">Late (mins) </td>	
				<td class = "font-height right"><?=$late_duration?></td>
				<td class = "font-height right">
				<?=number_format($late,2)?>
				</td>
			</tr>
			<tr>
				<td class = "font-height">Undertime (mins)</td>
				<td class = "font-height right"><?=$undertime_duration?></td>
				<td class = "font-height right"><?=number_format($undertime,2)?>

				</td>	
			</tr>
			<tr>
				<td class = "font-height"></td>		
			</tr>
			<tr>
				<td class = "font-height"><b>Gross Salary:</b></td>
				<td class = "font-height"></td>
				<td class = "font-height right"><b><?=number_format($gross_salary,2)?></b></td>	
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
				<td class = "font-height right"><?=number_format($sss,2)?></td>		
			</tr>
			<tr>
				<td class = "font-height">Philhealth </td>	
				<td class = "font-height"></td>
				<td class = "font-height right"><?=number_format($philhealth,2)?></td>
			</tr>
			<tr>
				<td class = "font-height">Pag-ibig </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=number_format($pag_ibig,2)?></td>	
			</tr>
			<tr>
				<td class = "font-height">SSS Loan </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=number_format($sss_loan,2)?></td>	
			</tr>
			<tr>
				<td class = "font-height">Pag-ibig Loan </td>
				<td class = "font-height"></td>
				<td class = "font-height right"><?=number_format($pag_ibig_loan,2)?></td>		
			</tr>
			<tr>
				<td class = "font-height">Cash Advance</td>	
				<td class = "font-height"></td>
				<td class = "font-height right"><?=number_format($cashadvance,2)?></td>	
			</tr>
			<tr>
				<td class = "font-height">Salary Deductions </td>	
				<td class = "font-height"></td>
				<td class = "font-height right"><?=number_format($salary_deduction,2)?></td>	
			</tr>
			<tr>
				<td class = "font-height">Total Deductions: </td>
				<td class = "font-height"></td>		
				<td class = "font-height right"> 
					<?=number_format($total_deductions,2)?>
				</td>	
			</tr>
			<tr>
				<td class = "font-height"></td>	
			</tr>
			<tr>
				<td class = "font-height"><b>Net Pay:</b></td>	
				<td class = "font-height"><b></b></td>	
				<td class = "font-height right"><b><?=number_format($netpay,2)?></b></td>	

			</tr>
			<tr>
				<td height="200px"></td>	
			</tr>
		</table>
		<br>
		<br>
		<hr>
	</div>
<style type="text/css">
	.table_top{
		margin-top: 300px;
	}
</style>