import Router from '@/libs/router';
import Vue from "vue";
import VueNotifications from "vue-notification";
import {default as axiosErrorHandler} from "@/libs/axios-error-handler";
import VueModals from "@/plugins/modals";
import VuePaste from "@/plugins/paste";

Vue.use(VueNotifications, {componentName: 'vue-notifications'});
Vue.use(VueModals);
Vue.use(VuePaste, {url: '/assets'});

axiosErrorHandler(message => Vue.notify({type: 'error', text: message}));

(() => {
  let r = new Router();

  r.on('/Praca/Application/*', () => {
    require('./job/application');
  })
  .on('/Praca/Payment/*', () => {
    require('./job/payment');
  })
  .on('/Praca/Oferta', () => {
    require('./job/business');
  })
  .on(['/Praca', '/Praca/Miasto/*', '/Praca/Technologia/*', '/Praca/Zdalna', '/Praca/Firma/*', '/Praca/Moje'], () => {
    require('./job/homepage');
  })
  .on('/Praca/\\d+\\-*', () => {
    require('./job/offer');
  })
  .on(['/Praca/Submit*', '/Praca/Renew/*'], () => {
    import('./job/submit');
  });

  r.resolve();
})();
