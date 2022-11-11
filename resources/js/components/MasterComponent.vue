<template>
    <div ref="mainContainer" id="main-container" class="leading-normal tracking-normal h-screen bg-map-static z-10">
        <!-- SVG Definitions -->
        <s-v-g-definitions-component></s-v-g-definitions-component>
        <alert-component class="z-20" v-model="apiLoadStatus"></alert-component>
        <header-component class="z-10"></header-component>

        <transition name="slideUp" v-if="contentLoaded && !mapVisible">
            <page-view-component loaded-via-mapview="true" :content-params="pageViewParams" v-if="contentLoaded && !mapVisible"></page-view-component>
        </transition>

        <div class="relative" :style="mapContainerStyles">
            <background-map v-if="mapVisible && !contentLoaded" ref="mapView" class="z-0"></background-map>
            <div class="flex-fill flex z-0">
                <inner-map-navigation-component v-model="apiLoadStatus" class="z-20"></inner-map-navigation-component>
                <router-view class="z-30"></router-view>
            </div>
        </div>
    </div>
</template>

<script>

import {EventBus} from '../event-bus.js';
import BackgroundMap from "./BackgroundMap";
import HeaderComponent from "./HeaderComponent";
import AlertComponent from "./AlertComponent";
import InnerMapNavigationComponent from "./FilterNavigation";
import SVGDefinitionsComponent from "./SVGDefinitionsComponent";
import PageViewComponent from "./PageViewComponent";
import moment from 'moment';

export default {
    computed: {
        active() {
            return this.$store.getters.getAPIRequestState === 0;
        },
        lastContentUpdate() {
            return this.$store.getters.getLastUpdatedTimestamp;
        },
        apiLoadStatus() {
            if (this.$store.getters.getAPIRequestState > 0) {
                EventBus.$emit('show-alert', {
                    dismiss: false,
                    message: 'Updating region, crop and nematode data',
                });
            }
            if (this.$store.getters.getAPIRequestState <= 0) {
                EventBus.$emit('show-alert', {
                    dismiss: true,
                    message: 'All data points updated successfully'
                });
                return 0;
            }
            return this.$store.getters.getAPIRequestState;
        },
    },
    components: {
        BackgroundMap,
        HeaderComponent,
        AlertComponent,
        SVGDefinitionsComponent,
        InnerMapNavigationComponent,
        PageViewComponent
    },
    data() {
        return {
            contentLoaded: false,
            pageViewParams: null,
            mapContainerStyles: {},
            mapVisible: false
        }
    },
    methods: {
        setContainerHeight(headerHeight) {
            this.mapContainerStyles.height = (this.$refs.mainContainer.clientHeight - headerHeight) + 'px';
        }
    },
    created() {
        EventBus.$on('header-mounted', function (data) {
            this.setContainerHeight(data.headerHeight);
        }.bind(this));
    },
    async mounted() {
        // Due due diligence checks
        this.$store.dispatch('resetAPIRequests');
        this.$store.dispatch('getLastContentUpdateTimestampFromAPI');

        // Get all businesses
        let businesses = this.$store.getters.getBusinesses;

        // Setup timestamps
        let lastUpdatedInApp = this.$store.getters.getLastAppContentUpdateTimestamp;
        let lastUpdatedInAPI = this.$store.getters.getLastAPIContentUpdateTimestamp;
        let now = moment().utc(false);

        // Check if we should load content
        if (!lastUpdatedInApp || (businesses && businesses.length <= 0) || moment(lastUpdatedInAPI).diff(moment(lastUpdatedInApp), 'seconds') > 0 || now.diff(moment(lastUpdatedInApp), 'hours') >= 24) {
            this.$store.dispatch('loadBusinesses');
            this.$store.dispatch('loadRegions');
            this.$store.dispatch('loadCrops'); // To get all Crops
            this.$store.dispatch('loadDiseases'); // To get all Diseases
        }else{
            this.mapVisible = true;
            this.contentLoaded = false;
        }

        EventBus.$on('show-map', function () {
            this.mapVisible = true;
            this.contentLoaded = false;
        }.bind(this));

        let selectedBusinessId = this.$store.getters.getSelectedBusiness;

        let selectedBusiness = businesses.filter(b => {
            return b.id === selectedBusinessId
        })[0];

        if (this.$route.path === '/') {
            this.$router.push({name: 'business', params: {business: selectedBusiness.shortname}});
        }

        EventBus.$on('loading-content', function (data) {

            if(this.$route.path !== '/data/' + data.business + '/' + data.crop){
                this.$router.push({name: 'business-crop', params: {business: data.business, crop: data.crop } });
            }

            this.contentLoaded = true;
            this.mapVisible = false;
            this.pageViewParams = data;
        }.bind(this));

        EventBus.$on('replacing-content', function (data) {
            if(this.$route.path !== '/data/' + data.business + '/' + data.crop){
                this.$router.push({name: 'business-crop', params: {business: data.business, crop: data.crop } });
            }
            this.pageViewParams = data;
        }.bind(this));

        EventBus.$on('hide-content', function () {
            if(this.$route.path !== '/map/' + selectedBusiness.shortname){
                this.$router.push({name: 'business', params: {business: selectedBusiness.shortname}});
            }
            this.contentLoaded = false;
            this.mapVisible = true;
            this.pageViewParams = null;
        }.bind(this));

    },
    beforeDestroy() {
        EventBus.$off('header-mounted');
    }
}
</script>
