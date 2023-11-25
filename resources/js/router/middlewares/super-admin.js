import store from '@/js/store/index';

export default function RedirectIfAuthenticated (router) {
    router.beforeEach((to, from, next) => {
        if (to.matched.some(record => record.meta.superAdmin) && !store.getters.isSuperAdmin) {
            return next(from.fullPath);
        }

        return next();
    });
};
