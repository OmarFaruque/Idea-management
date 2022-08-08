import { createApp } from 'vue/dist/vue.esm-bundler'
import style from './idea.scss'
import FetchWP from './utils/fetchWP'
import axios from 'axios'
import VueAxios from 'vue-axios'

let fetchWP = new FetchWP({
  restURL: window.idea_object.root,
  restNonce: window.idea_object.api_nonce,
});

createApp({
    data() {
      return {
        message: '', 
        idea_types: [], 
        style: style, 
        idea_type: '', 
        title: '', 
        content: '', 
        file: '', 
        listing: true, 
        ideas: [], 
        idea_url: window.idea_object.homepage + window.idea_object.cpost
      }
    },

    created() {
      this.fetchData()
    }, 
    methods: {
      fetchData() {

        fetchWP.get('getconfig/')
            .then(
                (json) => {
                    console.log('json: ', json)
                    this.idea_types = json.idea_type
                    this.ideas = json.ideas
                })
                .catch(function(error) {
                    console.log('error', error);
                });
      },
      
      
      handleFileUpload(){
        
      }, 

      backtolisting(){
        console.log('back to listing');
      },

      formSubmit(e=false){
        this.title = this.$refs.title.value
        this.idea_type = this.$refs.idea_type.value
        this.content = this.$refs.content.value
        this.file = this.$refs.file.files[0];

        const formData = new FormData();
        
        formData.append('title', this.title);
        formData.append('idea_type', this.idea_type);
        formData.append('content', this.content);
        formData.append('file', this.file, this.file.name);

        let headers = {
          'Content-Type': 'multipart/form-data',
          'X-WP-Nonce': window.idea_object.api_nonce,
        }
        

        axios.post(window.idea_object.root + 'create/', formData, {headers: headers})
        .then(
          (response) => {
            console.log('response: ', response)
          })
          .catch(function(error){
            console.log('error: ', error)
          })

      }
    }

  }).mount('#im_form')