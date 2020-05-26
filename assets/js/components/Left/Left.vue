<template>
    <div class="col-12 px-0">
        <div class="bg-white">

            <div class="bg-gray px-4 py-2 bg-light">
                <p class="h5 mb-0 py-1">Vos conversations récente</p>
            </div>

            <div class="messages-box">
                <div class="flex overflow-auto">

                    <template v-for="(conversation, index, key) in CONVERSATIONS">
                        <Conversation :conversation="conversation" />
                    </template>


                    <p class=" px-4  flex text-muted" v-if="CONVERSATIONSEMPTY">
                        Aucune conversation
                    </p>
                    <p class=" px-4  flex text-muted" v-else-if="CONVERSATIONSLOADING">
                        Chargement...
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import Conversation from "./Conversation";

    export default {

        components: {Conversation},
        computed: {
          ...mapGetters(["CONVERSATIONS", "HUBURL", "USERNAME","MERCURETOKEN","AVATARS"])
        },
        data: ()=>{
            //let userImage = window.location.hostname+"uploads/images/users/"+this.AVATARS.filter(x => x.username === this.conversation.username)[0].image

            return {
                CONVERSATIONSLOADING: true,
                CONVERSATIONSEMPTY: false
            }
        },
        methods: {
            updateConversations(data) {
                this.$store.commit("UPDATE_CONVERSATIONS", data)
            }
        },
        mounted() {
            const vm = this;
            this.$store.dispatch("GET_CONVERSATIONS")
                .then(() => {
                    if(this.CONVERSATIONS.length ===0){
                        this.CONVERSATIONSEMPTY= true;

                    }else{

                        this.CONVERSATIONSLOADING= false;

                    }


                    let url = new URL(this.HUBURL);
                    url.searchParams.append('topic', `/conversations/${this.USERNAME}`)

                 //  console.log(this.HUBURL)
                 //  console.log(this.USERNAME)
                 //  console.log(this.MERCURETOKEN)

                    const eventSource = new EventSourcePolyfill(url, {
                        headers: {
                            'Authorization': `Bearer ${this.MERCURETOKEN}`,
                        }
                    }, {withCredentials: false});

                    eventSource.onmessage = function (event) {
                        vm.updateConversations(JSON.parse(event.data))


                    }
                    eventSource.onerror = function (event) {
                        console.log('message erreur');
                    }

                    function getCookie(cname) {
                        var name = cname + "=";
                        var decodedCookie = decodeURIComponent(document.cookie);
                        var ca = decodedCookie.split(';');
                        for(var i = 0; i <ca.length; i++) {
                            var c = ca[i];
                            while (c.charAt(0) == ' ') {
                                c = c.substring(1);
                            }
                            if (c.indexOf(name) == 0) {
                                return c.substring(name.length, c.length);
                            }
                        }
                        return "";
                    }

                    if(getCookie("lastConversationId")){
                        let id = "/conversation/"+getCookie("lastConversationId")
                        let target = $("a[href='"+id+"']")[0]
                        if(target){
                            target.click();
                        }
                    }



                })
        }
    }
</script>
