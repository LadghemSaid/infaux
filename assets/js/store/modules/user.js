export default {
    state: {
        username: null,
        mercuretoken: null
    },
    getters: {
        USERNAME: state => state.username,
        MERCURETOKEN: state => state.mercuretoken
    },
    mutations: {
        SET_USERNAME: (state, payload) => state.username = payload,
        SET_MERCURETOKEN: (state, payload) => state.mercuretoken = payload
    },
    actions: {}
}

