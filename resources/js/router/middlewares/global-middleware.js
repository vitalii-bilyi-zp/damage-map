import loadUser from './load-user';
import auth from './auth';
import guest from './guest';

export default (router) => {
    loadUser(router);
    auth(router);
    guest(router);
}
