<template>
    <div ref="mainHeader" class="p-4 h-18 bg-white">
        <div class="panel-header__flex">
            <div class="panel-header__home">
                <h1>
                    <router-link class="text-brand" to="/" v-html="title"></router-link>
                </h1>
                <strong>Version</strong>: {{ version }}

            </div>
            <div class="panel-header__logo">
                <div class="">
                    <svg width="124px" height="39px" viewBox="0 0 124 39" version="1.1"
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink">
                        <use xlink:href="#logo-brand-color"></use>
                    </svg>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import {EventBus} from "../event-bus";

    export default {
        computed: {
            categories() {
                return [
                    {
                        name: 'Confidentiality',
                        url: '#'
                    },
                    {
                        name: 'Tymirium Brochure',
                        url: '#'
                    }
                ];
            }
        },
        data: function () {
            return {
                title: 'TYMIRIUM&trade;',
                version: '1.0.20200831'
            }
        },
        mounted() {
            EventBus.$emit('header-mounted', {
                headerHeight: this.$refs.mainHeader.clientHeight,
            });
        }
    }
</script>
