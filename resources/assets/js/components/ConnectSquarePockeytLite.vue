<template>
	<tr>
		<td class="text-center"><span class="icon-square-connect"></span></td>
		<td class="text-center">Pockeyt Lite</td>
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
			<a v-on:click="toggleConnection()" class="btn btn-block btn-social btn-github">
				<i class="fa fa-square-o"></i>
				Connect With Square
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
				if (this.profile.account.pockeyt_lite_enabled) {
					return "Disable";
				} else {
					return 'Enable';
				}
			}
		},

		methods: {
			doneLoading() {
				this.isLoading = false;
			},


			isConnected() {
				return this.profile.square_token;
			},

			btnClass() {
				if (this.isConnected()) {
					if (this.profile.account.pockeyt_lite_enabled) {
						return "btn btn-danger";
					} else {
						return "btn btn-success";
					}
				} else {
					return "btn btn-success disabled";
				}
			},

			toggleConnection() {
				var data = {
					action: this.enabled,
					company: 'square',
					feature: 'pockeytLite'
				};
				this.isLoading = true;
				VueEvent.fire('toggleConnection', data);
			}
		}
	}
</script>