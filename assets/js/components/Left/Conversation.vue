<template>
    <router-link :to="{name: 'conversation', params: {id: conversation.conversationId}}"
                 class="list-group-item list-group-item-action rounded-0">
        <div class="media">
            <img src="https://res.cloudinary.com/mhmd/image/upload/v1564960395/avatar_usae7z.svg" alt="user" width="50"
                 class="rounded-circle">
            <div class="media-body ml-4">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <h6 class="mb-0">{{ conversation.username }}</h6><small class="small font-weight-bold">{{ date
                    }}</small>
                    <p class="icon-close btn btn-danger" @click="deleteConv"></p>

                </div>
                <p class="font-italic mb-0 text-small">{{ conversation.content }}</p>
            </div>
        </div>
    </router-link>
</template>

<script>
    import axios from 'axios';
    import Toastify from "toastify-js";
    import {mapGetters} from "vuex";

    export default {
        props: {
            conversation: Object
        },

        computed: {
            ...mapGetters(["CONVERSATIONS"]),

            date() {
                return new Date(this.conversation.createdAt).toLocaleTimeString();
            }
        },
        methods: {


            deleteConversation(data) {
                this.$store.commit("DELETE_CONVERSATION", data)
            },

            deleteConv: async function () {
                const action = "/conversations/delete/" + this.conversation.username;
                try {
                    const response = await axios.post(action);
                    if (response.data === -1) {
                        Toastify({
                            text: "Conversation supprimé",
                            duration: 3000,
                            close: true,
                            gravity: "top", // `top` or `bottom`
                            position: 'left', // `left`, `center` or `right`
                            stopOnFocus: true, // Prevents dismissing of toast on hover
                            className: "info",
                            onClick: function () {
                            } // Callback after click
                        }).showToast();
                    } else {
                        Toastify({
                            text: "Une erreur est survenue",
                            duration: 3000,
                            close: true,
                            gravity: "top", // `top` or `bottom`
                            position: 'left', // `left`, `center` or `right`
                            stopOnFocus: true, // Prevents dismissing of toast on hover
                            className: "info",
                            onClick: function () {
                            } // Callback after click
                        }).showToast();
                    }
                } catch (error) {
                    console.error(error);
                }
                this.deleteConversation(this.conversation)

            }
        }
    }
</script>
