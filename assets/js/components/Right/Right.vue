<template>
    <div class="col-12 px-0">
        <div class="px-4 py-5 chat-box bg-white" ref="messagesBody">
            <template v-for="(message, index, key) in MESSAGES">
                <Message :message="message"/>
            </template>

            <p class=" px-4  flex text-muted" v-if="!MESSAGES">
                Chargement...
            </p>
            <p class=" px-4  flex text-muted" v-if="MESSAGES && MESSAGES.length === 0">
                Aucun message
            </p>
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
            eventSource: null
        }),
        components: {Message, Input},
        computed: {
            ...mapGetters(["HUBURL","MERCURETOKEN"]),
            MESSAGES() {
                return this.$store.getters.MESSAGES(this.$route.params.id);
            }
        },
        methods: {

            addMessage(data) {
                this.$store.commit("ADD_MESSAGE", {
                    conversationId: this.$route.params.id,
                    payload: data
                })
            }
        },
        mounted() {
            const vm = this;
            this.$store.dispatch("GET_MESSAGES", this.$route.params.id)
                .then(() => {
                    if (this.eventSource === null) {
                        let url = new URL(this.HUBURL);
                        url.searchParams.append('topic', `/conversations/${this.$route.params.id}`)

                        const eventSource = new EventSourcePolyfill(url, {
                            headers: {
                                'Authorization': `Bearer ${this.MERCURETOKEN}`,
                            }
                        }, {withCredentials: false});

                        eventSource.onmessage = function (event) {
                            console.log('test');
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

            }
        },
        beforeDestroy() {
            if (this.eventSource instanceof EventSource) {
                this.eventSource.close();
            }
        }
    }
</script>
