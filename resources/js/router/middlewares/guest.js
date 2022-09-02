import store from '@/js/store/index';

export default function RedirectIfAuthenticated (router) {
    router.beforeEach((to, from, next) => {
        if (to.matched.some(record => record.meta.guest) && store.getters.isAuthorized) {
            return next(from.fullPath);
        }

        return next();
    });
};
