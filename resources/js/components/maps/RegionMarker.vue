<template>
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
            <div
                v-if="countries.length > 5"
                class="country-hover wider-country-list z-50 flex"
            >
                <table
                    :key="'countrylim_' + index"
                    v-for="(repeat, index) in countriesLimit"
                    class="table-auto pb-4 text-sm rounded overflow-hidden"
                >
                    <tr class="bg-gray-500">
                        <th class="text-sm font-semibold uppercase py-1">
                            Country
                        </th>
                        <th class="text-sm font-light uppercase align-left">
                            Pest / Pathogen
                        </th>
                        <th class="text-sm font-light uppercase align-left">
                            Crop
                        </th>
                    </tr>
                    <tr
                        :key="'country_' + index"
                        v-for="(country, index) in limitedCountries[repeat - 1]"
                    >
                        <td class="font-semibold" width="160px">
                            <div class="flex py-2">
                                <div
                                    class="w-8 h-8 mx-2 border border-gray-500 flag-background flag-icon-squared rounded-full overflow-hidden"
                                    :class="
                                        'flag-icon-' +
                                            country.iso_code.toLowerCase()
                                    "
                                ></div>
                                <a
                                    class="flex items-center align-center country-name"
                                    @click.prevent="
                                        handleCountryNameClicked(country)
                                    "
                                >
                                    {{ country.name }}
                                </a>
                            </div>
                        </td>
                        <td
                            class="px-3 py-1 align-center vertical-center"
                            width="120px"
                        >
                            <p>
                                <span
                                    :key="'nematode_' + index"
                                    v-for="(nematode,
                                    index) in country.nematodes"
                                    >{{
                                        index !== country.nematodes.length - 1
                                            ? nematode.name + ", "
                                            : nematode.name + ""
                                    }}</span
                                >
                            </p>
                        </td>
                        <td class="px-3 py-1 align-center vertical-center">
                            <p>
                                <span
                                    :key="'crop_' + index"
                                    v-for="(crop, index) in country.crops"
                                >
                                    <a
                                        @click.prevent="loadContent(crop)"
                                        href="#"
                                        >{{
                                            index !== country.crops.length - 1
                                                ? crop.name + ", "
                                                : crop.name + ""
                                        }}</a
                                    >
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div
                v-if="countries.length > 0 && countries.length < 5"
                class="country-hover z-50"
            >
                <table class="table-auto pb-4 text-sm rounded overflow-hidden">
                    <tr class="bg-gray-500">
                        <th class="text-sm font-semibold uppercase py-1">
                            Country
                        </th>
                        <th class="text-sm font-light uppercase align-left">
                            Pest / Pathogen
                        </th>
                        <th class="text-sm font-light uppercase align-left">
                            Crop
                        </th>
                    </tr>
                    <tr
                        :key="'countrynames_' + index"
                        v-for="(country, index) in countries"
                    >
                        <td class="font-semibold" width="160px">
                            <div class="flex py-2">
                                <div
                                    class="w-8 h-8 mx-2 border border-gray-500 flag-background flag-icon-squared rounded-full overflow-hidden"
                                    :class="
                                        'flag-icon-' +
                                            country.iso_code.toLowerCase()
                                    "
                                ></div>
                                <a
                                    class="flex items-center align-center country-name"
                                    href="#"
                                    @click.prevent="
                                        handleCountryNameClicked(country)
                                    "
                                >
                                    {{ country.name }}
                                </a>
                            </div>
                        </td>
                        <td
                            class="px-3 py-1 align-center vertical-center text-left"
                            width="120px"
                        >
                            <p>
                                <span
                                    :key="'nmlst_' + index"
                                    v-for="(nematode,
                                    index) in country.nematodes"
                                    >{{
                                        index !== country.nematodes.length - 1
                                            ? nematode.name + ", "
                                            : nematode.name + ""
                                    }}</span
                                >
                            </p>
                        </td>
                        <td
                            class="px-3 py-1 align-center vertical-center text-left"
                        >
                            <p>
                                <span
                                    :key="'croplist_' + index"
                                    v-for="(crop, index) in country.crops"
                                >
                                    <a
                                        @click.prevent="loadContent(crop)"
                                        href="#"
                                    >
                                        {{
                                            index !== country.crops.length - 1
                                                ? crop.name + ", "
                                                : crop.name + ""
                                        }}
                                    </a>
                                </span>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div
            class="map-marker absolute z-40"
            slot="reference"
            :class="{
                hovered: hover && countries.length > 0,
                hidden: countries.length === 0
            }"
            @mouseover="handleHover(true)"
            @mouseleave="handleHover(false)"
        >
            <div
                class="marker-container flex"
                @click.once="handleRegionClick(regionId, abbr, region)"
            >
                <div class="code-circle">
                    {{ abbr }}
                </div>
                <div class="region-title flex">
                    {{ region }}
                </div>
            </div>
        </div>
    </popper>
</template>

<style lang="scss" scoped>
.country-name {
    color: rgba(0, 0, 0, 1);

    &:hover {
        text-decoration: underline;
    }
}
</style>

<script>
import LangFlag from "vue-lang-code-flags";
import { EventBus } from "../../event-bus";
import Popper from "vue-popperjs";
import "vue-popperjs/dist/vue-popper.css";

export default {
    name: "RegionMarker",
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
        handleCountryNameClicked(country) {
            EventBus.$emit("region-hover-country-clicked", {
                id: country.id,
                code: country.iso_code,
                name: country.name
            });
        },
        linkToCrop(crop) {
            return {
                name: "business-crop",
                params: {
                    business: this.$props.businessId,
                    crop: this.slugify(crop.name) || null
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
        handleRegionClick(regionId, abbr, region) {
            EventBus.$emit("map-region-marker-selected", {
                regionID: regionId,
                regionCode: abbr,
                regionName: region
            });
        }
    },
    props: ["abbr", "region", "countries", "regionId", "businessId"],
    data() {
        return {
            hoverTimeout: false,
            hoverTimeoutDelay: 100,
            hover: false,
            zIndex: 10,
            countryLimit: 4
        };
    },
    computed: {
        countriesLimit() {
            if (this.$props.countries.length % 4 === 0) {
                return Math.floor(this.$props.countries.length / 4);
            }
            return Math.floor(this.$props.countries.length / 4) + 1;
        },
        limitedCountries() {
            let startCounter = 1;
            let lastIndex = 0;
            let maxIndex = 4;
            let countries = [];
            for (
                let i = 0;
                i <= Math.floor(this.$props.countries.length / maxIndex);
                i++
            ) {
                countries[i] = this.$props.countries.filter(function(
                    item,
                    ind
                ) {
                    if (ind >= lastIndex && ind < lastIndex + maxIndex) {
                        return item;
                    }
                });
                lastIndex = maxIndex * startCounter;
                if (lastIndex >= this.$props.countries.length) {
                    lastIndex = this.$props.countries.length;
                }
                startCounter++;
            }
            return countries;
        }
    }
};
</script>
