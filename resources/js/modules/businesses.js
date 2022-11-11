import BusinessAPI from '../api/business';
import { EventBus } from "../event-bus";

export const businesses = {
    state: {
        businesses: [],
        businessesLoadStatus: 0,
        selectedBusiness: 1,
        lastUpdated: null
    },
    actions: {
        loadBusinesses({ commit, state }) {
            commit('startAPIRequest');
            BusinessAPI.getBusinesses()
                .then(function (response) {
                    let newBusiness = [];
                    let dataObject = response.data;
                    /** VIKAS CODE START */
                    dataObject.data.map(business => {
                        if (typeof business.countries !== 'undefined' && business.countries.length > 0) {
                            newBusiness[business.id] = business;
                            // newBusiness[business.id] = business.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0));
                        }
                    })
                    /** VIKAS CODE STOP */
                    commit('setBusinesses', newBusiness);
                    // commit('setBusinesses', dataObject.data);
                    commit('updateLastUpdatedInApp');
                    commit('stopAPIRequest');
                    EventBus.$emit('show-map');
                })
                .catch(function () {
                    commit('stopAPIRequest');
                    EventBus.$emit('show-alert', {
                        dismiss: false,
                        message: 'There was an error requesting businesses data from the API.',
                    });
                });
        },
    },
    mutations: {
        setBusinesses(state, businesses) {
            state.businesses = businesses;
        },
        setSelectedBusiness(state, key) {
            state.selectedBusiness = key;
        }
    },
    getters: {
        getBusinesses(state) {
            return state.businesses;
        },
        getSelectedBusiness(state) {
            return state.selectedBusiness;
        },
    }
};
