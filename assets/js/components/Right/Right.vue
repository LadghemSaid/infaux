<template>
    <div class="col-12 px-0">
        <div class="px-4 py-5 chat-box bg-white" ref="messagesBody">


            <p class=" px-4  flex text-muted" v-if="!MESSAGES">
                Chargement...
            </p>
            <p class=" px-4  flex text-muted" v-else-if="MESSAGES && MESSAGES.length === 0">
                Aucun message
            </p>
            <template v-else>
                <template v-for="(message, index, key) in MESSAGES">
                    <Message :avatar="CONVERSATION.image"  :message="message"/>
                </template>
            </template>

        </div>

        <Input/>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import Message from "./Message";
    import Input from "./Input";


    export default {
        data: () => ({
            eventSource: null,
        }),
        components: {Message, Input},
        computed: {
            ...mapGetters(["HUBURL", "MERCURETOKEN",]),
            MESSAGES() {
                return this.$store.getters.MESSAGES(this.$route.params.id);
            }   ,
            CONVERSATION() {
                return this.$store.getters.CONVERSATION(this.$route.params.id);
            }
        },
        methods: {
            scrollDown() {
                this.$refs.messagesBody.scrollTop = this.$refs.messagesBody.scrollHeight;
            },
            addMessage(data) {
                this.$store.commit("ADD_MESSAGE", {
                    conversationId: this.$route.params.id,
                    payload: data
                })
            }
        },
        mounted() {

            console.log(this.CONVERSATION)

            const vm = this;
            this.$store.dispatch("GET_MESSAGES", this.$route.params.id)
                .then(() => {

                    function setCookie(cname, cvalue, exdays) {
                        var d = new Date();
                        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                        var expires = "expires=" + d.toUTCString();
                        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/chat";
                    }
                    document.cookie = "lastConversationId=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/chat;";
                    setCookie('lastConversationId',  this.$route.params.id, 365)

                    this.scrollDown();
                    if (this.eventSource === null) {
                        let url = new URL(this.HUBURL);
                        url.searchParams.append('topic', `/conversations/${this.$route.params.id}`)

                        const eventSource = new EventSourcePolyfill(url, {
                            headers: {
                                'Authorization': `Bearer ${this.MERCURETOKEN}`,
                            }
                        }, {withCredentials: false});

                        eventSource.onmessage = function (event) {
                            vm.addMessage(JSON.parse(event.data))
                        }
                        eventSource.onerror = function (event) {
                            console.log('message erreur');
                        }
                    }

                })
        },
        watch: {
            MESSAGES: function (val) {
                this.$nextTick(() => {
                    this.scrollDown();
                })
            }
        },
        beforeDestroy() {
            if (this.eventSource instanceof EventSource) {
                this.eventSource.close();
            }
        }
    }
</script>
