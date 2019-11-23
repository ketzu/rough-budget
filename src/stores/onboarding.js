export default {
    state: {
        seen_stats: false,
        seen_book: false,
        seen_time : false,
        seen_log: false
    },
    getters: {
        seen_book : state => state.seen_book,
        seen_stats: state => state.seen_stats,
        seen_time : state => state.seen_time,
        seen_log  : state => state.seen_log
    },
    mutations: {
        setVisited(state, value) {
            switch (value) {
                case "bookkeeping": state.seen_book = true;
                    break;
                case "time": state.seen_time = true;
                    break;
                case "log": state.seen_log = true;
                    break;
                case "statistics": state.seen_stats = true;
                    break;
            }
        }
    },
    actions: {
        setVisited({commit}, value) {
            commit('setVisited', value);
        }
    }
};
