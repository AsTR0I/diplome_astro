export default {
    namespaced: true,

    state: {
        filters: {
            disposition: null,
            src: null,
            dst: null
        },
        currentPage: 1
    },

    mutations: {
        updateStatusFilter(state, disposition) {
            state.filters.disposition = disposition;
        },
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