<template>
    <div class="ml-4">
        <a
            ref="innerNavigationDropdown"
            @click.stop="handleDropdownClick"
            class="rounded-lg p-2 py-50 w-64 font-bold text-sm flex inline-flex items-center justify-between"
            :class="buttonClasses"
            href="#">

            <svg class="icon h-5 w-5 items-center fill-current"
                 :class="buttonIconClasses">
                <use xlink:href="#icon-globe"></use>
            </svg>

            <span>{{ selectedRegionName || 'Region' }}</span>
            <span @click.prevent id="close-region" class="p-1">
                <svg @click.prevent id="close-region-svg" class="fill-current h-3 w-3"
                     :class="buttonIconClasses">
                    <use v-if="selectedRegion === null" xlink:href="#icon-arrow-down"></use>
                    <use v-if="selectedRegion !== null" xlink:href="#icon-close"></use>
                </svg>
            </span>
        </a>
        <ul v-show="dropdownOpen"
            class="mt-2 ml-4 items-center relative group items-center rounded-lg bg-white overflow-hidden">
            <li
                v-if="regions.length > 0"
                class="p-2 text-sm text-button-text hover:bg-gray-100 hover:text-button-text cursor-pointer w-full overflow-hidden"
                v-for="region in regions"
                :key="region.id"
                @click="handleRegionClick(region.id)">
                {{ region.name }}
            </li>
        </ul>
    </div>
</template>

<script>

    import {EventBus} from '../../event-bus.js';

    export default {
        name: "RegionDropdownComponent",
        data: function () {
            return {
                dropdownOpen: false,
                optionSelected: false,
            }
        },
        computed: {
            buttonClasses() {
                return {
                    'bg-brand text-white': this.dropdownOpen && this.active || this.selectedRegion !== null && this.active && !this.dropdownOpen,
                    'bg-button-light text-button-text': !this.dropdownOpen && this.active && this.selectedRegion === null,
                    'bg-disabled-lighter text-disabled-darker': !this.active,
                }
            },
            buttonIconClasses() {
                return {
                    'text-white': this.dropdownOpen && this.active || this.selectedRegion !== null && this.active && !this.dropdownOpen,
                    'text-brand': !this.dropdownOpen && this.active && this.selectedRegion === null,
                    'text-disabled-darker': !this.active
                }
            },
            active() {
                return this.$store.getters.getAPIRequestState === 0;
            },
            regions() {
                return this.$store.getters.getRegions;
            },
            selectedRegion() {
                return this.$store.getters.getSelectedRegion;
            },
            selectedRegionCode() {
                if (this.selectedRegion !== null) {
                    let selectedRegion = this.selectedRegion;
                    return this.regions.filter(function (item) {
                        return item.id === selectedRegion;
                    })[0].code;
                }
                return false;
            },
            selectedRegionName() {
                if (this.selectedRegion !== null) {
                    return this.regions.filter(function (item) {
                        return item.id === this.selectedRegion;
                    }, this)[0].name;
                }
                return false;
            }
        },
        methods: {
            handleDropdownClick: function (e) {

                // Handle Close Button
                if (e.target.id !== '' && this.selectedRegion !== null) {
                    this.$store.commit('setSelectedRegion', null);
                    this.$store.commit('setSelectedCountry', null);
                    EventBus.$emit('region-selected', null);
                    EventBus.$emit('country-selected', null);
                    return false;
                }

                if (this.active) {
                    this.dropdownOpen = !this.dropdownOpen
                }
            },
            handleRegionClick: function (key) {

                this.$store.commit('setSelectedRegion', key);
                this.$store.commit('setSelectedCountry', null);
                this.dropdownOpen = false;

                EventBus.$emit('region-selected', {
                    regionID: key,
                    regionCode: this.selectedRegionCode,
                    regionName: this.selectedRegionName,
                });

            },
            handleBodyClick: function (e) {
                if (!this.$el.contains(e.target)) {
                    this.dropdownOpen = false;
                }
            },
        },
        created() {
            document.addEventListener('click', this.handleBodyClick)
        },
        beforeDestroy() {
            document.removeEventListener('click', this.handleBodyClick)
        }
    }
</script>

<style scoped>

</style>
