import RegionsAPI from "./api/regions";

require('es6-promise').polyfill();

import Vue from 'vue';
import Vuex from 'vuex';
import VuexPersist from 'vuex-persist';
import localforage from 'localforage';

import {businesses} from "./modules/businesses";
import {categories} from "./modules/categories";
import {diseases} from "./modules/diseases";
import {regions} from "./modules/regions";
import {crops} from "./modules/crops";
import {content} from "./modules/content";

// Simple API Request Module
// Used to check if we are doing any requests
// On the API to handle alerts and state
const api = {
    state: {
        requestState: 0,
    },
    actions: {
        resetAPIRequests({commit}) {
            commit('resetAPIRequests');
        }
    },
    mutations: {
        resetAPIRequests(state) {
            state.requestState = 0;
        },
        startAPIRequest(state) {
            state.requestState += 1;
        },
        stopAPIRequest(state) {
            if(state.requestState > 0){
                state.requestState -= 1;
            }else{
                state.requestState = 0;
            }
        }
    },
    getters: {
        getAPIRequestState(state) {
            return state.requestState;
        },
    }
};

Vue.use(Vuex);
Vue.config.devtools = true;

const vuexLocalStorage = new VuexPersist({
    key: 'tymirium',
    storage: localforage,
    asyncStorage: true,
});

export default new Vuex.Store({
    plugins: [vuexLocalStorage.plugin],
    modules: {
        api,
        businesses,
        categories,
        diseases,
        regions,
        crops,
        content
    }
});
