<template>
	<form :class="this.searchType + '-search'">
    <div class="input-group">
      <span class="input-group-addon"><i class="fa fa-search"></i></span>
      <input style="font-size: 28px; padding: 0px;" type="text" name="query" class="form-control" placeholder="Search" v-model="query">
    </div>
  </form>
</template>

<script>
	export default {
		props: ['searchType'],

		data() {
			return {
				query: ''
			}
		},

		watch: {
			query: _.debounce(function () {
	    	this.sendQuery()
	    }, 1000)
		},

		methods: {
			sendQuery() {
				VueEvent.fire(this.searchType + 'QueryChange', this.query);
			}
		}
	}

</script>