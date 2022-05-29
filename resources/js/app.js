import { createApp } from 'vue';
import App from '@/js/App.vue';
import axios from 'axios';
import VueAxios from 'vue-axios';

const app = createApp(App);

app.use(VueAxios, axios);

app.mount('#app');
