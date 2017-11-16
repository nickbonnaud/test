<script>
	
	import SalesHistoryNet from '../components/SalesHistoryNet.vue';
	import SalesHistoryTax from '../components/SalesHistoryTax.vue';
	import SalesHistoryTip from '../components/SalesHistoryTip.vue';
	import SalesHistoryTotal from '../components/SalesHistoryTotal.vue';
	import DateRangePicker from '../components/DateRangePicker.vue';
	import Modal from '../components/Modal.vue';
	import EmployeeTipTracking from '../components/EmployeeTipTracking.vue';

	export default {
		props: ['sales', 'teamMembers', 'profileSlug'],
		components: {SalesHistoryNet, SalesHistoryTax, SalesHistoryTip, SalesHistoryTotal, DateRangePicker, Modal, EmployeeTipTracking},

		data() {
			return {
				fromDate: "today",
				toDate: "",
				modalPick: "",
				modalPickData: "",
				customDate: false,
				transactions: this.sales,
				employees: this.teamMembers
			}
		},

		mounted() {
			VueEvent.listen('dateRangeChanged', this.getTransactions.bind(this));
			VueEvent.listen('showModalSalesHistory', this.setModalData.bind(this));
		},

		methods: {

			toggleDate() {
				this.customDate = !this.customDate;
			},

			getTransactions(picker) {
				var startDate = picker.startDate.format();
				var endDate = picker.endDate.format();
				this.fromDate = picker.startDate.format('MMM Do, YY');
				this.toDate = picker.endDate.format('MMM Do, YY');

				axios.get("/api/web/sales/" + this.profileSlug + "?customDate[]=" + startDate + "&customDate[]=" + endDate)
					.then(this.refresh);
			},

			refresh({data}) {
				console.log(data.employees)
				this.transactions = data.sales;
				this.employees = data.employees;
			},

			setModalData(data) {
				this.modalPick = data.modalType;
				this.modalPickData = data.modalData;
			}
		}
	}
	
</script>