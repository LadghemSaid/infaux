<template>
    <!-- Typing area -->
        <div class="input-group input-msg">
            <input type="text" placeholder="Votre message ..." v-model="content" @keyup.enter="sendMessage" aria-describedby="button-addon2" class="form-control rounded-0 border-0 py-4 bg-light">
            <div class="input-group-append">
                <a id="button-addon2"  @click="sendMessage"  class="btn btn-link"> <i class="fa fa-paper-plane"></i></a>
            </div>
        </div>
</template>

<script>
    export default {
        data: () => ({
            content: ''
        }),
        methods: {
            sendMessage() {
                if( this.content !== ''){
                    this.$store.dispatch("POST_MESSAGE", {
                        conversationId: this.$route.params.id,
                        content: this.content
                    }).
                    then(() => {
                        this.content = ''
                    })
                }

            }
        }
    }
</script>
