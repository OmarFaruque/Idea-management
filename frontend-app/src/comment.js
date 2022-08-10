import { createApp } from 'vue/dist/vue.esm-bundler'
import style from './comment.scss'
import FetchWP from './utils/fetchWP'

let fetchWP = new FetchWP({
    restURL: window.idea_object.root,
    restNonce: window.idea_object.api_nonce,
  });

  
createApp({
    data() {
      return {
        style: style, 
        list_url: window.idea_object.homepage + window.idea_object.cpost, 
        comments: [], 
        comment: '', 
        user_vote_status: false, 
        p_vote_id: false, 
        loader: false, 
        comment_filter: 'recent'
      }
    },

    created() {
      this.fetchData()
      if(!window.idea_object.user_login){
        window.location.replace(window.idea_object.homepage);
      }
    }, 
    methods: {
      fetchData() {
        this.loader = true
        let data = {
            post_id: window.idea_object.post_id, 
            comment_filter: this.comment_filter
        }
        fetchWP.post('getcomments/', data)
            .then(
                (json) => {
                    console.log('json comments: ', json)
                    this.comments = json.comments
                    this.user_vote_status = json.user_vote_status
                    this.p_vote_id = json.p_vote_id
                    this.loader = false
                })
                .catch(function(error) {
                    console.log('error', error);
                });
      },
      commentFilter(){
        this.comment_filter = this.$refs.comment_filter.value
        this.fetchData()
      },
      
      submitComment(){
        console.log('submit comment is')
      }, 

      backtolisting(){
        console.log('back to listing');
      },

      voteprocess(id, v_type){
        this.loader = true
        let data = {
          comment_id: id, 
          post_id: window.idea_object.post_id, 
          v_type: v_type
        }

        fetchWP.post('comment_vote/', data)
            .then(
                (json) => { 
                  this.comments = json.comments
                  this.user_vote_status = json.user_vote_status
                  this.p_vote_id = json.p_vote_id
                  this.loader = false
                    
                })
                .catch(function(error) {
                    console.log('error', error);
                });

      },

      formSubmit(e=false){
        this.loader = true
        this.comment = this.$refs.comment.value
        let data = {
          comment: this.comment, 
          post_id: window.idea_object.post_id
        }

        
        fetchWP.post('postcomment/', data)
            .then(
                (json) => { 
                    this.comments = json.comments
                    this.loader = false
                })
                .catch(function(error) {
                    console.log('error', error);
                });
      }

      
    }

  }).mount('#comments')