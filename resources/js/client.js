import axios from 'axios';
import * as Sentry from '@sentry/browser';
import {TYMIRIUM_CONFIG} from './config.js';

const getClient = () => {

    const options = {
        baseURL: TYMIRIUM_CONFIG.API_URL,
    };

    const client = axios.create(options);
    client.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    client.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector(
        'meta[name="csrf-token"]'
    ).content;

    // Add a request interceptor
    client.interceptors.request.use(
        (requestConfig) => {
            requestConfig.headers['Authorization'] = `Bearer ${TYMIRIUM_CONFIG.API_ACCESS_TOKEN}`;
            return requestConfig;
        },
        (requestError) => {
            // Sentry.captureException(requestError);
            return Promise.reject(requestError);
        },
    );

    // Add a response interceptor
    client.interceptors.response.use(
        response => response,
        (error) => {
            if (error.response.status >= 500) {
                // Sentry.captureException(error);
            }
            return Promise.reject(error);
        },
    );

    return client;
};

class ApiClient {
    constructor() {
        this.client = getClient();
    }

    async get(url, conf = {}) {
        return await getClient().get(url, conf)
            .then(response => response )
            .catch(error => error );
    }

    delete(url, conf = {}) {
        return this.client.delete(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    head(url, conf = {}) {
        return this.client.head(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    options(url, conf = {}) {
        return this.client.options(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    post(url, data = {}, conf = {}) {
        return this.client.post(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    put(url, data = {}, conf = {}) {
        return this.client.put(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    patch(url, data = {}, conf = {}) {
        return this.client.patch(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }
}

export {ApiClient};

/**
 * Base HTTP Client
 */
export default {
    // Provide request methods with the default base_url
    get(url, conf = {}) {
        return getClient().get(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    },

    delete(url, conf = {}) {
        return getClient().delete(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    },

    head(url, conf = {}) {
        return getClient().head(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    },

    options(url, conf = {}) {
        return getClient().options(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    },

    post(url, data = {}, conf = {}) {
        return getClient().post(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    },

    put(url, data = {}, conf = {}) {
        return getClient().put(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    },

    patch(url, data = {}, conf = {}) {
        return getClient().patch(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    },
};
