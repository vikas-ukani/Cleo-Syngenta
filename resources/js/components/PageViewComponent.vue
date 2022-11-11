<template>
    <div
        ref="page-view-content"
        id="page-view-content"
        class="page w-full bg-content-background z-40 absolute"
    >
        <div class="flex flex-fill w-full h-full">
            <div class="p-10 w-full">
                <div id="loader" v-if="loadingContent">
                    <div
                        class="flex w-full h-56 justify-center items-center flex-col"
                    >
                        <span class="spinner w-56 h-56"></span>
                        <p>Loading Content...</p>
                    </div>
                </div>

                <div id="content" class="w-full flex" v-if="!loadingContent">
                    <div class="w-2/12">
                        <a
                            @click.prevent="hideContent"
                            class="text-brand font-semibold text-sm"
                            href="#"
                            >Back to Map</a
                        >
                        <div class="text-brand" v-if="menu !== null">
                            <h3 class="my-3 font-bold text-sm uppercase">
                                Menu
                            </h3>
                            <ul>
                                <li
                                    :key="'crop_' + index"
                                    v-for="(crop, index) in crops"
                                >
                                    <router-link
                                        @click.native="loadContent(crop)"
                                        :to="linkToCrop(crop)"
                                    >
                                        {{ crop.name }}</router-link
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-if="!loadingContent" class="w-10/12">
                        <div class="flex">
                            <div class="bg-white rounded-lg p-5 my-0 w-9/12">
                                <div v-if="content">
                                    <h2 class="breadcrumb">
                                        <strong class="text-brand font-semibold text-sm clickable " @click="hideContent()" >Technical Manual</strong> /
                                        {{ selectedBusiness.name }} /
                                        {{ content.title }}
                                    </h2>
                                    <div v-if="content && content.title">
                                        <h3
                                            class="text-3xl font-semibold"
                                            v-html="content.title"
                                        ></h3>
                                        <img
                                            v-bind="{
                                                src: '/storage/' + content.image
                                            }"
                                            class="w-200 rounded border"
                                            :alt="content.title"
                                        />
                                        <div
                                            class="my-3 text-sm"
                                            v-html="content.intro"
                                        ></div>

                                        <div v-if="content.technical_data">
                                            <h2 id="technical-data-formulation">
                                                Technical Data
                                            </h2>

                                            <div
                                                class="rounded-lg overflow-hidden border border-gray-400 p-4"
                                            >
                                                <table class="table-auto">
                                                    <tr class="text-left">
                                                        <th
                                                            class="bolder text-left"
                                                        >
                                                            Country
                                                        </th>
                                                        <th
                                                            class="bolder text-left"
                                                        >
                                                            Formulation
                                                        </th>
                                                        <th
                                                            class="bolder text-left"
                                                        >
                                                            A Code
                                                        </th>
                                                        <th
                                                            class="bolder text-left"
                                                        >
                                                            Target
                                                        </th>
                                                        <th
                                                            class="bolder text-left"
                                                        >
                                                            Treatment Type
                                                        </th>
                                                        <th
                                                            class="bolder text-left cgap"
                                                        >
                                                            cGAP
                                                        </th>
                                                        <th
                                                            class="bolder text-left"
                                                        >
                                                            Registration
                                                        </th>
                                                    </tr>

                                                    <tr
                                                        v-for="(td,
                                                        index) in content.technical_data"
                                                        :key="'td_' + index"
                                                        v-if="td.attributes.business_id == (selectedBusinessId + 1)"
                                                    >
                                                        <td
                                                            v-if="countryName(td.attributes.country_id)"
                                                            class=""
                                                        >
                                                            {{
                                                                countryName(
                                                                    td.attributes
                                                                        .country_id
                                                                )
                                                            }}
                                                        </td>
                                                        <td
                                                            v-if="
                                                                countryName(
                                                                    td.attributes
                                                                        .country_id
                                                                )
                                                            "
                                                            class=""
                                                        >
                                                            {{
                                                                td.attributes
                                                                    .formulation
                                                            }}
                                                        </td>
                                                        <td
                                                            v-if="
                                                                countryName(
                                                                    td.attributes
                                                                        .country_id
                                                                )
                                                            "
                                                            class=""
                                                        >
                                                            {{
                                                                td.attributes.a_code
                                                            }}
                                                        </td>
                                                        <td
                                                            v-if="
                                                                countryName(
                                                                    td.attributes
                                                                        .country_id
                                                                )
                                                            "
                                                            class=""
                                                        >
                                                            {{
                                                                td.attributes.target
                                                            }}
                                                        </td>
                                                        <td
                                                            v-if="
                                                                countryName(
                                                                    td.attributes
                                                                        .country_id
                                                                )
                                                            "
                                                            class=""
                                                        >
                                                            {{
                                                                td.attributes
                                                                    .treatment_type
                                                            }}
                                                        </td>
                                                        <td
                                                            v-if="
                                                                countryName(
                                                                    td.attributes
                                                                        .country_id
                                                                )
                                                            "
                                                            class=""
                                                            v-html="
                                                                td.attributes.cgap
                                                            "
                                                        ></td>
                                                        <td
                                                            v-if="
                                                                countryName(
                                                                    td.attributes
                                                                        .country_id
                                                                )
                                                            "
                                                            class=""
                                                        >
                                                            {{
                                                                td.attributes
                                                                    .registration
                                                            }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div
                                            class="additional-content p-5 my-5 rounded bg-gray-100"
                                            :key="'content' + index"
                                            v-for="(_content,
                                            index) in content.additional_content"
                                        >
                                            <h2 class="font-semibold mb-2">
                                                {{ _content.attributes.title }}
                                            </h2>
                                            <div
                                                v-html="_content.attributes.body"
                                            ></div>
                                        </div>

                                    </div>
                                    <h1 class="mt-20 text-2xl text-center text-gray-600" v-else>No any crop contents available to show.</h1>
                                 </div>
                            </div>

                            <div v-if="media !== null" class="px-5 my-5 w-3/12">
                                <dl>
                                    <dd
                                        :key="'media' + index"
                                        v-for="(item, index) in media"
                                        class="border-b py-3"
                                    >
                                        <a
                                            href="#"
                                            class="flex items-center"
                                            @click.prevent="loadMedia(item)"
                                        >
                                            <span class="w-6 mr-2">
                                                <svg
                                                    :class="
                                                        getIcon(item.mime_type)
                                                            .color
                                                    "
                                                    class="icon mr-2 h-5 w-5 fill-current"
                                                >
                                                    <use
                                                        v-bind="{
                                                            'xlink:href':
                                                                '#icon-' +
                                                                getIcon(
                                                                    item.mime_type
                                                                ).icon
                                                        }"
                                                    ></use>
                                                </svg>
                                            </span>
                                            {{
                                                item.custom_properties.filename
                                                    ? item.custom_properties
                                                          .filename
                                                    : item.name
                                            }}
                                        </a>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ContentApi from "../api/content";
import { EventBus } from "../event-bus";

export default {
    props: ["loadedViaMapview", "contentParams"],
    computed: {
        selectedBusinessId() {
            return this.$store.getters.getSelectedBusiness - 1;
        },
        selectedBusiness() {
            return this.$store.getters.getBusinesses[this.selectedBusinessId];
        },
        regions() {
            return this.$store.getters.getRegions;
        },
        country() {
            return this.$route.params.country;
        },
        cropOrNematode() {
            return this.$route.params.cropOrDisease;
        },
        nematode() {
            return this.$route.params.disease;
        },
        crop() {
            return this.$route.params.crop;
        },
        crops() {
            return this.$store.getters.getBusinesses[this.selectedBusinessId]
                .crops;
        },
        slug() {
            return this.$route.params.slug;
        }
    },
    data() {
        return {
            loadingContent: true,
            content: {},
            media: {},
            menu: {},
            contentParameters: {}
        };
    },
    methods: {
        fetchContent(data) {
            ContentApi.getContentByParams(data)
                .then(
                    function(response) {
                        if (response.data.crop) {
                            this.loadingContent = null;
                            this.media = response.data.crop.media;

                            /** technical data filterd by Bussiness id */
                            var technical_datas = response.data.crop.technical_data.filter(technical_data => {
                                return technical_data.attributes.business_id == (this.selectedBusinessId + 1)
                            });
                            this.content = {
                                title: response.data.crop.name,
                                image: response.data.crop.image,
                                intro: response.data.crop.description,
                                technical_data: technical_datas,
                                additional_content: response.data.crop.content
                            };
                        } else {
                            this.loadingContent = null;
                        }
                    }.bind(this)
                )
                .catch(
                    function(data) {
                        EventBus.$emit("show-alert", {
                            dismiss: false,
                            message:
                                "There was an error requesting the required content from the API. Please reload the page."
                        });
                    }.bind(this)
                );
        },
        loadContent(crop) {
            EventBus.$emit("reload-content", {
                business: this.selectedBusiness.shortname,
                country: this.country,
                crop: this.slugify(crop.name)
            });
        },
        linkToCrop(crop) {
            return {
                name: "business-crop",
                params: {
                    business: this.selectedBusiness.shortname,
                    crop: this.slugify(crop.name) || null
                }
            };
        },
        countryName(id) {
            let countries = this.$store.getters.getBusinesses[this.selectedBusinessId].countries.filter(function(c) {
                return (parseInt(c.id) === parseInt(id))
            }, this);
            if (countries && countries.length > 0) {
                let country = countries[0]

                return country.name;
            }
            return null;
        },
        hideContent() {
            EventBus.$emit("hide-content");
        },
        loadMedia(item) {
            console.log(item);
        },
        getIcon(mime_type) {
            switch (mime_type) {
                case "application/pdf":
                    return {
                        icon: "application-pdf",
                        color: "text-icon-pdf"
                    };
                case "application/msword":
                case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
                    return {
                        icon: "application-word",
                        color: "text-icon-word"
                    };
                case "application/msexcel":
                case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                    return {
                        icon: "application-excel",
                        color: "text-icon-excel"
                    };
                case "image/jpg":
                case "image/jpeg":
                case "image/png":
                    return {
                        icon: "download-image",
                        color: "text-icon-image"
                    };
                case "video/quicktime":
                case "video/mp4":
                    return {
                        icon: "download-video",
                        color: "text-icon-video"
                    };
                default:
                    return {
                        icon: "download-image",
                        color: "text-icon-image"
                    };
            }
        }
    },
    mounted() {
        if (
            typeof this.$props.loadedViaMapview !== "undefined" &&
            this.$props.loadedViaMapview
        ) {
            this.contentParameters = this.$props.contentParams;
        }

        if (this.$route.params && !this.$props.loadedViaMapview) {
            this.contentParameters.business = this.selectedBusiness.shortname
                ? this.selectedBusiness.shortname
                : null;
            this.contentParameters.crop = this.crop ? this.crop : null;
        }

        let currentBusiness = this.selectedBusiness.shortname;

        this.fetchContent(this.contentParameters);

        EventBus.$on(
            "reload-content",
            function(data) {
                this.fetchContent(data);
            }.bind(this)
        );
    }
};
</script>

<style lang="scss" scoped>
@import "../../sass/_variables";

.page {
    box-shadow: 0 10px 15px -10px rgba(0, 0, 0, 0.4),
        0 4px 6px 5px rgba(0, 0, 0, 0.4);

    .breadcrumb {
        strong {
            color: $c-main-green;
        }
    }
}

table {
    width: 100%;
    tr {
        width: 100%;
        &:nth-child(2n) {
            background: rgba(200, 200, 200, 0.1);
        }
    }
    th {
        padding-left: 5px;
        padding-right: 10px;
        font-size: 13px;

        &.cgap {
            // width: 40%;
        }
    }
    td {
        padding-left: 5px;
        font-size: 13px;
    }
}

.spinner {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}

.clickable{
    cursor: pointer !important;
}
.spinner::after {
    content: "";
    position: absolute !important;
    top: calc(50% - (4em / 2));
    left: calc(50% - (4em / 2));
    display: block;
    width: 4em;
    height: 4em;
    border: 5px solid $c-main-green;
    border-radius: 9999px;
    border-right-color: transparent;
    border-top-color: transparent;
    animation: spinAround 800ms infinite linear;
}

@keyframes spinAround {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
