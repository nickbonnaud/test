<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>
			Error Notification
		</title>
	</head>
	<body>
		<h3>Customer: {{ $transaction->user->first_name }} {{ $transaction->user->last_name }}</h3>
		<h4>Email: {{ $transaction->user->email }}</h4>
		<hr>
		<h3>Business: {{ $transaction->profile->business_name }}</h3>
		<h4>Business Phone: {{ $transaction->profile->account->phone }}</h4>
		<hr>
		<h3>Field: {{ $error[0]['field'] }}</h3>
		<h3>Message: {{ $error[0]['msg'] }}</h3>
		<h3>Code: {{ $error[0]['code'] }}</h3>
		<h4>Transaction ID: {{ $transaction->id }}</h4>
	</body>
</html>