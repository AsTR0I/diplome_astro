export default {
  namespaced: true,
  state: {
    user: null,
    token: localStorage.getItem('token') || null,
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setToken(state, token) {
      state.token = token;
      localStorage.setItem('token', token);
    },
  },
  actions: {
    login({ commit }, credentials) {
      return axios.post('login', credentials)
        .then(response => {
          commit('setUser', response.data.user);
          commit('setToken', response.data.token);
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        });
    },
    logout({ commit }) {
      return axios.get('logout')
        .then(response => {
          commit('setUser', null);
          commit('setToken', null);
          localStorage.removeItem('token');
          delete axios.defaults.headers.common['Authorization'];
        })
    },
    register({ commit }, credentials) {
      return axios.post('register', credentials)
        .then(response => {
          commit('setUser', response.data.user);
          commit('setToken', response.data.token);
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;

        });
    },
  },
  getters: {
    isAuthenticated(state) {
      return !!state.token;
    },
    user(state) {
      return state.user;
    },
  },
};
