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
                    <p class=" px-4  flex text-muted" v-if="CONVERSATIONS.length === 0">
                        Aucune conversation
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
          ...mapGetters(["CONVERSATIONS", "HUBURL", "USERNAME","MERCURETOKEN"])
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
                        console.log('message recu');
                        vm.updateConversations(JSON.parse(event.data))
                    }
                    eventSource.onerror = function (event) {
                        console.log('message erreur');
                    }

                })
        }
    }
</script>
