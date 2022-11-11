<template>
    <div>
        <heading class="mb-6">Data Importer</heading>
        <card class="flex flex-col items-center justify-center" style="min-height: 300px">
            <p class="pb-4">Upload your CSV file here to upload/modify crop content.</p>
            <input class="mb-4" type="file" name="file" ref="file" @change="handleFile">
            <button type="submit" class="btn btn-default btn-primary" @click="upload">Import</button>
        </card>
    </div>
</template>

<script>
export default {
    mounted() {
        //
    },
    data() {
        return {
            file: '',
        };
    },
    methods: {
        handleFile: function (event) {
            this.file = this.$refs.file.files[0];
        },
        upload: function (event) {

            let formData = new FormData();
            formData.append('file', this.file);
            const self = this;

            Nova.request()
                .post('/nova-vendor/data-import/upload',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(function (response) {
                self.$router.push({name: 'data-import-preview', params: {file: response.data.file}})
            }).catch(function (e) {
                self.$toasted.show(e.response.data.message, {type: "error"});
            });

        }
    }
}
</script>

<style>
/* Scoped Styles */
</style>
