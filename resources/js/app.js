import Vue from 'vue';
import vuetify from '@/js/plugins/vuetify'

import App from '@/js/App.vue';

console.log(vuetify)

new Vue({
    vuetify,
    render: h => h(App)
}).$mount('#app');
