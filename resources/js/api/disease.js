import {ApiClient} from '../client';

let client = new ApiClient();

export default {
    getDiseases: function () {
        return client.get('/diseases');
    },
}
