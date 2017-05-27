import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpModule } from '@angular/http';
import { FormsModule } from '@angular/forms';
import { routing, appRoutingProviders }       from './app.routing';
import { AppComponent } from './app.component';
import { FeatureListComponent } from './features/feature-list/feature-list.component';
import { FeatureComponent } from './features/feature/feature.component';
import { FeatureService } from './features/shared/feature.service';
import { HomeComponent } from './home/home.component';
import { HomeService } from './services/home.service';

@NgModule({
  imports: [
    HttpModule,
  	BrowserModule,
  	FormsModule,
  	routing
  ],
  declarations: [ 
  	AppComponent,
  	FeatureListComponent,
  	FeatureComponent,
    HomeComponent
  ],
  providers: [
  	appRoutingProviders,
    FeatureService,
    HomeService
  ],
  bootstrap: [
  	AppComponent
  ]
})
export class AppModule { }