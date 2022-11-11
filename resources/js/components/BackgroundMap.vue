<template>
    <div
        class="z-0 absolute bottom-0 left-0 right-0 top-0"
        id="map-container"
        :class="{ 'country-visible': currentlySelectedCountry }"
    >
        <div class="z-0 h-full w-full fill-current text-gray-100">
            <div style="height: 100vh; background-color: #dcedf5"
                ref="world-map"
                id="world-map"
                class="h-full w-full fill-current"
            >
                <checkbox-svg-map
                    id="svgmap"
                    :map="World"
                    v-model="selectedLocations"
                    @click="handleLocationSelected"
                    @keydown.prevent.space="handleLocationSelected"
                    @mouseover.native="handleCountryHover"
                    @mouseleave.native="handleCountryHover"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from "../event-bus.js";
import { __uniqueByNameKey } from '../Utilities/DataFilter'
import * as am4core from "@amcharts/amcharts4/core";
import * as am4maps from "@amcharts/amcharts4/maps";
import am4geodata_worldLow from "@amcharts/amcharts4-geodata/worldLow";
import Vue from "vue";
import VueRouter from "vue-router";
import RegionMarker from "./maps/RegionMarker";
import CountryMarker from "./maps/CountryMarker";
import routes from "../routes";
import { SvgMap, CheckboxSvgMap } from "vue-svg-map";
import World from "@svg-maps/world";
// import * as d3 from "d3";
// import worldMap from "../datasets/world.json";

export default {
    name: "BackgroundMap",
    components: {
        SvgMap,
        CheckboxSvgMap
    },
    computed: {
        selectedLocations: {
            get: function() {
                return this.getAllowedCountryCodes.map(function(code) {
                    return code.toLowerCase();
                }, this);
            },
            set: function(selected) {
                return this.getAllowedCountryCodes.map(function(code) {
                    return code.toLowerCase();
                }, this);
            }
        },
        countries() {
            return this.getAllCountries;
        },
        availableCountries() {
            return this.getAllCountries;
        },
        getAllCountries() {
            if (!this.$store.getters.getBusinesses || (this.$store.getters.getBusinesses && this.$store.getters.getBusinesses.length === 0)) {
                return {};
            }

            if (this.selectedRegion !== null) {
                return this.$store.getters.getCountriesByRegion[
                    this.selectedRegion
                ];
            }

            if (this.$store.getters.getSelectedBusiness) {
                return this.$store.getters.getBusinesses[
                    this.$store.getters.getSelectedBusiness
                ].countries;
            }

            // Failsafe
            return {};
        },
        getAllowedCountryCodes() {
            let allowedCountries = [];
            this.availableCountries.map(function(item) {
                allowedCountries.push(item.iso_code);
            });

            if (this.selectedCrop !== null && this.getCountriesForCrop) {
                allowedCountries = allowedCountries.filter(function(code) {
                    return this.getCountriesForCrop.includes(code);
                }, this);
            }

            return allowedCountries;
        },
        getCountriesForCrop() {
// return false;
        console.log('data-data-data');
            return this.$store.getters.getCrops.business[this.selectedBusiness]
                .crop[this.selectedCrop];
        },
        selectedCountry() {
            return this.$store.getters.getSelectedCountry;
        },
        selectedCountryCode() {
            if (this.selectedCountry !== null) {
                let selectedCountry = this.selectedCountry;
                return this.countries.filter(function(item) {
                    return item.id === selectedCountry;
                })[0].iso_code;
            }
            return false;
        },
        selectedCountryName() {
            if (this.selectedCountry !== null) {
                let selectedCountry = this.selectedCountry;
                return this.countries.filter(function(item) {
                    return item.id === selectedCountry;
                })[0].name;
            }
            return false;
        },
        countriesByRegion() {
            return this.$store.getters.getCountriesByRegion;
        },
        regions() {
            return this.$store.getters.getRegions;
        },
        selectedRegion() {
            return this.$store.getters.getSelectedRegion;
        },
        selectedBusiness() {
            return this.$store.getters.getSelectedBusiness;
        },
        selectedCrop() {
            return this.$store.getters.getSelectedCrop;
        },
        selectedDisease() {
            return this.$store.getters.getSelectedDisease;
        }
    },
    data() {
        return {
            World,
            currentlySelectedCountry: null,
            north: null,
            east: null,
            south: null,
            west: null,
            imageSeries: null,
            map: null,
            worldSeries: null,
            worldPolygon: null,
            availableCountriesSeries: null,
            hoveredCountry: null,
            selectedCountryOnMap: null,
            regionMarkers: [],
            mapMarkers: [],
            mapColours: {
                base: "rgba(185,216,181,1)",
                available: "rgba(255,141,0,0.5)",
                selected: "rgba(235,130,0,1)",
                hover: "rgba(235,130,0,1)"
            }
        };
    },
    methods: {
        handleLocationSelected(event) {
            let countryCode = event.target.id.toUpperCase();
            if (this.getAllowedCountryCodes.includes(countryCode)) {
                this.selectCountry(countryCode, event.target);
            }
        },

        showHoverEffectOnCountry(code, countryObject) {
            this.hoveredCountry = countryObject;
            this.hoveredCountry.classList.add("active");
        },

        handleCountryHover(event) {
            // Handle being outside of the country regions
            if (event.target.id === "svgmap") {
                this.resetMap();
                return false;
            }

            // We haven't yet hovered on a country
            if (event.target.id && !this.hoveredCountry) {
                if (
                    this.getAllowedCountryCodes.includes(
                        event.target.id.toUpperCase()
                    )
                ) {
                    this.showHoverEffectOnCountry(
                        event.target.id,
                        event.target
                    );
                }
            }

            // We have already hovered on a country
            if (event.target.id && this.hoveredCountry) {
                this.resetMap();
                if (
                    this.getAllowedCountryCodes.includes(
                        event.target.id.toUpperCase()
                    )
                ) {
                    this.showHoverEffectOnCountry(
                        event.target.id,
                        event.target
                    );
                }
            }
        },

        initCountryMapMarker() {
            let boundingRect = this.selectedCountryOnMap.getBoundingClientRect();
            let boundingBox = this.selectedCountryOnMap.getBBox();

            let selectedCountry = this.$store.getters.getBusinesses[
                this.selectedBusiness
            ].countries.filter(function(c) {
                return c.id === this.selectedCountry;
            }, this);

            let selectedCrop = this.$store.getters.getBusinesses[
                this.selectedBusiness
            ].crops.filter(function(c) {
                return c.id === this.selectedCrop;
            }, this);

            let center = {
                x: boundingRect.left,
                y: boundingRect.y
            };

            if (selectedCountry[0].iso_code === "ZA") {
                center = {
                    x: boundingRect.left - 45,
                    y: boundingRect.y - 100
                };
            }

            if (selectedCountry[0].iso_code === "NZ") {
                center = {
                    x: boundingRect.left - 25,
                    y: boundingRect.y - 20
                };
            }

            if (selectedCountry[0].iso_code === "CA") {
                center = {
                    x: boundingRect.left + 75,
                    y: boundingRect.y + 200
                };
            }

            if (selectedCountry[0].iso_code === "US") {
                center = {
                    x: boundingRect.left + 175,
                    y: boundingRect.y + 200
                };
            }

            if (selectedCountry[0].iso_code === "RU") {
                center = {
                    x: boundingRect.left + 230,
                    y: boundingRect.y + 170
                };
            }

            this.mapMarkers = [
                {
                    title: this.selectedCountryName,
                    code: selectedCountry[0].iso_code,
                    image: null,
                    top: center.y,
                    left: center.x,
                    countryId: this.selectedCountry,
                    crops: selectedCountry[0].crops,
                    nematodes: selectedCountry[0].nematodes
                }
            ];
        },

        createCountryMapMarker(item) {
            let countryId = parseInt(item.countryId);
            let newCrops = [];
            item.crops.forEach(crop => {
                if (newCrops.filter(crp => crp && crp.name == crop.name).length == 0) {
                    newCrops.push(crop)
                }
            });
            item.crops = newCrops
            item.crops.map(
                function(crop) {
                    crop.use_rate = null;
                    if (
                        crop.technical_data !== null &&
                        crop.technical_data.length > 0
                    ) {
                        crop.use_rate = crop.technical_data[0].attributes.cgap;
                    }
                }.bind(this)
            );

            let ComponentClass = Vue.extend(CountryMarker);
            Vue.use(VueRouter);
            let router = new VueRouter(routes);
            let instance = new ComponentClass({
                router,
                propsData: {
                    businessId: this.$store.getters.getSelectedBusiness, //
                    countryId: "za",
                    abbr: item.code,
                    region: item.title,
                    countries: [],
                    crops: item.crops,
                    nematodes: item.nematodes
                }
            });
            instance.$mount();
            this.$refs["world-map"].appendChild(instance.$el);
            return instance.$el;
        },

        updateCountryMapMarkers() {
            this.mapMarkers.map(function(item) {
                let rm = this.createCountryMapMarker(item);
                rm.style.position = "absolute";
                rm.style.top = parseInt(item.top) + "px";
                rm.style.left = parseInt(item.left) + "px";
            }, this);
        },

        addRegionMapMarkers() {
            this.regionMarkers = [];

            this.regions.map(function(item) {
                let __new_countries = [];
                let _countries = this.countriesByRegion[item.id].filter(
                    function(country) {
                        return this.getAllowedCountryCodes.includes(
                            country.iso_code
                        );
                    },
                    this
                );
                _countries = __uniqueByNameKey(_countries);
                _countries.map(function(country) {
                    // let _currentBusiness = this.$store.getters.getBusinesses.filter((business) => {
                    //             return business.id == this.selectedBusiness
                    //         })[0]
                    // let _country = _currentBusiness.countries.filter(function(c) {
                    //     return c.id === country.id;
                    // }, country);
                    // if(_country.length > 0){
                    //     country = _country[0]
                    // }
                    country.nematodes = null;
                    country.crops = null;
                    let _country = this.$store.getters.getBusinesses[
                        this.selectedBusiness
                    ].countries.filter(function(c) {
                        return c.id === country.id;
                    }, country);
                    if (_country.length > 0) {
                        country.nematodes = _country[0].nematodes;
                        country.crops = _country[0].crops;
                    }
                }, this);
                if (this.selectedCrop !== null) {
                    _countries = this.countriesByRegion[item.id].filter(
                        function(country) {
                            return this.getCountriesForCrop && this.getCountriesForCrop.includes(
                                country.iso_code
                            );
                        },
                        this
                    );
                    _countries = __uniqueByNameKey(_countries);

                    let _selectedCrop = this.$store.getters.getCrops.business[
                        this.selectedBusiness
                    ].all.filter(function(crop) {
                        return crop.id === this.selectedCrop;
                    }, this);
                    // this.$store.getters.getBusinesses.filter((business) => {
                    //             return business.id == this.selectedBusiness
                    //         })[0]
                    _countries.map(function(country) {
                        country.nematodes = this.$store.getters.getDiseases.business[
                            this.selectedBusiness
                        ].country[country.id];
                        country.crops = _selectedCrop;
                    }, this);
                }

                this.regionMarkers.push({
                    title: item.name,
                    latitude: item.latitude,
                    longitude: item.longitude,
                    code: item.code,
                    regionId: item.id,
                    countries: _countries
                });
                console.log('all this.regionMarkers', this.regionMarkers);
            }, this);
        },

        removeMapMarkers() {
            const removeElements = elms => elms.forEach(el => el.remove());
            removeElements(document.querySelectorAll(".map-marker-container"));
            removeElements(document.querySelectorAll(".map-marker"));
        },

        createRegionMapMarker(item) {
            if (item.countries && item.countries.length && item.countries.length > 0) {
                let ComponentClass = Vue.extend(RegionMarker);
                Vue.use(VueRouter);
                let router = new VueRouter(routes);

                let instance = new ComponentClass({
                    router,
                    propsData: {
                        businessId: this.$store.getters.getBusinesses[
                            this.$store.getters.getSelectedBusiness - 1
                        ].shortname,
                        regionId: item.regionId,
                        abbr: item.code,
                        region: item.title,
                        countries: item.countries
                    }
                });
                instance.$mount();
                this.$refs["world-map"].appendChild(instance.$el);
                return instance.$el;
            } else {
                return 'error';
            }
        },

        updateRegionMapMarkers() {
            let showingErrorFlag = true;
            this.regionMarkers.map(function(item) {
                let rm = this.createRegionMapMarker(item);
                if (rm != 'error') {
                    showingErrorFlag = false;
                    let xy = { x: 0, y: 0 };

                    if (item.code === "AME") {
                        xy = { x: 50, y: 60 };
                    }

                    if (item.code === "LAN") {
                        xy = { x: 30, y: 60 };
                    }

                    if (item.code === "LAS") {
                        xy = { x: 30, y: 80 };
                    }

                    if (item.code === "NA") {
                        xy = { x: 22, y: 45 };
                    }

                    if (item.code === "APAC") {
                        xy = { x: 80, y: 50 };
                    }

                    if (item.code === "ZN") {
                        xy = { x: 66, y: 52 };
                    }

                    if (item.code === "ANZ") {
                        xy = { x: 76, y: 79 };
                    }

                    if (item.code === "CIS") {
                        xy = { x: 68, y: 30 };
                    }

                    rm.style.position = "absolute";
                    rm.style.top = xy.y + "%";
                    rm.style.left = xy.x + "%";
                    rm.classList.add("map-marker-container");
                }
            }, this);
            if (showingErrorFlag && showingErrorFlag == true) {
                EventBus.$emit('show-alert', {
                    dismiss: true,
                    message: 'No details avilable!',
                });
            }
        },

        removeMapClass(classname) {
            let worldMap = document.getElementsByClassName("svg-map__location");
            for (let i = 0; i < worldMap.length; i++) {
                worldMap[i].classList.remove(classname);
            }
        },

        resetMap() {
            this.removeMapClass("active");
            this.hoveredCountry = null;
            this.selectedCountryOnMap = null;
        },

        deselectCountryHighlight() {
            this.removeMapClass("selected");
            this.selectedCountryOnMap = null;
        },

        resetRegionMapMarkers() {
            this.addRegionMapMarkers();
            this.updateRegionMapMarkers();
        },

        zoomMapToRegion: function(polygon, callback) {
            if (typeof callback === "function") {
                callback();
            }
        },

        selectCountry: function(code, countryObject) {
            // Remove all map markers
            this.removeMapMarkers();

            // Double ensure we can't click on unavailable countries
            if (!this.getAllowedCountryCodes.includes(code)) {
                return false;
            }

            if (this.currentlySelectedCountry === null) {
                let countryKey = this.availableCountries.filter(function(item) {
                    return item.iso_code === code;
                })[0].id;

                this.$store.commit("setSelectedCountry", countryKey);
                this.currentlySelectedCountry = code;

                if (this.getAllowedCountryCodes.includes(code.toUpperCase())) {
                    this.selectedCountryOnMap = countryObject;
                    this.selectedCountryOnMap.classList.add("selected");
                    // this.selectedCountryOnMap
                }

                // Add Marker for Selected Country
                this.initCountryMapMarker();
                this.updateCountryMapMarkers();

                return true;
            } else {
                // Deselecting an already selected country
                if (this.currentlySelectedCountry === code) {
                    // Reset the current selected country
                    this.currentlySelectedCountry = null;
                    this.$store.commit("setSelectedCountry", null);
                    this.selectedCountryOnMap = null;
                    this.resetMap();
                    this.removeMapMarkers();
                    this.deselectCountryHighlight();
                    this.resetRegionMapMarkers;
                } else {
                    // We are setting a new country and one was already selected.
                    this.deselectCountryHighlight();
                    this.removeMapMarkers();
                    this.currentlySelectedCountry = null;
                    this.selectCountry(code, countryObject);
                }

                return true;
            }
        }
    },
    created() {
        // Handle when a business is selected from the menu
        EventBus.$on(
            "business-selected",
            function(data) {
                // console.log('log data' , data)
                this.resetMap();
                this.removeMapMarkers()
                this.$store.commit("setSelectedCountry", null);
                this.resetRegionMapMarkers();
            }.bind(this)
        );

        // Show hovered country on map from filter
        EventBus.$on(
            "country-highlighted",
            function(data) {
                if (data !== null) {
                    let countryCode = data.countryCode.toLowerCase();
                    let countryObject = document.getElementById(countryCode);
                    this.showHoverEffectOnCountry(countryCode, countryObject);
                } else {
                    this.resetMap();
                }
            }.bind(this)
        );

        // Handle countries selected from the filters
        EventBus.$on(
            "country-selected",
            function(data) {
                this.removeMapMarkers();

                if (data !== null) {
                    let countryObject = document.getElementById(
                        data.countryCode.toLowerCase()
                    );
                    this.selectCountry(data.countryCode, countryObject);
                } else {
                    // We are deselecting a country
                    if (this.currentlySelectedCountry) {
                        this.currentlySelectedCountry = null;
                    }
                    this.$store.commit("setSelectedCountry", null);
                    this.deselectCountryHighlight();
                    this.resetRegionMapMarkers();
                }
            }.bind(this)
        );
    },
    mounted() {
        if (this.selectedCountryCode) {
            this.currentlySelectedCountry = this.selectedCountryCode;
            EventBus.$emit(
                "country-selected",
                {
                    countryName: this.selectedCountryName,
                    countryCode: this.selectedCountryCode
                },
                this
            );
        } else {
            // Make sure we mimic button selected
            EventBus.$emit("business-selected", {
                businessId: this.$store.getters.getSelectedBusiness
            });
        }

        // Handle regions selected from the filters
        EventBus.$on(
            "crop-selected",
            function(data) {
                if (data !== null) {
                } else {
                    this.$store.commit("setSelectedCrop", null);
                }
                this.removeMapMarkers();
                this.addRegionMapMarkers();
                this.updateRegionMapMarkers();
                // this.highlightAvailableCountries();
            }.bind(this)
        );

        EventBus.$on(
            "region-hover-country-clicked",
            function(data) {
                EventBus.$emit("country-selected", {
                    countryCode: data.code,
                    countryName: data.name
                });
            }.bind(this)
        );

        EventBus.$on(
            "region-marker-hovered",
            function(data) {
                if (data !== null) {
                    let countriesByRegion = this.countriesByRegion[
                        data.regionID
                    ].filter(function(country) {
                        return this.getAllowedCountryCodes.includes(
                            country.iso_code
                        );
                    }, this);

                    const highlightRegions = hoverState => {
                        let fillColour = hoverState
                            ? this.mapColours.selected
                            : this.mapColours.available;
                        countriesByRegion.map(function(country) {
                            this.worldSeries.getPolygonById(
                                country.iso_code
                            ).isHover = hoverState;
                            this.worldSeries.getPolygonById(
                                country.iso_code
                            ).fill = am4core.color(fillColour);
                        }, this);
                    };
                    highlightRegions(data.hoverState);
                }
            }.bind(this)
        );

        /*
         * amCharts Event Handlers
         * This covers all events for amCharts required to get the functionality for the maps
         */

        // // Handle zoom on page load for selected country and highlighted countries
        // this.map.events.on('ready', function (ev) {
        //     if (this.selectedCountryCode) {
        //         this.currentlySelectedCountry = this.worldSeries.getPolygonById(this.selectedCountryCode);
        //         this.currentlySelectedCountry.isActive = true;
        //         EventBus.$emit('country-selected', {
        //             countryName: this.selectedCountryName,
        //             countryCode: this.selectedCountryCode
        //         }, this);
        //     } else {
        //         // Make sure we mimic button selected
        //         EventBus.$emit('business-selected', {
        //             businessId: this.$store.getters.getSelectedBusiness
        //         });
        //     }
        // }.bind(this));

        // // On hover on the map, show over only on allowed countries
        // this.worldPolygon.events.on('over', function (ev) {

        //     let code = ev.target.dataItem.dataContext.id;
        //     ev.target.tooltipText = '';
        //     ev.target.isHover = false;

        //     // Ensure we don't allow countries to be selected
        //     if (!this.getAllowedCountryCodes.includes(code)) {
        //         ev.target.togglable = false;
        //     } else {
        //         ev.target.fill = am4core.color(this.mapColours.hover);
        //         ev.target.isHover = true;
        //         EventBus.$emit('country-hover', {
        //             countryCode: code,
        //             active: true
        //         });
        //     }
        // }.bind(this));

        // // Ensure we reset the map back to the correct colours for available/selected regions
        // this.worldPolygon.events.on('out', function (ev) {
        //     let code = ev.target.dataItem.dataContext.id;
        //     ev.target.isHover = false;
        //     EventBus.$emit('country-hover', {
        //         countryCode: code,
        //         active: false
        //     });

        //     // Available Countries
        //     if (this.getAllowedCountryCodes.includes(code)) {
        //         ev.target.fill = am4core.color(this.mapColours.available);
        //         // If the country is "selected"
        //         if (this.selectedCountryCode === code) {
        //             ev.target.fill = am4core.color(this.mapColours.selected);
        //         }
        //     } else {
        //         ev.target.fill = am4core.color(this.mapColours.selected);
        //     }
        // }.bind(this));

        // // On clicking a country on the map
        // this.worldPolygon.events.on('hit', function (ev) {
        //     let code = ev.target.dataItem.dataContext.id;
        //     this.selectCountry(code, ev);
        // }.bind(this));
    },
    beforeDestroy() {
        EventBus.$off("country-selected");
        EventBus.$off("business-selected");
        EventBus.$off("map-region-marker-selected");
        EventBus.$off("region-marker-hovered");
        EventBus.$off("crop-selected");
        EventBus.$off("region-hover-country-clicked");
        EventBus.$off("country-marker-hovered");
        EventBus.$off("country-highlighted");
    }
};
</script>

<style lang="scss">
@import "../../sass/components/map";
</style>
