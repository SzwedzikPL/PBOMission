import Vue from 'vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import App from './App.vue';

Vue.config.productionTip = false;

new Vue({
  render: h => h(App),
}).$mount('#app');
