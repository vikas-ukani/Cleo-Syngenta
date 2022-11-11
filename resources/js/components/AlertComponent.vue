<template>
    <div v-show="show" class="z-20 absolute w-full bg-brand text-center py-2 lg:px-2">
        <div
            class="p-2 bg-brand-lighter items-center text-white leading-none lg:rounded-full flex lg:inline-flex"
            role="alert">
            <span class="flex rounded-full bg-white uppercase px-2 py-1 text-xs font-bold mr-3 text-brand-lighter">
                {{ messageType }}
            </span>
            <span class="font-semibold mr-2 text-left flex-auto text-white">
                {{ message }}
            </span>
        </div>
    </div>
</template>

<script>

    import {EventBus} from '../event-bus.js';

    export default {
        data() {
            return {
                show: false,
                message: '',
                messageType: 'info',
            }
        },
        mounted() {
            EventBus.$on('show-alert', function (data) {

                this.show = true;
                this.message = data.message;
                this.messageType = (data.messageType) ? data.messageType : 'info';

                if (data.dismiss === true) {
                    setTimeout(function () {
                        this.show = false;
                    }.bind(this), 2000);
                }

            }.bind(this));
        },
        beforeDestroy() {
            EventBus.$off('show-alert');
        }
    }
</script>
