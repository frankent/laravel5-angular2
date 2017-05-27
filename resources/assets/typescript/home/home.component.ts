import { Component, OnInit } from '@angular/core';
import { HomeService } from "../services/home.service";

@Component({
	template: require('./home.component.html')
})
export class HomeComponent implements OnInit {
    constructor(private homeService: HomeService) {}
	ngOnInit() {
		//Called after the constructor, initializing input properties, and the first call to ngOnChanges.
		//Add 'implements OnInit' to the class.
        this.getData();
	}
    getData() {
        const dd = this.homeService.getDataList();
        dd.then((data) => {
            console.log(data);
        });
    }
}