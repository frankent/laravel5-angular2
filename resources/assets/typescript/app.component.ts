import { Component, OnInit } from "@angular/core";

@Component({
    selector: 'my-app',
    template: require('./app.component.html')
})
export class AppComponent implements OnInit {
    currentYear = 2000;
    ngOnInit():void {
        const currentDate = new Date();
        this.currentYear = currentDate.getFullYear();
    }

}