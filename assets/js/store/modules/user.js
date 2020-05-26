export default {
    state: {
        username: null,
        mercuretoken: null,
        avatars:null,
    },
    getters: {
        USERNAME: state => state.username,
        AVATARS: state => state.avatars,
        MERCURETOKEN: state => state.mercuretoken
    },
    mutations: {
        SET_USERNAME: (state, payload) => state.username = payload,
        SET_AVATARS: (state, payload) => state.avatars = payload,
        SET_MERCURETOKEN: (state, payload) => state.mercuretoken = payload
    },
    actions: {

    }
}

