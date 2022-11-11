<template>
    <!-- trigger="clickToOpen" -->
    <popper
        trigger="hover"
        boundaries-selector="#world-map"
        :delay-on-mouse-out="hoverTimeoutDelay"
        :options="{
            placement: 'left',
            modifiers: {
                offset: {
                    offset: '0,10px'
                }
            }
        }"
    >
        <div class="popper">
            <!--  v-if="crops.length > 5" -->
            <div
                class="country-hover wider-country-list z-50 flex">
                <!-- :key="'cropslim_' + index"
                    v-for="(repeat, index) in cropsLimit" -->
                <table

                    class="table-auto pb-4 text-sm rounded overflow-hidden">
                    <tr class="bg-gray-500">
                        <th class="text-sm font-semibold uppercase py-1">
                            Crop
                        </th>
                        <th class="text-sm font-light uppercase align-left">
                            Disease
                        </th>
                        <th class="text-sm font-light uppercase align-left">
                            Use Rate
                        </th>
                    </tr>
                    <!-- v-for="(crop, index) in crops.slice(0, 4)" -->

                    <!-- :key="'crop_' + index"
                        v-for="(crop, index) in limitedCrops[repeat - 1]" -->
                    <tr
                        :key="'crop_' + index"
                        v-for="(crop, index) in crops"
                    >
                        <td class="font-semibold" width="160px">
                            <div class="flex py-2">
                                <div
                                    class="w-8 h-8 mx-2 border border-gray-500 rounded-full overflow-hidden"
                                >
                                    <img :src="'/storage/' + crop.image" />
                                </div>
                                <div class="flex items-center align-center">
                                    <router-link
                                        @click.native="loadContent(crop)"
                                        :to="linkToCrop(crop)"
                                        >{{ crop.name }}
                                        </router-link
                                    >
                                </div>
                            </div>
                        </td>
                        <td
                            class="px-3 py-1 align-center vertical-center text-left"
                            width="120px"
                        >
                            <p>
                                <span
                                    v-for="(nematode, index) in nematodes"
                                    :key="'index_nematode_' + index"
                                    >{{
                                        index !== nematodes.length - 1
                                            ? nematode.name + ", "
                                            : nematode.name + ""
                                    }}</span
                                >
                            </p>
                        </td>
                        <td
                            class="px-3 py-1 align-center vertical-center text-left"
                        >
                            <div v-html="crop.use_rate"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div
            class="map-marker absolute z-40"
            ref="marker"
            slot="reference"
            :class="{ hovered: hover }"
            @mouseover="handleHover(true)"
            @mouseout="handleHover(false)"
        >
            <div class="marker-container flex">
                <div class="code-circle">
                    <div
                        class="w-8 h-8 flag-background flag-icon-squared rounded-full overflow-hidden"
                        :class="'flag-icon-' + abbr.toLowerCase()"
                    ></div>
                </div>
                <div class="region-title flex">
                    {{ region }}
                </div>
            </div>
        </div>
    </popper>
</template>

<script>
import LangFlag from "vue-lang-code-flags";
import { EventBus } from "../../event-bus";
import Popper from "vue-popperjs";
import "vue-popperjs/dist/vue-popper.css";

export default {
    name: "WorldMap",
    components: {
        LangFlag,
        popper: Popper
    },
    methods: {
        loadContent(crop) {
            EventBus.$emit("loading-content", {
                business: this.$props.businessId,
                country: this.$props.countryId,
                crop: this.slugify(crop.name)
            });
        },
        linkToCrop(crop) {
            return {
                // name: "business-country-crop",
                params: {
                    business: this.$props.businessId,
                    country: this.$props.countryId,
                    crop: this.slugify(crop.name)
                }
            };
        },
        handleHover(hoverState) {
            if (this.hoverTimeout && hoverState) {
                clearTimeout(this.hoverTimeout);
                this.hoverTimeout = null;
            }
            if (hoverState === false) {
                this.hoverTimeout = setTimeout(
                    function() {
                        this.hover = hoverState;
                    }.bind(this),
                    this.hoverTimeoutDelay
                );
            } else {
                this.hover = hoverState;
            }
        },
        handleRegionClick(countryId, abbr, region) {
            EventBus.$emit("map-region-marker-selected", {
                regionID: countryId,
                regionCode: abbr,
                regionName: region
            });
        }
    },
    props: [
        "abbr",
        "businessId",
        "region",
        "countries",
        "zIndex",
        "countryId",
        "crops",
        "nematodes"
    ],
    data() {
        return {
            hover: false,
            hoverTimeoutDelay: 100
        };
    },
    computed: {
        // cropsLimit() {
        //     if (this.$props.crops.length % 4 === 0) {
        //         return Math.floor(this.$props.crops.length / 4);
        //     }
        //     return Math.floor(this.$props.crops.length / 4) + 1;

        // },
        // limitedCrops() {
        //     let startCounter = 1;
        //     let lastIndex = 0;
        //     let maxIndex = 4;
        //     let crops = [];
        //     for (
        //         let i = 0;
        //         i <= Math.floor(this.$props.crops.length / maxIndex);
        //         i++
        //     ) {
        //         crops[i] = this.$props.crops.filter(function(
        //             item,
        //             ind
        //         ) {
        //             if (ind >= lastIndex && ind < lastIndex + maxIndex) {
        //                 return item;
        //             }
        //         });
        //         lastIndex = maxIndex * startCounter;
        //         if (lastIndex >= this.$props.crops.length) {
        //             lastIndex = this.$props.crops.length;
        //         }
        //         startCounter++;
        //     }
        //     return crops;
        // }
    }
};
</script>

<style scoped>

    .popper {
        width: auto;
        height: 750px;
    }

    .popper .country-hover.wider-country-list {
        flex-direction: column;
        height: 100%;
        overflow: auto;
        width: 100%;
    }


</style>
