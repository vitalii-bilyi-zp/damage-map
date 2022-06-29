import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'map',
            component: () => import("@/js/pages/Map"),
        },
        {
            path: "/damage-notes",
            name: "damage-notes",
            component: () => import("@/js/pages/DamageNotes"),
        }
    ]
});

export default router;
