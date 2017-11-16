<script>
	import VueMask from 'v-mask';
	import InputMoney from '../components/InputMoney.vue';
	
	Vue.use(VueMask);

	export default {
		props: ['account'],
		components: {InputMoney},

		data() {
			return {
				ownership: this.account.ownership,
        indivState: this.account.indivState,
        indivZip: this.account.indivZip,
        ssn: 'XXXXX' + this.account.ssn,
        bizTaxId: this.account.bizTaxId,
        annualCCSales: this.account.annualCCSales,
        bizState: this.account.bizState,
        bizZip: this.account.bizZip,
        phone: this.account.phone,
        routing: 'XXXXX' + this.account.routing,
			}
		}
	}
	
</script>