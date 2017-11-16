<template>
	<div class="auto-post">
    <a v-on:click="sync()">
      <button type="button" class="btn btn-social btn-github">
        <i class="fa fa-sign-in"></i>
        Sync
      </button>
    </a>
  </div>
</template>

<script>
	import swal from 'sweetalert2';
	
	export default {
		props: ['profile'],

		data() {
			return {
				title: '',
				message: '',
				status: '',
				buttonText: '',
				url: ''
			}
		},

		methods: {
			sync() {
				axios.get('/api/web/products/' + this.profile.slug + '/sync')
	        .then(this.handleResult);
			},

			handleResult({data}) {
				switch(data.result) {
					case 'not_connected':
						this.title = 'Not connected to Square';
						this.message = 'Please connect your Pockeyt account to Square in the Account Connections tab.';
						this.buttonText = 'Go';
						this.url = '/connections/' + this.profile.slug;
						this.status = 'error';
						break;
					case 'no_account':
						this.title = 'No Pockeyt Pay Account';
						this.message = 'Please create your Pockeyt Pay Account before connecting to Square.';
						this.buttonText = 'Go';
						this.url = '/accounts/' + this.profile.slug + '/create';
						this.status = 'error';
						break;
					case 'location_not_set':
						this.title = 'Connection with Square not complete';
						this.message = 'Please finish connecting your Pockeyt account to Square.';
						this.url = '/connections/' + this.profile.slug;
						this.buttonText = 'Go';
						this.status = 'error';
						break;
					case 'no_inventory':
						this.title = 'No inventory on your Square Account';
						this.message = 'There is no inventory saved on your Square account.';
						this.buttonText = 'OK';
						this.status = 'error';
						break;
					case 'success':
						this.title = 'Successfully Synced!';
						this.message = 'Your inventory on Pockeyt has been updated.';
						this.buttonText = 'OK';
						this.status = 'success';
						break;
				}
				this.flashMessage();
			},

			flashMessage() {
				if (this.buttonText == 'OK') {
					if (this.status == 'error') {
						swal({
							title: this.title,
							text: this.message,
							type: this.status,
							showConfirmButton: true,
							confirmButtonText: this.buttonText
						});
					} else {
						swal({
							title: this.title,
							text: this.message,
							type: this.status,
							showConfirmButton: true,
							confirmButtonText: this.buttonText,
							timer: 1000,
						}).then(
							function() {},
							function(dismiss) {
								return window.location.reload();
							}.bind(this)
						)
					}
				} else {
					swal({
						title: this.title,
						text: this.message,
						type: this.status,
						showConfirmButton: true,
						showCancelButton: true,
						confirmButtonText: this.buttonText,
						cancelButtonText: 'Cancel',
					}).then(function() {
						window.location.replace(this.getFullUrl());
					}.bind(this));
				}
			},

			getFullUrl() {
				return (window.location.href).replace('/products/' + this.profile.slug, '') + this.url;
			}
		}
	}
</script>