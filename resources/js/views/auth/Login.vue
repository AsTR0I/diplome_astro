<template>
  <v-container>
    <v-row justify="center" align="center" style="min-height: 100vh;">
      <v-col cols="12" md="6">
        <v-card :loading="loading" class="mb-4">
          <v-card-title>
            <span class="headline">Login</span>
          </v-card-title>
          <v-card-text>
            <v-form ref="form" v-model="valid" @submit.prevent="handleLogin">
              <v-row class="my-0">
                <v-col
                  cols="12"
                  class="py-0"
                >
                  <v-text-field
                    v-model="email"
                    label="Email"
                    type="email"
                    :rules="rules.email"
                    required
                  />
                  <v-text-field
                    v-model="password"
                    label="Password"
                    type="password"
                    :rules="rules.password"
                    required
                  />
                </v-col>
              </v-row>
            
              <v-btn 
                :disabled="loading" 
                depressed
                color="primary" 
                type="submit">
                Login
              </v-btn>
              <router-link :to="{ name: 'register' }">Нет аккаунта?</router-link>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import rules from './rules_validator';

import { createNamespacedHelpers } from 'vuex';
const { mapActions, mapGetters } = createNamespacedHelpers('auth');

export default {

  data() {
    return {
      loading: false,
      valid: false,
      email: null,
      password: null,
      rules
    };
  },

  computed: {
    ...mapGetters(['isAuthenticated']),
  },
  created() {
    if (this.isAuthenticated) {
      this.$router.push({ name: 'dashboard' });
    }
  },

  methods: {
    ...mapActions(['login']), // Подключаем действие login

    handleLogin() {
      this.loading = true;
      const formValid = this.$refs.form.validate();
      if (!formValid) {
        this.loading = false;
        return;
      }

      this.login({
        email: this.email,
        password: this.password,
      })
      .then((response) => {
        this.loading = false;
        this.$router.push({ name: 'dashboard' });
      })
      .catch((error) => {
        this.loading = false;
        alert('Login failed: ' + error.message);
      });
    },
  },
};
</script>