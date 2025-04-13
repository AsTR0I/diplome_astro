<template>
  <v-app class="theme--cocobri">

   <v-navigation-drawer app floating :clipped="$vuetify.breakpoint.lgAndUp" v-model="drawer">
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
                v-if="!item.permission || profilePermissions.includes(item.permission)"
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
              v-if="!item.permission || profilePermissions.includes(item.permission)"
            >
              <v-list-item-icon class="icon-24">
                <v-icon v-if="item.icon" class="icon-24">
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

    <v-app-bar
      app
      dark
      color="primary"
      :clipped-left="$vuetify.breakpoint.lgAndUp"
    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />

      <v-toolbar-title>
        <router-link
          class="hidden-sm-and-down white--text"
          style="text-decoration: none"
          :to="{ name: 'dashboard' }"
        >
          {{ profile.name }}
        </router-link>
      </v-toolbar-title>

      <v-spacer />

      <!-- support -->
      <div>
        <v-list-item two-line>
          <v-list-item-avatar>
            <v-icon class="lighten-1 blue white--text">
              call
            </v-icon>
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-subtitle>Поддержка 24/7</v-list-item-subtitle>
            <v-list-item-title>8 800 800 00 00</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </div>

      <form method="post" action="/logout">
        <input
          type="hidden"
          name="_token"
          :value="csrtToken"
        >

        <v-btn
          icon
          type="submit"
        >
          <v-icon>exit_to_app</v-icon>
        </v-btn>
      </form>
    </v-app-bar>

    <v-main>
      <v-container fluid>
          <router-view />
      </v-container>
    </v-main>

    <notifications position="bottom center" />
  </v-app>
</template>

<script>
// Add a response interceptor
window.axios.interceptors.response.use(response => response, error => {
  if (error.response && [401, 419].includes(error.response.status)) {
    window.location.replace('/login');
  }
  return Promise.reject(error);
});

export default {
  name: 'App',

  data() {
    return {
      drawer: null,
      profile: window.bootstrap.profile,
      items: [{
        icon: 'home',
        title: 'Главная',
        route: { name: 'dashboard'}
      }, {
          icon: 'history',
          title: 'История звонков',
          route: { name: 'calls' }
        }, {
          icon: 'group',
          title: 'SIP пиры',
          route: { name: 'sippeers' }
        }, {
          icon: 'route',
          title: 'Диалплан',
          route: { name: 'dialplans' }
        }, {
          icon: 'extension',
          title: 'Расширения',
          route: { name: 'extensions' }
        }, {
          icon: 'computer',
          title: 'Система',
          route: { name: 'system' }
        }, {
          icon: 'settings',
          title: 'Настройки',
          children: [
            {
              icon: 'users',
              title: 'Пользователи',
              route: { name: 'settings.users'}
            }, {
              icon: 'configs',
              title: 'Конфигурационные файлы',
              route: { name: 'settings.configs'}
            }
          ]
        }],
      csrtToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
}
</script>

<style scoped>
.icon-24 {
  min-width: 32px !important; /* стандарт, можно 24 + немного отступа */
  width: 32px !important;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-24 .v-icon {
  font-size: 24px !important;
  height: 24px !important;
  width: 24px !important;
  line-height: 24px !important;
}
</style>
