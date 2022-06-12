import Vue from 'vue';
import vuetify from '@/js/plugins/vuetify';
import '@/js/plugins/vuelidate';

import App from '@/js/App.vue';

new Vue({
    vuetify,
    render: h => h(App)
}).$mount('#app');
