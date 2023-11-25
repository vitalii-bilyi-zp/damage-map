import store from '@/js/store/index';

export default function RedirectIfAuthenticated (router) {
    router.beforeEach((to, from, next) => {
        const routeWithRoles = to.matched.find(record => !!record.meta.roles);
        const roles = routeWithRoles ? routeWithRoles.meta.roles : null;

        if (roles) {
            const currentUserRoles = store.state.currentUser ? store.state.currentUser.roles : [];
            if (!currentUserRoles.some(item => roles.includes(item))) {
                return next(from.fullPath);
            }
        }

        return next();
    });
};
