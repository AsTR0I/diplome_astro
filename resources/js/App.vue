<template>
  <v-app class="theme--cocobri">
    <v-navigation-drawer app floating :clipped="$vuetify.breakpoint.lgAndUp" v-model="drawer" v-if="isAuthenticated">
      <v-list nav dense>
        <template v-for="(item, index) in items">
          <!-- group -->
          <v-list-group no-action :prepend-icon="item.icon" :key="index" v-model="item.expanded" v-if="item.children">
            <template v-slot:activator>
              <v-list-item-content>
                <v-list-item-title>{{ item.title }}</v-list-item-title>
              </v-list-item-content>
            </template>

            <template v-for="(item, index) in item.children">

              <v-list-item
                link
                 :key="index"
                :to="item.route"
              >
                <v-list-item-content>
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </template>
          </v-list-group>

          <!-- single item -->
          <template v-else>
            <v-list-item
              link
              :key="index"
              :to="item.route"
              :exact="item.route.name === 'dashboard'"
            >
              <v-list-item-icon>
                <v-icon v-if="item.icon">
                  {{ item.icon }}
                </v-icon>
                <span v-else-if="item.iconAlt" v-html="item.iconAlt"
                  class="v-icon notranslate material-icons theme--light" />
              </v-list-item-icon>

              <v-list-item-content>
                <v-list-item-title>{{ item.title }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </template>
        </template>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar app dark color="primary" :clipped-left="$vuetify.breakpoint.lgAndUp">
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" v-if="isAuthenticated"/>

      <v-toolbar-title>
        <router-link class="hidden-sm-and-down white--text" style="text-decoration: none" :to="{ name: 'dashboard' }">
          ASTRO ATC
        </router-link>
      </v-toolbar-title>

      <v-spacer />

      <v-spacer />

      <form v-if="isAuthenticated">
        <v-btn icon @click="logoutHandler">
          <v-icon>mdi-exit-to-app</v-icon>
        </v-btn>
      </form>
    </v-app-bar>

    <v-main>
      <v-container fluid>
        <v-slide-x-reverse-transition hide-on-leave>
          <router-view />
        </v-slide-x-reverse-transition>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import { createNamespacedHelpers } from 'vuex';
const { mapGetters, mapActions } = createNamespacedHelpers('auth');

window.axios.interceptors.response.use(response => response, error => {
  if (error.response && [401, 419].includes(error.response.status)) {
    this.$router.push({ name: 'login' });
  }
  return Promise.reject(error);
});

export default {
  name: 'App',

  data() {
    return {
      loading: false,
      drawer: null,
      items: [{
          icon: 'mdi-home',
          title: 'Главная',
          route: { name: 'dashboard' }
        }, {
          icon: 'mdi-phone-clock',
          title: 'История звонков',
          route: { name: 'calls' }
        },
      ]
    }
  },

  computed: {
    // Получаем статус авторизации из Vuex
    ...mapGetters(['isAuthenticated']),
  },

  methods: {
    ...mapActions(['logout']),
    logoutHandler() {
      this.loading = true;
      this.logout()
        .then(() => {
          this.$router.push({name: 'login'});
        })
        .finally(() => {
          this.loading = false;
        })
    }
  }
};
</script>
