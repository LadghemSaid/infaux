<template>
    <!-- Sender Message-->
    <div class="media w-75 mb-3" :class="{'ml-auto': message.mine}">
        <img v-if="!message.mine" :src=avatar alt="user" width="50" height="50" class="rounded-circle">
        <div class="media-body ml-3">
            <div class="message-bloc" :class="[message.mine ? 'msg-bg-green' : 'msg-bg-grey']">
                <p class="text-small mb-0" :class="[message.mine ? 'text-white' : 'text-muted']">{{ message.content }}</p>
            </div>
            <p class="small text-muted">{{ date }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            message: Object,
            avatar: String
        },
        computed: {
            date() {
                return moment(this.message.createdAt).local().fromNow()
                //return new Date(this.message.createdAt).toLocaleString();
            }
        }
    }
</script>
