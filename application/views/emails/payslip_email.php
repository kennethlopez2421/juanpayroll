<!DOCTYPE html>
<html>
<head lang="en">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>HRIS</title>
</head>
<body style="font-family: Arial ,sans-serif; background-color: #e8ebe9; width: 100%; padding: 10px;">
	<table style="width: 100%;" cellspacing="0" cellpadding="0">
		<tbody>
      <tr>
        <td align = "center" style = "padding-bottom: 30px;"><img src="<?=base_url('assets/img/juanpayroll-logo-04.png')?>" alt="" width = "100"></td>
      </tr>
			<tr>
				<td align="center">
					<table style="margin-top: 50px border-radius:3px;padding:10px 10px 30px 10px; margin-bottom: 40px;background: white;border-radius: 3px;max-width: 600px; overflow: hidden;" cellspacing="0" cellpadding="0">
						<tbody>
							<tr>
								<td width="100%" align="center"  style=" text-align: center;;padding:10px 120px;">
									<h2 style = "font-family:tahoma;"><?=$date?> Payslip</h2>
								</td>
							</tr>
              <tr>
                <td width = "100%" align = "justify" style = "padding:0px 50px;">
                  <p>Hi, <?=$fullname?></p>
                  <p style = "font-size:13px;color:#333;">
                    Provided in the link below is your payslip for <?=$date?>.
                    For any concern on your payslip or if you have any question, do not hesitate to contact us.
                  </p>
                  <br>
                  <p style = "font-size:13px;color:#333;">Regards,</p>
                  <p style = "font-size:13px;color:#333;">HR Department</p>
                  <!-- <p style = "font-size:13px;color:#333;">One Payroll JC World Wide</p> -->
                </td>
              </tr>
							<tr>
								<td align = "center" style = "padding-top:50px;">
                  <a href="<?=$download_link?>" style = "display:inline-block;height:20px;width:150px;text-decoration:none;background-color:#72716f !important;font-size:14px;padding:10px 40px;color:#fff;border-radius:3px;">Download Payslip</a>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
