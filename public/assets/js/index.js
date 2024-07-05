$(function () {
	"use strict";


	// chart 1
  const chart1Elem = document.querySelector("#chart1");
  const chart1Config = JSON.parse(chart1Elem.getAttribute("data-config"));
  console.log(chart1Config);

	var options = {
		series: [{
			name: 'Entradas',
			data: chart1Config.map((data) => data.amount)
		}],
		chart: {
			foreColor: '#9ba7b2',
			height: 330,
			type: 'bar',
			zoom: {
				enabled: false
			},
			toolbar: {
				show: false
			},
		},
		stroke: {
			width: 0,
			curve: 'smooth'
		},
		plotOptions: {
			bar: {
				horizontal: !1,
				columnWidth: "30%",
				endingShape: "rounded"
			}
		},
		grid: {
			borderColor: 'rgba(255, 255, 255, 0.15)',
			strokeDashArray: 4,
			yaxis: {
				lines: {
					show: true
				}
			}
		},
		fill: {
			type: 'gradient',
			gradient: {
			  shade: 'light',
			  type: 'vertical',
			  shadeIntensity: 0.5,
			  gradientToColors: ['#01e195'],
			  inverseColors: true,
			  opacityFrom: 1,
			  opacityTo: 1,
			}
		  },
		colors: ['#0d6efd'],
		dataLabels: {
			enabled: false,
			enabledOnSeries: [1]
		},
		xaxis: {
			categories: chart1Config.map((data) => data.period),
		},
	};
	var chart = new ApexCharts(chart1Elem, options);
	chart.render();




// chart 2
const chart2Elem = document.querySelector("#chart2");
const chart2Config = JSON.parse(chart2Elem.getAttribute("data-config"));

var options = {
	series: [chart2Config.totalEntry, chart2Config.totalExit, chart2Config.totalDebts],
	chart: {
		height: 255,
		type: 'donut',
	},
	legend: {
		position: 'bottom',
		show: false,
	},
	plotOptions: {
		pie: {
			// customScale: 0.8,
			donut: {
				size: '80%'
			}
		}
	},
	colors: [ "#198754", "#dc3545", "#ffc107"],
	dataLabels: {
		enabled: false
	},
	labels: ['Entradas', 'saídas', 'Dívidas'],
	responsive: [{
		breakpoint: 480,
		options: {
			chart: {
				height: 200
			},
			legend: {
				position: 'bottom'
			}
		}
	}]
};
var chart = new ApexCharts(chart2Elem, options);
chart.render();



  // chart 3

	var options = {
		series: [{
			name: 'Monthly Views',
			data: [10, 25, 42, 12, 55, 30, 63, 27, 20]
		}],
		chart: {
			foreColor: '#9ba7b2',
			height: 250,
			type: 'line',
			zoom: {
				enabled: false
			},
			toolbar: {
				show: false
			},
		},
		stroke: {
			width: 4,
			curve: 'smooth'
		},
		plotOptions: {
			bar: {
				horizontal: !1,
				columnWidth: "30%",
				endingShape: "rounded"
			}
		},
		grid: {
			borderColor: 'rgba(255, 255, 255, 0.15)',
			strokeDashArray: 4,
			yaxis: {
				lines: {
					show: true
				}
			}
		},
		fill: {
			type: 'gradient',
			gradient: {
			  shade: 'light',
			  type: 'vertical',
			  shadeIntensity: 0.5,
			  gradientToColors: ['#01e195'],
			  inverseColors: true,
			  opacityFrom: 1,
			  opacityTo: 1,
			}
		  },
		colors: ['#0d6efd'],
		dataLabels: {
			enabled: false,
			enabledOnSeries: [1]
		},
		xaxis: {
			categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
		},
	};
	var chart = new ApexCharts(document.querySelector("#chart3"), options);
	chart.render();






  // chart 4

  var options = {
	series: [{
		name: 'Monthly Users',
		data: [2, 45, 30, 80, 55, 30, 63, 27, 5]
	}],
	chart: {
		foreColor: '#9ba7b2',
		height: 250,
		type: 'area',
		zoom: {
			enabled: false
		},
		toolbar: {
			show: false
		},
	},
	stroke: {
		width: 3,
		curve: 'smooth'
	},
	plotOptions: {
		bar: {
			horizontal: !1,
			columnWidth: "30%",
			endingShape: "rounded"
		}
	},
	grid: {
		borderColor: 'rgba(255, 255, 255, 0.15)',
		strokeDashArray: 4,
		yaxis: {
			lines: {
				show: true
			}
		}
	},
	fill: {
		type: 'gradient',
		gradient: {
		  shade: 'light',
		  type: 'vertical',
		  shadeIntensity: 0.5,
		  gradientToColors: ['#01e195'],
		  inverseColors: false,
		  opacityFrom: 0.8,
		  opacityTo: 0.2,
		}
	  },
	colors: ['#0d6efd'],
	dataLabels: {
		enabled: false,
		enabledOnSeries: [1]
	},
	xaxis: {
		categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
	},
};
var chart = new ApexCharts(document.querySelector("#chart4"), options);
chart.render();









});
