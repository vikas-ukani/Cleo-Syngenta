import {ApiClient} from '../client';

let client = new ApiClient();

export default {
    async getBusinesses() {
        var data = await client.get('/businesses');
        return data
    },
}
