<template>
	<div class="box-body">
		<div class="table-responsive">
			<table class="table no-margin">
				<thead>
					<tr>
						<th class="text-center">Company</th>
						<th class="text-center">Service</th>
						<th class="text-center">Connection Status</th>
						<th class="text-center">Feature Status</th>
					</tr>
				</thead>
				<tbody>
					<connect-facebook v-bind:profile="this.profile"></connect-facebook>
					<connect-instagram v-bind:profile="this.profile"></connect-instagram>
					<connect-square-inventory v-if="this.profile.account" v-bind:profile="this.profile"></connect-square-inventory>
					<connect-square-pockeyt-lite v-if="this.profile.account" v-bind:profile="this.profile"></connect-square-pockeyt-lite>
					<connect-quickbooks v-if="this.profile.account" v-bind:profile="this.profile"></connect-quickbooks>
				</tbody>
			</table>
		</div>
	</div>
</template>
<script>

	import ConnectFacebook from '../components/ConnectFacebook.vue';
	import ConnectInstagram from '../components/ConnectInstagram.vue';
	import ConnectSquareInventory from '../components/ConnectSquareInventory.vue';
	import ConnectSquarePockeytLite from '../components/ConnectSquarePockeytLite.vue';
	import ConnectQuickbooks from '../components/ConnectQuickbooks.vue'
	import swal from 'sweetalert2';
	
	export default {
		props: ['profileInitial'],
		components: {ConnectFacebook, ConnectInstagram, ConnectSquareInventory, ConnectSquarePockeytLite, ConnectQuickbooks},

		data() {
			return {
				profile: this.profileInitial
			}
		},

		mounted() {
			VueEvent.listen('toggleConnection', this.updateConnections.bind(this));
		},

		methods: {

			updateConnections(connectionData) {
				axios.patch('/api/web/connections/' + this.profile.slug, connectionData)
          .then(this.checkResponse);
			},

			checkResponse({data}) {
				VueEvent.fire('doneLoading');
				if (data.url) {
					this.redirect(data.url);
				} else if (data.squareResult) {
					this.updateProfile(data.profile);
					this.flashSquareResult(data.squareResult);
				} else if (data.qboResult) {
					this.updateProfile(data.profile);
					this.flashQboResult(data.qboResult);
				} else {
					this.updateProfile(data.profile);
				}
			},

			redirect(url) {
				window.location.replace(url);
			},

			updateProfile(profile) {
				this.profile = profile;
			},

			getAccountUrl() {
				return (window.location.href).replace('connections', 'accounts');
			},

			flashSquareResult(result) {
				switch(result) {
          case 'no_match':
          	this.flashNoMatch();
          	break;
          case 'success_location':
          	this.flashInventory();
          	break;
          case 'success_lite':
          	this.flashLite();
          	break;
        }
			},

			flashNoMatch() {
				swal({
					title: 'Oops! Your business address in Pockeyt does not match your address in Square',
					text: 'Your street address in Pockeyt is ' + this.profile.account.bizStreetAddress + '. Please change your address in Pockeyt or Square to match in order to continue.',
					type: 'error',
					showConfirmButton: true,
					showCancelButton: true,
					confirmButtonText: 'Go to Account',
					cancelButtonText: 'Cancel',
				}).then(function() {
						window.location.replace(this.getAccountUrl());
				}.bind(this));
			},

			flashInventory() {
				swal({
					title: 'Success! Inventory Import Enabled',
					text: 'You can now import inventory from Square to Pockeyt using the Sync button in your inventory page.',
					type: 'success',
					showConfirmButton: true,
					confirmButtonText: 'OK',
					timer: 3000,
				}).then(
					function() {},
					function(dismiss) {}
				)
			},

			flashLite() {
				swal({
					title: 'Success! Pockeyt Lite Enabled',
					text: 'Pockeyt Customers will now appear in the bottom right corner of your first Favorite Items Page. Include Pockeyt Customer in the bill (as if they were a product) and then pay with Other, we will handle the rest!',
					type: 'success',
					showConfirmButton: true,
					confirmButtonText: 'OK',
				})
			},

			flashTaxNotSet() {
				swal({
					title: 'Sales Tax Rate not set in QuickBooks.',
					text: 'Your sales tax rate in QuickBooks is not set. Please set your sales tax rate to ' + (this.profile.tax.total  / 100).toFixed(2) + '% in your QuickBooks Account.',
					type: 'error',
					showConfirmButton: true,
					confirmButtonText: 'OK',
				});
			},

			flashTaxNotMatch() {
				swal({
					title: 'QuickBooks Sales Tax Rate does not match Sales Tax Rate with Pockeyt.',
					text: 'Your sales tax rate in QuickBooks does not match what Pockeyt has set for your Sales Tax Rate. Please set your sales tax rate to ' + (this.profile.tax.total  / 100).toFixed(2) + '% in your QuickBooks Account. If that is not your correct sales tax rate please contact Pockeyt.',
					type: 'error',
					showConfirmButton: true,
					confirmButtonText: 'OK',
				});
			},

			qboSuccess() {
				swal({
					title: 'Success! Pockeyt Sync with QuickBooks Enabled!',
					text: 'Your transactions in Pockeyt will automatically be synced with your QuickBooks account.',
					type: 'success',
					showConfirmButton: true,
					confirmButtonText: 'OK',
				});
			},

			flashQboResult(result) {
				switch(result) {
          case 'qbo_tax_not_set':
          	this.flashTaxNotSet();
          	break;
          case 'qbo_not_match':
          	this.flashTaxNotMatch();
          	break;
          case 'success':
          	this.qboSuccess();
          	break;
        }
			}
		}
	}
</script>