import {ApiClient} from '../client';

let client = new ApiClient();

export default {
    getCrops: function () {
        return client.get('/crops');
    },
}
