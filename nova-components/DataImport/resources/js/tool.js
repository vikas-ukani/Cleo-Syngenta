Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'data-import',
            path: '/data-import',
            component: require('./components/Main'),
        },
        {
            name: 'data-import-preview',
            path: '/data-import/preview/:file',
            component: require('./components/Preview'),
            props: route => {
                return {
                    file: route.params.file,
                }
            },
        },
        {
            name: 'data-import-review',
            path: '/data-import/review/:file',
            component: require('./components/Review'),
            props: route => {
                return {
                    file: route.params.file,
                }
            },
        },
    ])
})
