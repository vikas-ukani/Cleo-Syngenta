import CategoryAPI from '../api/categories';
import {EventBus} from "../event-bus";

export const categories = {
    state: {
        categories: [],
        categoriesLoadStatus: 0,
    },
    actions: {
        loadCategories({commit}) {
            commit('startAPIRequest');
            CategoryAPI.getCategories()
                .then(function (response) {
                    let dataObject = response.data;
                    commit('setCategories', dataObject.data);
                    commit('stopAPIRequest');
                })
                .catch(function () {
                    commit('stopAPIRequest');
                    EventBus.$emit('show-alert', {
                        dismiss: false,
                        message: 'There was an error requesting categories data from the API.',
                    });
                });
        },
    },
    mutations: {
        setCategories(state, categories) {
            state.categories = categories;
        },
    },
    getters: {
        getCategories(state) {
            return state.categories;
        },
    }
};
