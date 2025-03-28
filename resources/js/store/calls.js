export default {
    namespaced: true,

    state: {
        filters: {
            src: null,
            dst: null
        },
        currentPage: 1
    },

    mutations: {
        updateSrcFilter(state, src) {
            state.filters.src = src;
        },
        updateDstFilter(state, dst) {
            state.filters.dst = dst;
        },
        updateCurrentPage(state, page) {
            state.currentPage = page;
        }
    },
}