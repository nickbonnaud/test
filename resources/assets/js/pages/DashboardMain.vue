<script>

	import CustomerList from '../components/CustomerList.vue';
	import SearchDefault from '../components/SearchDefault.vue';
	import CustomerInfoModal from '../components/CustomerInfoModal.vue';
	import EmployeeSelectModal from '../components/EmployeeSelectModal.vue';
	import DealModal from '../components/DealModal.vue';

	export default {
		props: ['profileSlug'],
		components: {CustomerList, SearchDefault, CustomerInfoModal, EmployeeSelectModal, DealModal}
	}
</script>