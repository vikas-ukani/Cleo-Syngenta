<template>
    <div class="ml-4">
        <div class="inline-flex" id="tabs" role="tablist">
            <a
                v-for="(business, index) in businesses"
                :key="business.id"
                :class="linkClasses(business.id, index, businesses.length)"
                @click.prevent="handleBusinessSelect(business)"
                class="p-2 px-3 text-sm font-bold text-white inline-flex items-center business-select-tab"
                href="#"
            >
                <svg
                    class="icon mr-2 h-5 w-5 fill-current"
                    :class="iconClasses(business.id)"
                    role="tab"
                >
                    <use
                        v-show="selectedTab !== business.id"
                        v-bind="{ 'xlink:href': unselectedTabIcon }"
                    ></use>
                    <use
                        v-show="selectedTab === business.id"
                        v-bind="{ 'xlink:href': selectedTabIcon }"
                    ></use>
                </svg>
                <span>{{ business.name }}</span>
            </a>
        </div>
    </div>
</template>

<style lang="scss">
.business-select-tab {
    padding: 0.5em 0.7em;
}
</style>

<script>
import { EventBus } from "../../event-bus";

export default {
    name: "BusinessDropdownComponent",
    data: function() {
        return {
            unselectedTabIcon: "#icon-radio-unselected",
            selectedTabIcon: "#icon-radio-selected"
        };
    },
    computed: {
        active() {
            return this.$store.getters.getAPIRequestState === 0;
        },
        businesses() {
            // console.log('this.$store.getters.getBusinesses', this.$store.getters.getBusinesses)
            return this.$store.getters.getBusinesses;
        },
        selectedTab() {
            return this.$store.getters.getSelectedBusiness;
        }
    },
    methods: {
        iconClasses(id) {
            return {
                "text-white": this.selectedTab === id && this.active,
                "text-color-text": this.selectedTab !== id && this.active,
                "text-disabled-darker": !this.active
            };
        },
        linkClasses(id, index, size) {
            return {
                "rounded-l-lg": id === 1,
                "rounded-r-lg": index === size - 1,
                "bg-button-light text-button-text":
                    this.selectedTab !== id && this.active,
                "bg-brand text-white": this.selectedTab === id && this.active,
                "bg-disabled-lighter text-disabled-darker": !this.active
            };
        },
        handleBusinessSelect: function(business, event) {
            if (event) {
                event.preventDefault();
            }

            this.$store.commit("setSelectedBusiness", business.id);
            EventBus.$emit("business-selected", {
                businessId: business.id
            });

            this.$router.push({
                name: "business",
                params: {
                    business: business.shortname
                }
            });

            this.$store.commit("setSelectedCountry", null);
            this.$store.commit("setSelectedRegion", null);
            this.$store.commit("setSelectedCrop", null);
        }
    }
};
</script>
