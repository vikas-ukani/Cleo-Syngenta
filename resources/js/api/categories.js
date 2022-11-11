import {ApiClient} from '../client';

let client = new ApiClient();

export default {
    getCategories: function () {
        return client.get('/categories');
    },
}
