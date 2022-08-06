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
        message: 'Hello Vue!', 
        idea_types: [], 
        style: style, 
        idea_type: '', 
        title: '', 
        content: '', 
        file: ''
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
                })
                .catch(function(error) {
                    console.log('error', error);
                });
      },
      
      
      handleFileUpload(){
        
      }, 


      formSubmit(e=false){
        this.title = this.$refs.title.value
        this.idea_type = this.$refs.idea_type.value
        this.content = this.$refs.content.value
        this.file = this.$refs.file.files[0];

        const formData = new FormData();
        
        formData.append('file', this.file, this.file.name);

        let data = {
          title: this.title, 
          idea_type: this.idea_type, 
          content: this.content, 
          // file: formData
        }

        // console.log('form data: ', data)
        // console.log('filename: ', this.file.name)
        // fetchWP.post('create/', data)
        //     .then(
        //         (json) => {
        //             console.log('json: ', json)
        //         })
        //         .catch(function(error) {
        //             console.log('error', error);
        //         });





        axios.get('https://api.coindesk.com/v1/bpi/currentprice.json')
        .then(response => ( console.log('res: ', response) ))
   
            // try {
            //   let response = this.$axios.post('create/', data, {
            //     headers: {
            //       'Content-Type': 'multipart/form-data; boundary=' + formData._boundary
            //     }
            //   })
            //   console.log('response : ', response)
            //   if (response.status === 200 && response.data.status === 'success') {
            //     console.log(this.response)
            //   }
            // } catch (e) {
            //  console.log(e)
            // }
        
        


      }
    }

  }).mount('#im_form')