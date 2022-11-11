import ContentAPI from '../api/content';
import {EventBus} from "../event-bus";
import moment from "moment";

export const content = {
    state: {
        lastUpdatedFromApi: null,
        lastUpdatedInApp: null,
    },
    actions: {
        getLastContentUpdateTimestampFromAPI({commit}) {
            ContentAPI.getLatestUpdatedContentTimestamp()
                .then(function (response) {
                    commit('updateLastUpdatedFromApi', response.data.lastUpdated );
                })
                .catch(function () {
                    EventBus.$emit('show-alert', {
                        dismiss: false,
                        message: 'There was an error requesting content metadata.',
                    });
                });
        }
    },
    mutations: {
        updateLastUpdatedFromApi(state, timestamp) {
            state.lastUpdatedFromApi = timestamp;
        },
        updateLastUpdatedInApp(state) {
            state.lastUpdatedInApp = moment().utc(false).format('YYYY-MM-DD HH:mm:ss');
        }
    },
    getters: {
        getLastAPIContentUpdateTimestamp(state) {
            return state.lastUpdatedFromApi;
        },
        getLastAppContentUpdateTimestamp(state){
            return state.lastUpdatedInApp;
        }
    }
};
