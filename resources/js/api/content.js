import {ApiClient} from '../client';

let client = new ApiClient();

export default {

    getLatestUpdatedContentTimestamp: function() {
        return client.get('/content/last-updated');
    },

    getRelatedMedia: function (params) {
        return client.post('/content/media', {
            params: params,
        });
    },

    getContentByParams: function (params) {
        return client.post('/content', {
            params: params,
        });
    },

    getContentBySlug: function (slug) {
        return client.post('/content/', {
            slug: slug,
        });
    },
}
