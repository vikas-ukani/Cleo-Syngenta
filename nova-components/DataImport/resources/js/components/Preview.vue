<template>
    <div>
        <heading class="mb-6">Data Importer</heading>

        <card class="flex flex-col" style="min-height: 300px">
            <div class="p-8">
                <h2 class="pb-4">Preview</h2>
                <p class="pb-4">
                    We were able to discover <b>{{ headings.length }}</b> column(s) and <b>{{ total_rows }}</b>
                    row(s) in your data.
                </p>

                <p><strong>Please note</strong>: Importing the data will overwrite existing values.</p>
            </div>

            <table class="table w-full">
                <thead>
                    <tr>
                        <th v-for="heading in headings">{{ heading }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td v-for="heading in headings" class="text-center">
                            <select class="w-full form-control form-select" v-model="mappings[heading]">
                                <option value="">- Ignore this column -</option>
                                <option v-for="field in fields[resource]" :value="field.attribute">{{ field.name }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr v-for="row in rows">
                        <td v-for="col in row">{{ col }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="bg-30 flex px-8 py-4">
                <!--<button class="btn btn-default">&leftarrow; Cancel</button>-->
                <button class="btn btn-default btn-primary" @click="runImport" id="run-import">Import &rightarrow; </button>
            </div>
        </card>
    </div>
</template>

<script>
export default {
    mounted() {
        const self = this;

        Nova.request()
            .get('/nova-vendor/data-import/preview/' + this.file)
            .then(function (response) {


                self.headings = response.data.headings;
                self.rows = response.data.sample;
                self.resources = response.data.resources;
                self.total_rows = response.data.total_rows;
                self.fields = response.data.fields;

                self.headings.forEach(function (heading) {
                    self.$set(self.mappings, heading, "");
                });
            });
    },
    data() {
        return {
            headings: [],
            rows: [],
            resources: [],
            fields: [],
            resource: '',
            mappings: {},
        };
    },
    props: [
        'file'
    ],
    watch: {
        resource : function (resource) {
            const self = this;

            // Reset all of the headings to blanks
            this.headings.forEach(function (heading) {
                self.$set(self.mappings, heading, "");
            });

            if (resource === "") {
                return;
            }

            // For each field of the resource, try to find a matching heading and pre-assign
            this.fields[resource].forEach(function (field_config) {
                let field = field_config.attribute,
                    heading_index = self.headings.indexOf(field);

                if (heading_index < 0) {
                    return;
                }

                let heading = self.headings[heading_index];

                if (heading === field) {
                    self.$set(self.mappings, heading, field);
                }
            });
        }
    },
    methods: {
        runImport: function () {
            const self = this;

            // if (! this.hasValidConfiguration()) {
            //     return;
            // }

            const button = document.getElementById('run-import');
            button.innerHTML = 'Importing...';
            button.setAttribute("disabled", "disabled");

            let data = {
                resource: this.resource,
                mappings: this.mappings
            };

            Nova.request()
                .post(this.url('import/' + this.file), data)
                .then(function (response) {
                    if (response.data.result === 'success') {
                        self.$toasted.show('All data imported!', {type: "success"});
                        self.$router.push({name: 'data-import-review', params: {file: self.file, resource: self.resource}});
                    } else {
                        button.innerHTML = 'Import &rightarrow;';
                        button.removeAttribute("disabled");
                        self.$toasted.show('There were problems importing some of your data', {type: "error"});
                    }
                });

        },
        hasValidConfiguration: function () {
            const mappedColumns = [],
                mappings = this.mappings;

            Object.keys(mappings).forEach(function (key) {
                if (mappings[key] !== "") {
                    mappedColumns.push(key);
                }
            });

            return this.resource !== '' && mappedColumns.length > 0;
        },
        url: function (path) {
            return '/nova-vendor/data-import/' + path;
        }
    },
    computed: {
        disabledImport: function () {
            return ! this.hasValidConfiguration();
        },
    }
}
</script>

<style>
/* Scoped Styles */
</style>
