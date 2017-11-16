<template>
	<tr>
		<td class="text-center"><span class="icon-quickbooks-connect"></span></td>
		<td class="text-center">Pockeyt Sync</td>
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
			<ipp:connectToIntuit></ipp:connectToIntuit>
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
				if (this.profile.account.pockeyt_qb_taxcode) {
					return "Disable";
				} else {
					return "Set Sales Tax";
				}
			}
		},

		methods: {
			doneLoading() {
				this.isLoading = false;
			},

			btnClass() {
				if (this.isConnected()) {
					if (this.profile.account.pockeyt_qb_taxcode) {
						return "btn btn-danger";
					} else {
						return "btn btn-success";
					}
				} else {
					return "btn btn-success disabled";
				}
			},

			isConnected() {
				return this.profile.connected_qb;
			},

			toggleConnection() {
				var data = {
					action: this.enabled,
					company: 'qbo'
				};
				this.isLoading = true;
				VueEvent.fire('toggleConnection', data);
			}
		}
	}
</script>