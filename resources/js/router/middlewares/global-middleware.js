import loadUser from './load-user';
import auth from './auth';
import guest from './guest';
import hasRole from './has-role';

export default (router) => {
    loadUser(router);
    auth(router);
    guest(router);
    hasRole(router);
}
