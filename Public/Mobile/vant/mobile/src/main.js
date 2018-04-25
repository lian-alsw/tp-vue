import Vue from 'vue';
import App from './App.vue';
require('./mock.js')
new Vue({
  el: '#app',
  render: h => h(App)
});
