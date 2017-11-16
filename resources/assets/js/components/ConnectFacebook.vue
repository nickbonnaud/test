<template>
	<tr>
		<td class="text-center"><span class="icon-fb-connect"></span></td>
		<td class="text-center">Auto Posting</td>
		<td v-show="this.isConnected()" class="text-center"><span class="label label-success">Connected</span></td>
		<td v-show="this.isConnected()" class="text-center">
			<a v-on:click="toggleConnection()">
				<button v-bind:class="btnClass()">
					<i v-show="isLoading" class="fa fa-spinner fa-spin"></i>
					{{ enabled }}
				</button>
			</a>
		</td>
		<td v-show="!this.isConnected()" class="text-center">
			<a v-on:click="toggleConnection()" class="btn btn-block btn-social btn-facebook">
	      <i class="fa fa-facebook"></i>
	      Connect With Facebook
  		</a>
  	</td>
		<td v-show="!this.isConnected()" class="text-center"><button v-bind:class="btnClass()" disabled>Enable</button></td>
	</tr>
</template>
<script>

	export default {
		props: ['profile'],

		data() {
			return {
				isLoading: false
			}
		},

		mounted() {
			VueEvent.listen('doneLoading', this.doneLoading.bind(this));
		},

		computed: {
			enabled() {
				if (this.isConnectedTo() == 'facebook') {
					return "Disable";
				} else {
					return "Enable";
				}
			}
		},

		methods: {
			doneLoading() {
				this.isLoading = false;
			},

			isConnected() {
				return this.profile.fb_app_id;
			},

			isConnectedTo() {
				return this.profile.connected;
			},

			btnClass() {
				if (this.isConnected()) {
					if (this.isConnectedTo() == 'facebook') {
						return "btn btn-danger";
					} else if (!this.isConnectedTo()) {
						return "btn btn-success";
					} else {
						return "btn btn-success disabled";
					}
				} else {
					return "btn btn-success disabled";
				}
			},

			toggleConnection() {
				if (this.isConnectedTo() == 'instagram') {return;}
				var data = {
					action: this.enabled,
					company: 'facebook'
				};
				this.isLoading = true;
				VueEvent.fire('toggleConnection', data);
			}
		}
	}
</script>