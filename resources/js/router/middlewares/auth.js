import store from '@/js/store/index';

export default function RedirectIfAuthenticated(router) {
    router.beforeEach((to, from, next) => {
        if (to.matched.some(record => record.meta.auth) && !store.getters.isAuthorized) {
            return next({
                name: 'auth.login',
                params: { nextUrl: to.fullPath }
            });
        }

        return next();
    });
};
