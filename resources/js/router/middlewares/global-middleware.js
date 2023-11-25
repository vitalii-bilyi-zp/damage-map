import loadUser from './load-user';
import auth from './auth';
import guest from './guest';
import superAdmin from './super-admin';

export default (router) => {
    loadUser(router);
    auth(router);
    guest(router);
    superAdmin(router);
}
