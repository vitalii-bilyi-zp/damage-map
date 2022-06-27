import Vue from 'vue';
import vuetify from '@/js/plugins/vuetify';
import '@/js/plugins/vuelidate';

import App from '@/js/App.vue';
import store from '@/js/store';

import HttpClient from '@/js/api/HttpClient';

window.httpClient = new HttpClient();

new Vue({
    store,
    vuetify,
    render: h => h(App)
}).$mount('#app');
