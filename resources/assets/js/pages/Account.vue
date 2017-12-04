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
        indiv_state: this.account.indiv_state,
        indiv_zip: this.account.indiv_zip,
        ssn: 'XXXXX' + this.account.ssn,
        biz_tax_id: this.account.biz_tax_id,
        annual_cc_sales: this.account.annual_cc_sales,
        biz_state: this.account.biz_state,
        biz_zip: this.account.biz_zip,
        phone: this.account.phone,
        routing: 'XXXXX' + this.account.routing,
			}
		}
	}
	
</script>