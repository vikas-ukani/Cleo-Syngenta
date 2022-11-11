import MasterComponent from './components/MasterComponent';
import PageViewComponent from "./components/PageViewComponent";
import EmbedViewComponent from "./components/EmbedViewComponent";

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            component: MasterComponent,
            name: 'master',
        },
        {
            path: '/map/:business',
            component: MasterComponent,
            name: 'business',
            children: [
                {
                    path: '/embed/:id/:filename',
                    component: EmbedViewComponent,
                    name: 'embed'
                },
                {
                    path: '/data/:business/:crop',
                    component: PageViewComponent,
                    name: 'business-crop',
                },

            ]
        },

    ]

};
