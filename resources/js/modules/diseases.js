import DiseaseAPI from '../api/disease';
import {EventBus} from "../event-bus";

export const diseases = {
    state: {
        diseases: [],
        diseasesLoadStatus: 0,
        selectedDisease: null
    },
    actions: {
        loadDiseases({commit}) {
            commit('startAPIRequest');
            DiseaseAPI.getDiseases()
                .then(function (response) {
                    commit('setDiseases', response.data);
                    commit('stopAPIRequest');
                })
                .catch(function () {
                    commit('stopAPIRequest');
                    EventBus.$emit('show-alert', {
                        dismiss: false,
                        message: 'There was an error requesting disease data from the API.',
                    });
                });
        },
    },
    mutations: {
        setDiseases(state, diseases) {
            state.diseases = diseases;
        },
        setSelectedDisease(state, key) {
            state.selectedDisease = key;
        },
    },
    getters: {
        getDiseases(state) {
            return state.diseases;
        },
        getSelectedDisease(state) {
            return state.selectedDisease;
        },
    }
};
