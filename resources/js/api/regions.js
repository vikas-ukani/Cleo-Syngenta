import {ApiClient} from '../client';

let client = new ApiClient();

export default {
    getRegions: function () {
        return client.get('/regions');
    },
}
