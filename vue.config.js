// vue.config.js
module.exports = {
    module: {
        rules: [
            {
                test: /\.geojson$/i,
                loader: 'json-loader',
            },
        ],
    },
    css: {
        loaderOptions: {
            sass: {
                data: `
                    @import "@/sass/_variables.scss";
                `
            }
        }
    }
};
