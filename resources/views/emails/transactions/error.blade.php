<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>
			Error Notification
		</title>
	</head>
	<body>
		<h3>Customer: {{ $user->first_name }} {{ $user->last_name }}</h3>
		<h4>Email: {{ $user->email }}</h4>
		<hr>
		<h3>Business: {{ $profile->business_name }}</h3>
		<h4>Business Phone: {{ $profile->account->phone }}</h4>
		<hr>
		<h3>Error: {{ $msg }}</h3>
		<h3>Code: {{ $code }}</h3>
		<h4>Splash ID: {{ $transactionSplashId }}</h4>
		<h4>Transaction ID: {{ $transaction->id }}</h4>
	</body>
</html>