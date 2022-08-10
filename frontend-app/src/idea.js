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
        idea_url: window.idea_object.homepage + window.idea_object.cpost, 
        idea_collection_end_date: window.idea_object.idea_collection_end_date,
        list_by_category: '', 
        loader: false, 
        user_vote_status: false, 
        idea_filter: 'top_rated', 
        show_msg: false, 
        vote_allowed: window.idea_object.vote_allowed, 
        datediff: window.idea_object.datediff
      }
    },

    created() {
      this.fetchData()
      if(!window.idea_object.user_login){
        window.location.replace(window.idea_object.homepage);
      }
    }, 
    methods: {

      //Idea vote process
      voteprocess(id, v_type){
        this.loader = true
        let data = {
          post_id: id, 
          v_type: v_type, 
          category: this.list_by_category, 
          idea_filter: this.idea_filter
        }

        fetchWP.post('idea_vote/', data)
            .then(
                (json) => { 
                  this.ideas = json.ideas
                  this.loader = false
                })
                .catch(function(error) {
                    console.log('error', error);
                });

      },
      // List by category filter
      listByCategory(){
        this.list_by_category = this.$refs.list_by_category.value 
        this.fetchData()
      },

      //Go to form page or back to listing
      gotoNewIdeaForm(){
        this.listing = this.listing ? false : true
      },

      // Filter by status 
      ideaFilter(){
        this.idea_filter = this.$refs.idea_filter.value
        this.fetchData();
      },

      fetchData() {
        this.loader = true
        let data = {
          category: this.list_by_category,
          idea_filter: this.idea_filter
        }
        fetchWP.post('getconfig/', data)
            .then(
                (json) => {
                    this.idea_types = json.idea_type
                    this.ideas = json.ideas
                    this.loader = false
                })
                .catch(function(error) {
                    console.log('error', error);
                });
      },
      
      
      handleFileUpload(){
        
      }, 


      formSubmit(e=false){
        this.loader = true
        this.title = this.$refs.title.value
        this.idea_type = this.$refs.idea_type.value
        this.content = this.$refs.content.value
        this.file = this.$refs.file.files[0];

        const formData = new FormData();
        
        formData.append('title', this.title);
        formData.append('idea_type', this.idea_type);
        formData.append('content', this.content);
        

        if(typeof this.file != 'undefined'){
          formData.append('file', this.file, this.file.name);
        }

        let headers = {
          'Content-Type': 'multipart/form-data',
          'X-WP-Nonce': window.idea_object.api_nonce,
        }
        

        axios.post(window.idea_object.root + 'create/', formData, {headers: headers})
        .then(
          (response) => {
            this.title = ''
            this.content = ''
            this.file = ''
            this.loader = false
            this.show_msg = true

            setTimeout(function(){
              this.show_msg = false
             }, 500);
            
          })
          .catch(function(error){
            console.log('error: ', error)
          })

      }
    }

  }).mount('#im_form')