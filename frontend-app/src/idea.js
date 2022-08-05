import { createApp } from 'vue/dist/vue.esm-bundler'
import FetchWP from './utils/fetchWP'

createApp({
    data() {
      return {
        message: 'Hello Vue!'
      }
    },

    setup() {
        let fetchWP = new FetchWP({
            restURL: window.idea_object.root,
            restNonce: window.idea_object.api_nonce,
        });

        fetchWP.get('getconfig/')
            .then(
                (json) => {
                    console.log('config is: ', json)
                })
                .catch(function(error) {
                    console.log('error', error);
                });
    }

  }).mount('#im_form')