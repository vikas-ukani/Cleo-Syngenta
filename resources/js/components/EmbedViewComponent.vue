<template>
    <div class="page w-full h-full bg-content-background z-0 absolute bottom-0 left-0 right-0 top-0 z-0">
        <div class="flex flex-fill w-full h-full">
            <div class="p-10 w-full">

            </div>
        </div>
    </div>
</template>

<script>

    import ContentApi from "../api/content";
    import {EventBus} from "../event-bus";

    export default {
        computed: {
            pageId() {
                return this.$route.params.id;
            },
            filename() {
                return this.$route.params.filename;
            },
        },
        data() {
            return {
                content: null,
                media: null,
                menu: null,
            }
        },
        mounted() {

            if (this.$route.params) {

                let params = {};
                params.id = (this.id) ? this.id : null;
                params.filename = (this.filename) ? this.filename : null;

                ContentApi.getRelatedMedia(params)
                    .then(function (response) {

                        this.loadingContent = false;
                        this.menu = response.data.menu;
                        this.content = response.data.content[0];
                        this.media = response.data.content[0].media;

                    }.bind(this))
                    .catch(function (data) {
                        EventBus.$emit('show-alert', {
                            dismiss: false,
                            message: 'There was an error requesting disease data from the API.',
                        });
                    }.bind(this));
            }
        }
    }
</script>

<style lang="scss" scoped>

    @import "../../sass/_variables";

    .page {
        box-shadow: 0 10px 15px -10px rgba(0, 0, 0, .4),
        0 4px 6px 5px rgba(0, 0, 0, .4);
    }

</style>
