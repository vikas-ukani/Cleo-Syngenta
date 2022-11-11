<template>
    <div class="ml-4">
        <div
            ref="innerNavigationDropdown"
            @click.stop="handleDropdownClick"
            class="rounded-lg p-2 py-50 w-64 text-sm font-bold text-white flex inline-flex items-center justify-between"
            :class="buttonClasses">
            <svg class="icon h-5 w-5 items-center fill-current"
                 :class="buttonIconClasses">
                <use xlink:href="#icon-disease"></use>
            </svg>
            <span>{{ selectedDiseaseObject.name || 'Pests / Pathogens' }}</span>
            <a @click.prevent="resetFilter" id="close-disease" class="p-1 close-filter">
                <svg id="close-disease-svg" class="fill-current h-3 w-3"
                     :class="buttonIconClasses">
                    <use id="close-disease-svg-use" v-if="selectedDisease === null" xlink:href="#icon-arrow-down"></use>
                    <use v-if="selectedDisease !== null" xlink:href="#icon-close"></use>
                </svg>
            </a>
        </div>
        <ul v-show="dropdownOpen"
            class="mt-2 items-center relative group items-center rounded-lg bg-white">
            <li
                class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden"
                v-for="disease in diseases"
                :key="disease.id"
                @click.stop="handleDiseaseClick(disease.id)">
                {{ disease.name }}
            </li>
            <li
                class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden"
                v-if="typeof diseases === 'undefined' || diseases.length === 0">
                There are no diseases for this region
            </li>
        </ul>
    </div>
</template>

<script>

    import {EventBus} from '../../event-bus.js';

    export default {
        created() {
            document.addEventListener('click', this.handleBodyClick)
        },
        computed: {
            active() {
                return this.$store.getters.getAPIRequestState === 0;
            },
            buttonClasses() {
                return {
                    'btn-padded bg-brand text-white': this.dropdownOpen && this.active || this.selectedDisease !== null && this.active && !this.dropdownOpen,
                    'btn-padded bg-button-light text-button-text': !this.dropdownOpen && this.active && this.selectedDisease === null,
                    'btn-padded bg-disabled-lighter text-disabled-darker': !this.active,
                }
            },
            buttonIconClasses() {
                return {
                    'text-white': this.dropdownOpen && this.active || this.selectedDisease !== null && this.active && !this.dropdownOpen,
                    'text-brand': !this.dropdownOpen && this.active && this.selectedDisease === null,
                    'text-disabled-darker': !this.active
                }
            },
            diseases() {

                if (this.getDiseasesByCrop && this.selectedCrop !== null) {
                    return this.getDiseasesByCrop.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0))
                }

                if (this.selectedCountry !== null) {
                    return this.getDiseasesByCountry.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0))
                }
                return this.getAllDiseases;
            },
            getDiseasesByCrop() {
                if(this.getAllBusinesses.length <= 0){
                    return [];
                }
                return this.$store.getters.getDiseases.business[this.selectedBusiness].country[this.selectedCountry];
            },
            getDiseasesByCountry() {
                if(this.getAllBusinesses.length <= 0){
                    return [];
                }
                return this.$store.getters.getBusinesses[this.selectedBusiness - 1].countries.filter(function(c){ return c.id === this.selectedCountry}, this)[0].nematodes;
            },
            getAllDiseases() {
                if(this.getAllBusinesses.length <= 0){

                    return [];
                }
                return this.$store.getters.getBusinesses[this.selectedBusiness].nematodes;
            },
            getAllBusinesses() {
                return this.$store.getters.getBusinesses;
            },
            selectedBusiness() {
                return this.$store.getters.getSelectedBusiness;
            },
            selectedCountry() {
                return this.$store.getters.getSelectedCountry;
            },
            selectedCrop() {
                return this.$store.getters.getSelectedCrop;
            },
            selectedDisease() {
                return this.$store.getters.getSelectedDisease;
            },
            selectedDiseaseObject() {
                if (this.selectedDisease) {
                    return this.diseases.filter(function (item) {
                        return item.id === this.selectedDisease;
                    }, this)[0];
                }
                return {};
            }
        },
        destroyed() {
            document.removeEventListener('click', this.handleBodyClick)
        },
        data: function () {
            return {
                dropdownOpen: false,
                optionSelected: false,
            }
        },
        methods: {
            resetFilter() {

                if (event) {
                    event.preventDefault();
                }

                if(!this.selectedDisease){
                    return false;
                }

                this.$store.commit('setSelectedDisease', null);
                EventBus.$emit('disease-selected', null);
                this.$nextTick(() => {
                    if (this.active) {
                        this.dropdownOpen = !this.dropdownOpen
                    }
                });
                return false;
            },
            handleDropdownClick: function (e) {

                if(e){
                    e.preventDefault();
                }

                // Handle Close Button
                if (e.target.id !== '' && this.selectedDisease !== null) {
                    this.$store.commit('setSelectedDisease', null);
                    EventBus.$emit('disease-selected', null);
                    return false;
                }

                if (this.active) {
                    this.dropdownOpen = !this.dropdownOpen
                }
            },
            handleDiseaseClick: function (key) {
                this.$store.commit('setSelectedDisease', key);
                this.dropdownOpen = false;

                EventBus.$emit('disease-selected', {
                    diseaseName: this.selectedDiseaseObject.name,
                    diseaseId: this.selectedDisease,
                    diseaseData: this.selectedDiseaseObject
                });
            },
            handleBodyClick: function (e) {
                if (!this.$el.contains(e.target)) {
                    this.dropdownOpen = false;
                }
            }
        },
    }
</script>
