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
  <script src="{{ asset('js/app.js') }}"></script>
  <script type="text/javascript" src="https://test-api.splashpayments.com/payFrameScript"></script>
  <script>
		PaymentFrame.onSuccess = function (res) {
      var token = res.response.data[0].token;
      var number = res.response.data[0].payment.number;
      var method = res.response.data[0].payment.method;
      this.sendResults(token, number, method);
    };

    PaymentFrame.onFailure = function (response) {
      console.log(response);
    };

		PaymentFrame.config.apiKey = "6c5efd94b04e7ddc049ac0147c0fab01";
    PaymentFrame.config.mode = "token";
    PaymentFrame.config.name = "Pockeyt Card Vault";
    PaymentFrame.config.description = "Address & Phone Optional";
    PaymentFrame.config.billingAddress = { email: '{{ $user->email }}' };
    PaymentFrame.config.image = "https://pockeyt-test.com/images/pockeyt-icon-square.png";
		
		document.addEventListener("DOMContentLoaded", function(event) {
      PaymentFrame.popup();
    });    

		sendResults = function(token, number, method) {
      $.ajax({
        method: 'POST',
        url: '/api/mobile/card/vault/{{ $user->id }}',
        headers: { 'Authorization': 'Bearer {{ $user->token }}' },
        data: {
          'token' : token,
          'numberLastFour' : number,
          'cardType' : method,
        },
        success: function(data) {
          console.log(data);
          if (data == true) {
            window.location.replace("mobile/close/success");
          } else {
            window.location.replace("mobile/close/fail");
          }
        },
        error: function(data) {
          console.log(data);
        }
      })
    };
  </script>
</body>
</html>
