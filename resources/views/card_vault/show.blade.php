<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0' >
  <title>Card Info</title>
</head>
<body>
  <h1 style="left: 0; line-height: 200px; margin-top: -100px; position: absolute; text-align: center; top: 50%; width: 100%;" id="app">Loading...</h1>
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script type="text/javascript" src="https://test-api.splashpayments.com/payFrameScript"></script>
  <script>
		PayFrame.onSuccess = function (res) {
			console.log(res);
      var token = res.response.data[0].token;
      var numberLastFour = res.response.data[0].payment.number;
      sendResults(token, numberLastFour);
    };

    PayFrame.onFailure = function (response) {
      console.log(response);
    };

		PayFrame.config.apiKey = "6c5efd94b04e7ddc049ac0147c0fab01";
		PayFrame.config.merchant = "g15952a377ce686";
    PayFrame.config.mode = "token";
    PayFrame.config.name = "Pockeyt Card Vault";
    PayFrame.config.description = "Address & Phone Optional";
    PayFrame.config.image = "https://pockeyt-test.com/images/pockeyt-icon-square.png";
    PayFrame.config.billingAddress = {
    	email: 'test@email.com'
    };
		
		document.addEventListener("DOMContentLoaded", function(event) {
      PayFrame.popup();
    });    

		sendResults = function(token, numberLastFour) {
      $.ajax({
        method: 'POST',
        url: '/api/mobile/card/vault/{{$user->id}}',
        headers: { 'Authorization': 'Bearer {{$user->token}}' },
        data: {
          'token' : token,
          'numberLastFour' : numberLastFour,
        },
        success: function(data) {
          console.log(data);
          if (data) {
            window.location.replace("vault/close/success");
          } else {
            window.location.replace("vault/close/fail");
          }
        },
        error: function(data) {
          console.log(data);
        }
      });
    };
  </script>
</body>
</html>
