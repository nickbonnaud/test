<?php

namespace App\Http\Controllers\Api\Web;

use Socialite;
use App\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConnectionRequest;

class ConnectionsController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function update(Profile $profile, UpdateConnectionRequest $request) {
		$this->authorize('update', $profile);
		$service = strtolower($request->input('company'));
		$action = strtolower($request->input('action'));
		$serviceConnected = $service . 'Connected';
		if ((!$profile->$serviceConnected()) && ($action == 'enable')) {
			$serviceRedirect = $service . 'Redirect';
			return $this->$serviceRedirect();
		} else {
			$serviceUpdate = $service . 'Update';
			if ($service == 'square') {
				$result = $profile->$serviceUpdate($action, $request->input('feature'));
				return response()->json(['profile' => $profile->withAccountandTax(), 'squareResult' => $result]);
			} elseif ($service == 'qbo') {
				$result = $profile->$serviceUpdate($action);
				return response()->json(['profile' => $profile->withAccountandTax(), 'qboResult' => $result]);
			}
			$profile->$serviceUpdate($action);
			return response()->json(['profile' => $profile->withAccountandTax()]);
		}
	}


	public function facebookRedirect() {
		return response()->json(['url' => Socialite::with('facebook')
			->fields(['accounts'])->scopes(['pages_show_list', 'manage_pages'])->redirect()->getTargetUrl()]) ;
	}

	public function instagramRedirect() {
		return response()->json(['url' => Socialite::driver('instagram')
			->redirect()->getTargetUrl()]);
	}

	public function squareRedirect() {
		return response()->json(['url' => 'https://connect.squareup.com/oauth2/authorize?client_id=' . env('SQUARE_ID') . '&scope=ITEMS_READ%20ITEMS_WRITE%20MERCHANT_PROFILE_READ%20PAYMENTS_READ&state=' . env('SQUARE_STATE')]);
	}

	public function connectFacebook(Request $request) {
		$profile = auth()->user()->profile;
		$profile = $profile->receiveFBData($request->has('code'));
		flash()->success('Success', 'Auto updates enabled for facebook');
		return redirect()->route('connections.show', ['profile' => auth()->user()->profile->slug]);
	}

	public function connectInstagram(Request $request) {
		$profile = auth()->user()->profile;
		$profile = $profile->receiveInstaData($request->has('code'));
		return redirect()->route('connections.show', ['profile' => auth()->user()->profile->slug]);
	}

	public function connectSquare(Request $request) {
		$profile = auth()->user()->profile;
		$profile = $profile->receiveSquareData($request->all());
		flash()->success('Success', 'You can know enable inventory imports from Square');
		return redirect()->route('connections.show', ['profile' => auth()->user()->profile->slug]);
	}

	public function connectQbo(Request $request) {
		$profile = auth()->user()->profile;
		$result = $profile->receiveQuickbooksData();
		if ($result == 'success') {
			return view('quickbooks.success');
		} else {
			return view('quickbooks.tax', compact('result'));
		}
	}
}
