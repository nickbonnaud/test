<html lang="en">
<head>
<meta charset="utf-8">
<title>
{{ $profile->business_name }}
</title>
<style>
	.tips-top-section {
		font-family: "RealtimeRounded"; 
		padding-bottom: 20px;
	}
	.tips-business-logo {border-radius: 50%; width:70px; height:70px; margin-top: 20px; margin-left: 25px; float: left; display: inline-block; margin-bottom: 30px;
	}
	.tips-title-section {
		display: inline-block;
		float: right;
		position: absolute;
		margin-top: 40px;
		padding-left: 10px;
		padding-right: 10px;
	}
	.tips-business-title{ font-size: 16px; display: inline-block; margin-top: 70px; color: #3a3a3a;
	}
	.tips-main-section {
		clear: both;
	}
	.tips-receipt-section-header{ border-top: 2px solid #7f7f7f; border-bottom: 2px solid #7f7f7f; margin-left: 10px; margin-right: 10px;
	}
	.tips-receipt-section-header h4 { display: inline; margin-left: 10px; margin-right: 10px; font-size: 16px; margin-top: 4px; color: #3a3a3a;
	}
	.tips-receipt-section-body {margin-left: 10px; margin-right: 10px; color: #3a3a3a;
	}
	.tips-receipt-section-body h4 {display: inline; margin-left: 10px; margin-right: 10px; font-size: 14px;
	}
	.tips-item-name { float: left; clear: left;
	}
	.tips-item-amount { float: right; clear: right;
	}
	.tips-receipt-section-subtotal {
		clear: both;
	}
	.tips-tax-title { float: left; clear: left; margin-left: 0px; margin-right: 0px; margin-top: 10px;
	}
	.tips-tax-total { float: right; clear: right; margin-left: 0px; margin-right: 0px; margin-top: 10px;
	}
	.bill-total-section-individual { text-align: center; margin-left: 10px; margin-right: 10px; padding-top: 20px; border-top: 2px dotted #7f7f7f; margin-top: 70px; margin-bottom: 50px;
	}
	.tips-total-title { font-size: 24px; display: inline;
	}
	.tips-total-amount { font-size: 22px; float: right; margin-top: 0px; margin-bottom: 0px;
	}
  * {
    margin-bottom: 0px;
    padding: 0px;
    border: none;
    line-height: normal;
    outline: none;
    list-style: none;
    -webkit-text-size-adjust: none;
    -ms-text-size-adjust: none;}
  
  body {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    width: 100% !important;
    -webkit-text-size-adjust: 100% !important;
    -ms-text-size-adjust: 100% !important;
    -webkit-font-smoothing: antialiased !important;
  }
  table {border-collapse: collapse !important;  padding: 0px !important;  mso-table-lspace:0pt; mso-table-rspace: 0pt;}
  
  table td {border-collapse:collapse; mso-line-height-rule:exactly;}
  
  img {
    border: 0 !important;
    display: block !important;
    outline: none !important;}
  
  p, br {margin:0px; padding:0px;}
  
   .ExternalClass* {
  line-height: 100% !important;}
  
  .call_text a{
    text-decoration:none;
    color:#8f969f;}
  
  table .main_table{
    width:700px;}
  
  .mobile-only-spacer{
    display: table-cell;}
  
  
  @media only screen and (min-width : 600px) {
    .mobile-only-spacer {
    display: none;}
  }
  
  @media only screen and (min-width : 481px) and (max-width : 600px) {
  
  table[class=main_table]{
    width:480px !important;}
  
  table[class=wrapper]{
    width:100% !important;}
  
  td[class=hide], br[class=hide]{
    display:none !important;}
  
  td[class=mob_show] {
    display:table !important;
      float:none !important;
      width:100% !important;
      overflow:visible !important;
      height:auto !important;}
  
  div[class=mob_show] {
    display:table !important;
      float:none !important;
      width:100% !important;
      overflow:visible !important;
      height:auto !important;}
  
  td [class=txt_dvd]{
    text-align:center !important;}
  
  table[class=social], table[class=banner]{
    width:460px !important;}
  
  td [class=pad_top]{
    padding-top:15px !important;}
  
  td [class=spacer]{
    width:10px !important;}
  
  }
  
  @media only screen and (max-width : 480px){
  
  table[class=main_table]{
    width:320px !important;}
  
  table[class=wrapper]{
    width:100% !important;}
  
  td[class=hide], br[class=hide]{
    display:none !important;}
  
  td [class=order_txt]{
    font-size:12px !important;}
  
  table[class=social], table[class=banner]{
    width:300px !important;}
  
  td[class=mob_show] {
    display:table !important;
      float:none !important;
      width:100% !important;
      overflow:visible !important;
      height:auto !important;}
  
  div[class=mob_show] {
    display:table !important;
      float:none !important;
      width:100% !important;
      overflow:visible !important;
      height:auto !important;}
  
  td [class=txt_dvd]{
    text-align:center !important;}
  
  td [class=pad_top]{
    padding-top:15px !important;}
  
  td [class=spacer]{
    width:10px !important;}
  
  }
</style>

</head>
<body>


<table align="center" bgcolor="#424a4d" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="main_table" width="700">
<tr>
<td height="25"></td>
</tr>
<tr>
<td align="center" style="line-height:0px; font-size:0px;" valign="top">
<img alt="" border="0" style="display:block;" width="100%" src="{{ asset('/images/top-receipt.jpg') }}">
</td>
</tr>
<tr>
<td align="left" bgcolor="#ffffff" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="main_table" width="700">
<tr>
<td></td>
<td height="25"></td>
<td></td>
</tr>
<tr>
<td class="spacer" width="20"></td>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="center" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="266">
<tr>
<td align="center" style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:32px; font-style:bold; text-align:center; color:#1b2838; line-height:normal;" valign="top">
{{ $profile->business_name }}
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td height="8" style="line-height:0px; font-size:0px;"></td>
</tr>
</table>
</td>
<td class="spacer" width="20"></td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="left" bgcolor="#ffffff" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="main_table" width="700">
<tr>
<td height="8" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="main_table" width="700">
<tr>
<td class="spacer" width="20"></td>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="left" class="order_txt" style="font-family: Arial, sans-serif; font-size:14px; color:#8f969f; text-align:left; line-height:normal;" valign="top"></td>
<td width="5"></td>
<td align="right" class="order_txt" style="font-family: Arial, sans-serif; font-size:14px; color:#8f969f; text-align:right; line-height:normal;" valign="top"></td>
</tr>
</table>
</td>
<td class="spacer" width="20"></td>
</tr>
</table>
</td>
</tr>
<tr>
<td height="12" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="main_table" width="700">
<tr>
<td class="not-shown" width="18"></td>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="left" valign="top">
<table align="left" border="0" cellpadding="0" cellspacing="0" class="wrapper" width="320">
<tr>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="spacer" width="1"></td>
<td align="center" valign="top">

<!-- Actual receipt goes here. -->
<div style="padding-bottom: 20px;">
  <img style="border-radius: 50%; width:70px; height:70px; margin-top: 20px; margin-left: 25px; float: left; display: inline-block;" src="{{ $profile->logo->url }}">
  <h4 style="font-size: 16px; display: inline-block; margin-top: 70px; color: #3a3a3a;">Purchase on {{ date_format($transaction->updated_at, 'M-d-Y') }}</h4>
  <h4 style="font-size: 14px; margin-top: 0px">Receipt ID: {{ substr($transaction->splash_id, -5) }}</h4>
</div>
<div style="clear: both;">
  <div style="border-top: 2px solid #7f7f7f; border-bottom: 2px solid #7f7f7f; margin-left: 10px; margin-right: 10px;">
    <h4 style="display: inline; font-size: 16px; margin-top: 4px; color: #3a3a3a; float: left; clear: left; margin-bottom: 5px;">Item</h4>
    <h4 style="display: inline; font-size: 16px; margin-top: 4px; color: #3a3a3a; float: right; margin-bottom: 5px;">Amount</h4>
  </div>
  <div style="margin-left: 10px; margin-right: 10px; color: #3a3a3a">
    @foreach($items as $item)
      <h4 style="display: inline; font-size: 14px; float: left; clear: left; margin-top: 10px;">{{ $item->quantity }}x {{ $item->name }}</h4>
      <h4 style="display: inline; font-size: 14px; float: right; clear: right; margin-top: 10px;">${{ number_format((float)round(($item->price * $item->quantity) / 100, 2), 2) }}</h4>
    @endforeach
    <div style="clear: both;">
      <h4 style="float: left; clear: left; margin-left: 0px; margin-right: 0px; margin-top: 15px;">Tax</h4>
      <h4 style="float: right; clear: right; margin-left: 0px; margin-right: 0px; margin-top: 15px;">${{ number_format((float)round($transaction->tax / 100, 2), 2) }}</h4>
      @if($transaction->tips != null && $transaction->tips != 0)
        <h4 style="float: left; clear: left; margin-left: 0px; margin-right: 0px; margin-top: 10px;">Tip</h4>
        <h4 style="float: right; clear: right; margin-left: 0px; margin-right: 0px; margin-top: 10px;">${{ number_format((float)round($transaction->tips / 100, 2), 2) }}</h4>
      @endif
    </div>
  </div>
</div>
<div style="text-align: center; margin-left: 10px; margin-right: 10px; padding-top: 20px; border-top: 2px dotted #7f7f7f; margin-top: 70px; margin-bottom: 50px;">
  <h3 style="font-size: 24px; display: inline;">Total</h3>
  <h3 style="font-size: 22px; float: right; margin-top: 0px; margin-bottom: 0px;">${{ number_format((float)round($transaction->total / 100, 2), 2)  }}</h3>
</div>

</td>
<td class="spacer" width="1"></td>
</tr>
</table>
</td>
</tr>

</table>
<table align="right" border="0" cellpadding="0" cellspacing="0" class="wrapper" width="320">
<tr></tr>
<tr>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="social" style="border-radius:4px; background-color:#f6af33; min-width:320px;" width="320">
<tr>
<td></td>
<td height="12" style="line-height:0px; font-size:0px;"></td>
<td></td>
</tr>
<tr>
<td class="spacer" width="14"></td>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:100%;" width="100%">
<tr>
<td align="left" style="font-family: Arial, sans-serif; font-size:16px; color:#ffffff; text-align:left; line-height:23px;" valign="top">
Share Pockeyt with a Friend
<br>
<a style="text-decoration:underline; color:#ffffff;"  href="mailto:?body=Download link for Pockeyt: https://bnc.lt/igem/zeKtENCUhC">Email Link</a>
</td>
<td align="left" valign="top" width="27">
<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:27px;" width="27">
<tr>
<td height="8" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td align="left" style="line-height:0px; font-size:0px;" valign="top" width="27">
<a href="mailto:?body=Download link for Pockeyt: https://bnc.lt/igem/zeKtENCUhC"><img border="0" height="40" style="display:block; margin-right: -5px;" width="40" src="{{ asset('/images/white-logo-sq.png') }}" alt="Pockeyt"></a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td class="spacer" width="14"></td>
</tr>
<tr>
<td></td>
<td height="14" style="line-height:0px; font-size:0px;"></td>
<td></td>
</tr>
</table>
</td>
</tr>
<tr>
<td height="10" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="social" style="border-radius:4px; background-color:#8895a8; min-width:320px;" width="320">
<tr>
<td></td>
<td height="12" style="line-height:0px; font-size:0px;"></td>
<td></td>
</tr>
<tr>
<td class="spacer" width="14"></td>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:100%;" width="100%">
<tr>
<td align="left" style="font-family: Arial, sans-serif; font-size:16px; color:#ffffff; text-align:left; line-height:23px;" valign="top">
Email the Pockeyt team
<br>
<a style="text-decoration:underline; color:#ffffff;" href="mailto:info@pockeyt.com">Email us</a>
</td>
<td align="left" valign="top" width="30">
<table align="center" border="0" cellpadding="0" cellspacing="0" style="min-width:30px;" width="30">
<tr>
<td height="8" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td align="left" style="line-height:0px; font-size:0px;" valign="top" width="30">
<a href="mailto:info@pockeyt.com" ><img border="0" height="24" style="display:block;" width="30" src="{{ asset('/images/email-icon.png') }}" alt="Email"></a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td class="spacer" width="14"></td>
</tr>
<tr>
<td></td>
<td height="14" style="line-height:0px; font-size:0px;"></td>
<td></td>
</tr>
</table>
</td>
</tr>
</table>
<tr>
<td height="20" style="line-height:0px; font-size:0px"></td>
</tr>
</td>
</tr>
</table>
</td>
<td class="not-shown" width="18"></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="left" bgcolor="#f4f4f4" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" class="main_table" width="700">
<tr>
<td></td>
<td height="16" style="line-height:0px; font-size:0px;"></td>
<td></td>
</tr>
<tr>
<td class="spacer" width="20"></td>
<td align="left" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="center" class="call_text" style="font-family: Arial, sans-serif; font-size:14px; color:#8f969f; text-align:center; line-height:18px;" valign="top">
@if(isset($profile->account->bizStreetAdress))
{{ $profile->account->bizStreetAdress }}, {{ $profile->account->bizCity }}, {{ $profile->account->bizState }}, {{ $profile->account->bizZip }}
@endif
</td>
</tr>
<tr>
<td height="10" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td height="10" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td align="center" style="font-family: Arial, sans-serif; font-size:14px; color:#b1c300; text-align:center; line-height:18px;" valign="top">
<a href="http://{{$profile->website}}" style="text-decoration:none; color:#b1c300;" >{{ $profile->website }}</a>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td></td>
<td height="18" style="line-height:0px; font-size:0px;"></td>
<td></td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="center" style="line-height:0px; font-size:0px;" valign="top">
<img border="0" style="display:block;" width="100%" src="{{ asset('/images/bottom-receipt.jpg') }}" alt="Graphic footer">
</td>
</tr>
<tr>
<td align="center" valign="top">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="118">
<tr>
<td height="13" style="line-height:0px; font-size:0px;"></td>
</tr>
<tr>
<td align="left" style="font-family: Arial, sans-serif; font-size:14px; color:#8f969f; text-align:left; line-height:17px;" valign="top">Made Possible by</td>
</tr>
<tr>
<td align="left" valign="top">
<a href="http://pockeyt.com">
<img border="0" height="80" style="display:block; margin-left: 15px;" width="80" src="{{ asset('/images/full-logo.png') }}" alt="Shopkeep">
</a>
</td>
</tr>
<tr>
<td height="25" style="line-height:0px; font-size:0px;"></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>