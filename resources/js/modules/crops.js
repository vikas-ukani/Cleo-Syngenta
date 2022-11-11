import CropAPI from '../api/crop';
import {EventBus} from "../event-bus";

export const crops = {
    state: {
        crops: {},
        cropsLoadStatus: 0,
        selectedCrop: null,
    },
    actions: {
        loadCrops({commit}) {
            commit('startAPIRequest');
            CropAPI.getCrops()
                .then(function (response) {
                    commit('setCrops', response.data);
                    commit('stopAPIRequest');
                })
                .catch(function () {
                    commit('stopAPIRequest');
                    EventBus.$emit('show-alert', {
                        dismiss: false,
                        message: 'There was an error requesting disease data from the API.',
                    }).bind(this);
                });
        },
    },
    mutations: {
        setCrops(state, crops) {
            state.crops = crops;
        },
        setSelectedCrop(state, key) {
            state.selectedCrop = key;
        },
    },
    getters: {
        getCrops(state) {
            return state.crops;
        },
        getCropsByCountry(state) {
            return state.crops;
        },
        getSelectedCrop(state) {
            return state.selectedCrop;
        }
    }
};
