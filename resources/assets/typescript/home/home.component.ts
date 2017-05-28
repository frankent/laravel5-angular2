import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { HomeService } from "../services/home.service";
const timeseries = require('timeseries-analysis');

@Component({
	styles: [`
      chart {
        display: block;
      }
    `],
	template: require('./home.component.html')
})
export class HomeComponent implements OnInit {
	optionsTubidity = {};
	optionsRainHour = {};
	optionsHumidity = {};

	@ViewChild("radarMap") canvasRef: ElementRef; 

    constructor(private homeService: HomeService) { }

	ngOnInit() {
        this.getData();
		this.drawRadarMap();
	}

    getData() {
        const dataList = this.homeService.getDataList();
        dataList.then((response) => {
			this.drawTubidity(response);
			this.drawRainPerHour(response);
			this.drawHumid(response);
        });
    }

	drawRainPerHour(stream) {
		const allUnix = [];
		const rainDiffHour = [];
		const rainDiff30Min = [];
		const label = [];
		Object.keys(stream.data).forEach((unix) => {
			allUnix.push(unix);
		});

		allUnix.reverse();
		allUnix.forEach((unix, index) => {
			if (index + 4 < allUnix.length) {
				const currentData = stream.data[unix];
				currentData.rainDiffHourHour = currentData.total_rain - stream.data[allUnix[index + 4]].total_rain;
				rainDiffHour.push(currentData.total_rain - stream.data[allUnix[index + 4]].total_rain);
				rainDiff30Min.push(currentData.total_rain - stream.data[allUnix[index + 2]].total_rain);
				label.push(currentData.timestamp);
			}
		});

		this.optionsRainHour = {
			chart: {
				type: 'line',
				zoomType: 'x'
			},
			title: {
				text: 'Rain'
			},
			xAxis: {
				categories: label
			},
			yAxis: {
				title: {
					text: 'mm.'
				}
			},
			legend: {
				enabled: true
			},
			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: true
				}
			},
			series: [{
				name: 'Maengron - Rain Hourly',
				data: rainDiffHour,
				color: 'blue',
				shadow: true
			},{
				name: 'Maengron - Rain 30 Min',
				data: rainDiff30Min,
				color: 'green',
				shadow: true
			}]
		};
	}

	drawTubidity(stream) {
		const label = [];
		const dataNTU = [];
		const forecastData = [];
		const testData = [];

		const countData = Object.keys(stream.data).length;
		Object.keys(stream.data).forEach((key, index) => {
			label.push(stream.data[key].timestamp);
			dataNTU.push(stream.data[key].Turbidity);
			if ((countData -1) > index) {
				forecastData.push(null);
			} else {
				forecastData.push(stream.data[key].Turbidity);
			}

			testData.push([stream.data[key].timestamp, stream.data[key].Turbidity]);
		});

		try {
			const t = new timeseries.main(testData);
			var coeffs = t.ARMaxEntropy({
			    data:	t.data.slice(0,10)
			});
			
			let forecast	= 0;	// Init the value at 0.
			for (var i=0;i<coeffs.length;i++) {	// Loop through the coefficients
			    forecast -= t.data[10-i][1]*coeffs[i];
			}
	
			stream.forcastNode.forEach((dateTime, index) => {
				label.push(dateTime);
				dataNTU.push(null);
				forecastData.push((forecast + coeffs[index]));
			});
		} catch(err) {
			console.log('ARMA:', err);
		}

		this.optionsTubidity = {
			chart: {
				type: 'line'
			},
			title: {
				text: 'Realtime Tubidity'
			},
			xAxis: {
				categories: label
			},
			yAxis: {
				title: {
					text: 'NTU'
				}
			},
			legend: {
				enabled: true
			},
			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: true
				}
			},
			series: [{
				name: 'Real Tubidity',
				data: dataNTU,
				color: '#ff0000',
				shadow: true
			},{
				name: 'Forcast Tubidity',
				data: forecastData,
				color: 'blue',
				shadow: true
			}]
		};
	}

	drawHumid(stream) {
		const humid = [];
		const label = [];

		Object.keys(stream.data).forEach((key) => {
			label.push(stream.data[key].timestamp);
			humid.push(stream.data[key].Humidity);
		});

		this.optionsHumidity = {
			chart: {
				type: 'line'
			},
			title: {
				text: 'Realtime Humidity'
			},
			xAxis: {
				categories: label
			},
			yAxis: {
				title: {
					text: 'g.'
				}
			},
			legend: {
				enabled: true
			},
			plotOptions: {
				line: {
					dataLabels: {
						enabled: true
					},
					enableMouseTracking: true
				}
			},
			series: [{
				name: 'Humidity',
				data: humid,
				color: 'green',
				shadow: true
			}]
		};
	}

	drawRadarMap() {
		console.log('Radar Map: Update');
		const strDataURI = "http://tiwrm.haii.or.th/TyphoonTracking/rainMaker/OKI/rm_OKI_lastest.gif";
		let ctx: CanvasRenderingContext2D = this.canvasRef.nativeElement.getContext('2d');
		let img = new Image;
		img.onload = function(){
			ctx.beginPath();
			ctx.drawImage(img, -900, 0, 1800, 1671);
			ctx.arc(313, 62, 30, 0, (2 * Math.PI));
			ctx.stroke();
			ctx.fillStyle = "rgba(255, 0, 0, 0.4)";
			ctx.fill();
		};
		img.src = strDataURI;
	}
}