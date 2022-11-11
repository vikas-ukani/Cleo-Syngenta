import RegionsAPI from '../api/regions';
import { EventBus } from '../event-bus.js';

export const regions = {
    state: {
        regions: [],
        regionsLoadStatus: 0,
        allCountries: null,
        availableCountries: null,
        selectedRegion: null,
        selectedCountry: null,
    },
    actions: {
        loadRegions({ commit, getters }) {
            commit('startAPIRequest');
            RegionsAPI.getRegions()
                .then(function (response) {

                    let allCountries = [];
                    let countriesByRegion = [];
                    response.data.map(function (item) {
                        if (typeof item.countries !== 'undefined' && item.countries.length > 0) {
                            countriesByRegion[item.id] = item.countries.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0));
                            item.countries.map(function (item) {
                                allCountries.push(item);
                            });
                        }
                    });

                    commit('setRegions', response.data);
                    commit('setAllCountries', allCountries.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0)));
                    commit('setCountriesByRegion', countriesByRegion);
                    commit('stopAPIRequest');
                })
                .catch(function () {
                    commit('stopAPIRequest');
                    EventBus.$emit('show-alert', {
                        dismiss: false,
                        message: 'There was an error requesting region data from the API.',
                    });
                });
        },
    },
    mutations: {
        setSelectedRegion(state, key) {
            state.selectedRegion = key;
        },
        setRegions(state, regions) {
            state.regions = regions;
        },
        setSelectedCountry(state, key) {
            state.selectedCountry = key;
        },
        setAvailableCountries(state, availableCountries) {
            state.availableCountries = availableCountries;
        },
        setAllCountries(state, allCountries) {
            state.allCountries = allCountries;
        },
        setCountriesByRegion(state, countriesByRegion) {
            state.countriesByRegion = countriesByRegion;
        },
    },
    getters: {
        getSelectedRegion(state) {
            return state.selectedRegion;
        },
        getRegions(state) {
            return state.regions;
        },
        getSelectedCountry(state) {
            return state.selectedCountry;
        },
        getAllCountries(state) {
            return state.allCountries;
        },
        getAvailableCountries(state) {
            return state.availableCountries;
        },
        getCountriesByRegion(state) {
            return state.countriesByRegion;
        }
    }
};
