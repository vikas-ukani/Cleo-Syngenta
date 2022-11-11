<template>
    <div class="container-fluid py-4 d-flex flex-column flex-fill flex-wrap justify-content-between">
        <nav class="flex h-10">

            <button @click="handleSearchClick"
                    class="ml-4 p-1 px-3 rounded-lg"
                    :class="buttonClasses" id="search"
                    type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <svg class="icon h-4 w-4 fill-current"
                     :class="buttonIconClasses">
                    <use xlink:href="#icon-search"></use>
                </svg>
            </button>

            <business-select></business-select>
            <div class="dropdown-menus flex">
                <country-dropdown v-if="showNav"></country-dropdown>
                <crop-dropdown v-if="showNav"></crop-dropdown>
                <disease-dropdown v-if="showNav"></disease-dropdown>
            </div>


        </nav>
    </div>
</template>

<script>

    import BusinessSelect from "./FilterNavigationComponents/BusinessSelect";
    import RegionDropdown from "./FilterNavigationComponents/RegionDropdown";
    import CropDropdown from "./FilterNavigationComponents/CropDropdown";
    import DiseaseDropdown from "./FilterNavigationComponents/DiseaseDropdown";
    import CountryDropdown from "./FilterNavigationComponents/CountryDropdown";

    export default {
        components: {
            CountryDropdown,
            BusinessSelect,
            CropDropdown,
            DiseaseDropdown
        },
        computed: {
            active() {
                return this.$store.getters.getAPIRequestState === 0;
            },
            businesses() {
                return this.$store.getters.getBusinesses;
            },
            showNav() {
                if(this.businesses && this.businesses.length > 0){
                    return true;
                }
                return false;
            },
            crops() {
                return this.$store.getters.getCrops;
            },
            nematodes() {
                return this.$store.getters.getDiseases;
            },
            buttonClasses() {
                return {
                    'bg-brand text-white': this.searchActive && this.active,
                    'bg-button-light text-brand': !this.searchActive && this.active,
                    'bg-disabled-lighter text-disabled-darker': !this.active,
                    'hover:bg-brand hover:text-white': this.active
                }
            },
            buttonIconClasses() {
                return {
                    'text-white': this.searchActive && this.active,
                    'text-brand': !this.searchActive && this.active,
                    'text-disabled-darker': !this.active,
                    'hover:text-white': this.active
                }
            },
        },
        data: function () {
            return {
                searchActive: false,
            };
        },
        methods: {
            handleSearchClick: function (e) {
                if (this.active) {
                    this.searchActive = !this.searchActive
                }
            },
        },
        props: ['apiLoadStatus']
    }
</script>
