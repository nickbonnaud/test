export default {
	data() {
		return {
			barChartOptionsInteractions: this.setBarChartOptionsInteractions(),
			barChartOptionsRevenue: this.setBarChartOptionsRevenue(),
			lineChartOptions: this.setLineChartOptions(),
		}
	},

	methods: {
		
		setBarChartOptionsInteractions() {
			return {
		    scaleShowGridLines: true,
		    scaleGridLineColor: "rgba(0,0,0,.05)",
		    scaleGridLineWidth: 1,
		    scaleShowHorizontalLines: true,
		    scaleShowVerticalLines: false,
		    barShowStroke: true,
		    barStrokeWidth: 2,
		    barValueSpacing: 5,
		    barDatasetSpacing: 1,
		    responsive: true,
		    maintainAspectRatio: true,
		    scales: {
		    	yAxes: [{
		    		ticks: {
		    			beginAtZero: true
		    		}
		    	}]
		    }
			};
		},

		setBarChartOptionsRevenue() {
			return {
		    scaleShowGridLines: true,
		    scaleGridLineColor: "rgba(0,0,0,.05)",
		    scaleGridLineWidth: 1,
		    scaleShowHorizontalLines: true,
		    scaleShowVerticalLines: false,
		    barShowStroke: true,
		    barStrokeWidth: 2,
		    barValueSpacing: 5,
		    barDatasetSpacing: 1,
		    responsive: true,
		    maintainAspectRatio: true,
		    scales: {
		    	yAxes: [{
		    		ticks: {
		    			beginAtZero: true,
		    			callback: function(value, index, values) {
		    				value = value.toFixed(2);
		            if(value >= 1000){
		              return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		            } else {
		              return '$' + value;
		            }
		          } 
		    		}
		    	}]
		    }
			};
		},

		setLineChartOptions() {
			return {
				responsive: true,
		    maintainAspectRatio: true,
				scales: {
					yAxes: [{
		    		ticks: {
		    			beginAtZero: true,
		    			callback: function(value, index, values) {
		            return value.toFixed(0) + '%';
		          } 
		    		}
		    	}],
		      xAxes: [{
		        ticks: {
		          autoSkip: true,
		          autoSkipPadding: 5
		        }
		      }]
		    }
			};
		}
	}
}