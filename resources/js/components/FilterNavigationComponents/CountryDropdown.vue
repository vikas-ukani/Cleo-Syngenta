<template>
    <div class="ml-4">
        <div
            ref="innerNavigationDropdown"
            @click.stop="handleDropdownClick"
            class="rounded-lg p-2 py-50 w-64 text-sm font-bold text-white flex inline-flex items-center justify-between"
            :class="buttonClasses">
            <svg class="icon h-5 w-5 items-center fill-current"
                 :class="buttonIconClasses">
                <use xlink:href="#icon-map-marker"></use>
            </svg>
            <span>{{ selectedCountryName || 'Country' }}</span>
            <a @click.prevent="resetFilter" id="close-country" class="p-1 close-filter">
                <svg id="close-country-svg" class="fill-current h-3 w-3"
                     :class="buttonIconClasses">
                    <use id="close-country-svg-use" v-if="selectedCountry === null" xlink:href="#icon-arrow-down"></use>
                    <use v-if="selectedCountry !== null" xlink:href="#icon-close"></use>
                </svg>
            </a>
        </div>
        <div class="searchable-dropdown">
            <ul v-show="dropdownOpen"
                class="mt-2 items-center absolute group items-center rounded-lg bg-white w-64">
                <li
                    class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden"
                    v-for="country in countries"
                    :key="country.id"
                    @mouseover="handleCountryHover(country.iso_code)"
                    @mouseout="disableAllHover"
                    @click.stop="handleCountryClick(country.id)">
                    {{ country.name }}
                </li>
                <li
                    class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden"
                    v-if="typeof countries === 'undefined' || countries.length === 0">
                    There are no countries for this region.
                </li>
            </ul>
        </div>
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
                    'btn-padded bg-brand text-white': this.dropdownOpen && this.active || this.selectedCountry !== null && this.active && !this.dropdownOpen,
                    'btn-padded bg-button-light text-button-text': !this.dropdownOpen && this.active && this.selectedCountry === null,
                    'btn-padded bg-disabled-lighter text-disabled-darker': !this.active,
                }
            },
            buttonIconClasses() {
                return {
                    'text-white': this.dropdownOpen && this.active || this.selectedCountry !== null && this.active && !this.dropdownOpen,
                    'text-brand': !this.dropdownOpen && this.active && this.selectedCountry === null,
                    'text-disabled-darker': !this.active
                }
            },
            countries() {
                if(this.$store.getters.getBusinesses.length === 0) {
                    return {};
                }

                if (this.selectedRegion !== null) {
                    return this.$store.getters.getCountriesByRegion[this.selectedRegion].sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0))
                }

                if(this.$store.getters.getSelectedBusiness){
                    return this.$store.getters.getBusinesses[this.$store.getters.getSelectedBusiness].countries.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0));
                }
                // Failsafe
                return {};
            },
            getAllCountries() {
                return this.$store.getters.getAllCountries;
            },
            selectedCountryCode() {
                if (this.selectedCountry !== null) {
                    let selectedCountryKey = this.selectedCountry;
                    let selectedCountry = this.countries.filter(function (item) {
                        return item.id === selectedCountryKey;
                    });
                    if (selectedCountry.length <= 0) {
                        return false;
                    }
                    return selectedCountry[0].iso_code;
                }
                return false;
            },
            selectedCountryName() {
                if (this.selectedCountry !== null) {
                    let selectedCountryKey = this.selectedCountry;
                    let selectedCountry = this.countries.filter(function (item) {
                        return item.id === selectedCountryKey;
                    });
                    if (selectedCountry.length <= 0) {
                        return false;
                    }
                    return selectedCountry[0].name;
                }
                return false;
            },
            selectedRegion() {
                return this.$store.getters.getSelectedRegion;
            },
            selectedCountry() {
                return this.$store.getters.getSelectedCountry;
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
            resetFilter(event) {

                if(event){
                    event.preventDefault();
                }

                if(!this.selectedCountry){
                    return false;
                }

                this.$store.commit('setSelectedCountry', null);
                EventBus.$emit('country-selected', null);
                return false;
            },
            disableAllHover() {
                EventBus.$emit('country-highlighted', null);
            },
            handleCountryHover(country_code) {
                EventBus.$emit('country-highlighted', {countryCode: country_code});
            },
            handleDropdownClick: function (e) {

                if(e){
                    e.preventDefault();
                }

                // Handle Close Button
                if (e.target.id !== '' && this.selectedCountry !== null) {
                    this.$store.commit('setSelectedCountry', null);
                    EventBus.$emit('country-selected', null);
                    return false;
                }

                if (this.active) {
                    this.dropdownOpen = !this.dropdownOpen
                }
            },
            handleCountryClick: function (key) {
                this.$store.commit('setSelectedCountry', key);
                this.dropdownOpen = false;

                EventBus.$emit('country-selected', {
                    countryCode: this.selectedCountryCode,
                    countryName: this.selectedCountryName,
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
