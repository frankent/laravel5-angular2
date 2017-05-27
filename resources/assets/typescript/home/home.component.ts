import { Component, OnInit } from '@angular/core';
import { HomeService } from "../services/home.service";

@Component({
	styles: [`
      chart {
        display: block;
      }
    `],
	template: require('./home.component.html')
})
export class HomeComponent implements OnInit {
	options = {};
    constructor(private homeService: HomeService) {}
	ngOnInit() {
        this.getData();
	}
    getData() {
        const dd = this.homeService.getDataList();
        dd.then((response) => {
            console.log(response);

			const label = [];
			const dataNTU = [];
			Object.keys(response.data).forEach((key) => {
				label.push(response.data[key].timestamp);
				dataNTU.push(response.data[key].Turbidity);
			});

			this.options = {
				chart: {
					type: 'line',
					zoomType: 'x'
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
		            enabled: false
		        },
				plotOptions: {
					line: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: false
					}
				},
				series: [{
					name: 'Maengron',
					data: dataNTU,
					color: '#ff0000',
					shadow: true
				}]
			};
        });
    }
}