<template>
    <div class="ml-4">
        <div
            ref="innerNavigationDropdown"
            @click.stop="handleDropdownClick"
            class="rounded-lg p-2 py-50 w-64 text-sm font-bold text-white flex inline-flex items-center justify-between"
            :class="buttonClasses">
            <svg class="icon h-5 w-5 items-center fill-current"
                 :class="buttonIconClasses">
                <use xlink:href="#icon-crop"></use>
            </svg>
            <span>{{ selectedCropObject.name || 'Crop' }}</span>
            <a @click.prevent="resetFilter" id="close-crop" class="p-1 close-filter">
                <svg id="close-crop-svg" class="fill-current h-3 w-3"
                     :class="buttonIconClasses">
                    <use id="close-crop-svg-use" v-if="selectedCrop === null" xlink:href="#icon-arrow-down"></use>
                    <use v-if="selectedCrop !== null" xlink:href="#icon-close"></use>
                </svg>
            </a>
        </div>
        <div class="searchable-dropdown">
            <ul v-show="dropdownOpen"
                id="crop-dropdown"
                class="mt-2 items-center absolute group items-center rounded-lg bg-white w-64">
                <li class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden">
                    <input class="form-control rounded w-full p-1" type="text" name="crop-search" id="crop-search"
                           v-model="cropSearch" placeholder="Search Crop Name"/>
                </li>
                <!-- :key="crop.id" -->
                <li
                    class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden"
                    v-for="(crop, index) in searchableCrops"
                    :key="'searchableCrop_'  + index"

                    @click.stop="handleCropClick(crop.id)">
                    {{ crop.name }}
                </li>
                <li
                    class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden"
                    v-if="typeof crops === 'undefined' || crops.length === 0">
                    There are no crops for this region
                </li>
            </ul>
        </div>

    </div>
</template>

<style lang="scss">
    #crop-search {
        width: 100%;
        border: 1px solid #ccc;
    }

    #crop-dropdown {
        max-height: 90% !important;
        overflow: scroll !important;
    }
</style>

<script>

import _ from 'lodash'
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
                    'btn-padded bg-brand text-white': this.dropdownOpen && this.active || this.selectedCrop !== null && this.active && !this.dropdownOpen,
                    'btn-padded bg-button-light text-button-text': !this.dropdownOpen && this.active && this.selectedCrop === null,
                    'btn-padded bg-disabled-lighter text-disabled-darker': !this.active,
                }
            },
            buttonIconClasses() {
                return {
                    'text-white': this.dropdownOpen && this.active || this.selectedCrop !== null && this.active && !this.dropdownOpen,
                    'text-brand': !this.dropdownOpen && this.active && this.selectedCrop === null,
                    'text-disabled-darker': !this.active
                }
            },
            crops() {
                if(this.$store.getters.getBusinesses.length === 0) {
                    return {};
                }

                if (this.selectedCountry !== null) {
                    return this.getCropsByCountry
                }
                return this.getAllCrops;
            },
            searchableCrops() {
                if (this.cropSearch) {
                    let filteredResults = this.crops.filter(function (crop) {
                        return crop.name.toLowerCase().includes(this.cropSearch.toLowerCase())
                    }, this);
                    if (filteredResults.length > 0) {
                        return filteredResults.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0));
                    } else {
                        return [{ id: null, name: 'No matches' }];
                    }
                } else {
                    if(this.crops.length > 0){
                        /** makes crops unique by crop.name */
                        var crops = [] ;
                        this.crops.forEach(crop => {
                            if (crops.filter(cr => crop.name == cr.name).length == 0) {
                                crops.push(crop)
                            }
                        });
                        return crops.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0));
                    }
                    return {};
                }
            },
            getCountriesForCrop() {
                return this.$store.getters.getBusinesses[this.selectedBusiness].crops[this.selectedCrop];
            },
            getCropsByCountry() {
                return this.$store.getters.getBusinesses[this.selectedBusiness - 1].countries.filter(function(c){ return c.id === this.selectedCountry}, this)[0].crops;
            },
            getAllCrops() {
                var data = this.$store.getters.getBusinesses[this.selectedBusiness].crops;
                return _.uniqBy(data, 'name');
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
            selectedCropObject() {
                if (this.selectedCrop) {
                    return this.crops.filter(function (item) {
                        return item.id === this.selectedCrop;
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
                cropSearch: null,
                dropdownOpen: false,
                optionSelected: false,
            }
        },
        methods: {
            resetFilter(event) {

                if (event) {
                    event.preventDefault();
                }

                if(!this.selectedCrop){
                    return false;
                }

                this.$store.commit('setSelectedCrop', null);
                EventBus.$emit('crop-selected', null);
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
                if (e.target.id !== '' && this.selectedCrop !== null) {
                    this.$store.commit('setSelectedCrop', null);
                    EventBus.$emit('crop-selected', null);
                    return false;
                }

                if (this.active) {
                    this.dropdownOpen = !this.dropdownOpen
                }
            },
            handleCropClick: function (key) {

                this.$store.commit('setSelectedCrop', key);
                this.dropdownOpen = false;

                EventBus.$emit('crop-selected', {
                    cropName: this.selectedCropObject.name,
                    cropId: this.selectedCrop,
                    cropData: this.selectedCropObject,
                    cropCountries: this.getCountriesForCrop
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
