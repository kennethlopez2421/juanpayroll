<!DOCTYPE html>
<html>
<head lang="en">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>HRIS</title>
  <style>
    p{
      font-size: 13px;
    }

    #container{
      padding:30px;
      background-color: white;
      max-width: 600px;
      border-radius: 3px;
    }
  </style>
</head>
<body style="font-family: Arial ,sans-serif; background-color: #e8ebe9; width: 100%; padding: 20px;">
	<table style="width: 100%;" cellspacing="0" cellpadding="0">
		<tbody>
      <tr>
        <td align = "center" style = "padding-bottom: 30px;"><img src="<?=base_url('assets/img/juanpayroll-logo-04.png')?>" alt="" width = "100"></td>
      </tr>
			<tr>
				<td align = "center">
          <div id = "container">
            <table>
              <tbody>
                <tr>
                  <td>
                    <p>Hi <u><?=$fullname?></u></p>
                  </td>
                </tr>
                <tr>
                  <td>
                    <p>You have a pending evaluation for <u><?=$evaluatee?></u>. Evaluation cover period is from <?=$from?> to <?=$to?>. Thank you </p>
                  </td>
                </tr>
                <tr>
                  <td align = "center">
                    <a href="<?=base_url($code)?>" style = "display:inline-block;height:20px;width:150px;text-decoration:none;background-color:#72716f !important;font-size:14px;padding:10px 40px;color:#fff;border-radius:3px;">Login to Onepayroll</a>

                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </td>
			</tr>
		</tbody>
	</table>
</body>
</html>
