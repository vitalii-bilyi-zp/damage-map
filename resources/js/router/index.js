import Vue from 'vue';
import VueRouter from 'vue-router';
import globalMiddleware from '@/js/router/middlewares/global-middleware';

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: "/",
            component: () => import("@/js/layouts/DefaultLayout"),
            children: [
                {
                    path: "/",
                    name: 'map',
                    component: () => import("@/js/pages/Map"),
                },
                {
                    path: "statistics",
                    name: "statistics",
                    component: () => import("@/js/pages/Statistics"),
                },
                {
                    path: "damage-notes",
                    name: "damage-notes",
                    component: () => import("@/js/pages/DamageNotes"),
                    meta: {
                        auth: true,
                    },
                },
                {
                    path: "damage-notes/create",
                    name: "damage-notes.create",
                    component: () => import("@/js/pages/CreateDamageNote"),
                    meta: {
                        auth: true,
                    },
                },
                {
                    path: "damage-notes/:id/edit",
                    name: "damage-notes.edit",
                    component: () => import("@/js/pages/EditDamageNote"),
                    props: true,
                    meta: {
                        auth: true,
                    },
                },
            ]
        },
        {
            path: "/auth",
            component: () => import("@/js/layouts/BlankLayout"),
            children: [
                {
                    path: "/",
                    redirect: { name: "auth.login" },
                },
                {
                    path: "login",
                    name: "auth.login",
                    component: () => import("@/js/pages/Login"),
                    meta: {
                        guest: true,
                    },
                },
            ],
        },
    ]
});

globalMiddleware(router);

export default router;
