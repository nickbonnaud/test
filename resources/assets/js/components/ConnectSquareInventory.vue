<template>
	<tr>
		<td class="text-center"><span class="icon-square-connect"></span></td>
		<td class="text-center">Inventory Import</td>
		<td v-show="this.isConnected()" class="text-center"><span class="label label-success">Connected</span></td>
		<td v-show="!this.isConnected()" class="text-center">
			<a v-on:click="toggleConnection()" class="btn btn-block btn-social btn-github">
				<i class="fa fa-square-o"></i>
				Connect With Square
			</a>
		</td>
		<td v-show="this.isEnabled()" class="text-center"><span class="label label-primary">Enabled while connected</span></td>
		<td v-show="this.isConnected() && !this.profile.account.square_location_id" class="text-center">
			<a v-on:click="toggleConnection()">
				<button class="btn btn-success">
					<i v-show="isLoading" class="fa fa-spinner fa-spin"></i>
					Enable
				</button>
			</a>
		</td>
		<td v-show="!this.isConnected()" class="text-center"><button class="btn btn-success disabled" disabled>Enable</button></td>
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

		methods: {
			doneLoading() {
				this.isLoading = false;
			},

			isConnected() {
				return this.profile.square_token;
			},

			isEnabled() {
				if (this.profile.square_token && this.profile.account.square_location_id) {
					return true;
				} else {
					return false;
				}
			},

			toggleConnection() {
				var data = {
					action: 'enable',
					company: 'square',
					feature: 'inventory'
				};
				this.isLoading = true;
				VueEvent.fire('toggleConnection', data);
			}
		}
	}
</script>