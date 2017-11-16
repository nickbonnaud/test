
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('customer-geofence', require('./components/CustomerGeofence.vue'));
Vue.component('customer-reward', require('./components/CustomerReward.vue'));
Vue.component('transaction-error', require('./components/TransactionError.vue'));
Vue.component('transaction-success', require('./components/TransactionSuccess.vue'));
Vue.component('customer-request-bill', require('./components/CustomerRequestBill.vue'));
Vue.component('sidebar-pending', require('./components/SidebarPending.vue'));
Vue.component('sidebar-finalized', require('./components/SidebarFinalized.vue'));
Vue.component('transactions-change', require('./components/TransactionsChange.vue'));
Vue.component('input-money', require('./components/InputMoney.vue'));
Vue.component('debug', require('./components/Debug.vue'));


Vue.component('create-loyalty', require('./pages/CreateLoyalty.vue'));
Vue.component('deals', require('./pages/Deals.vue'));
Vue.component('sales-history', require('./pages/SalesHistory.vue'));
Vue.component('team', require('./pages/Team.vue'));
Vue.component('dashboard-analytics', require('./pages/DashboardAnalytics.vue'));
Vue.component('dashboard-main', require('./pages/DashboardMain.vue'));
Vue.component('bill', require('./pages/Bill.vue'));
Vue.component('connections', require('./pages/Connections.vue'));
Vue.component('products', require('./pages/Products.vue'));
Vue.component('account', require('./pages/Account.vue'));


const app = new Vue({
    el: '#app'
});
