import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/add/operator/toPromise';

@Injectable()
export class HomeService {
    private url = '/api/data';
    constructor(private http:Http) {}

    getDataList(): Promise<any> {
        return this.http.get(this.url)
               .toPromise()
               .then((response) => {
                    return response.json();
               })
               .catch(this.handleError);
    }

    private handleError(error: any): Promise<any> {
        console.error('An error occurred', error); // for demo purposes only
        return Promise.reject(error.message || error);
    }
}