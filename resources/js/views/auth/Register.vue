<template>
  <v-container>
    <v-row justify="center" align="center" style="min-height: 100vh;">
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>
            <span class="headline">Register</span>
          </v-card-title>
          <v-card-text>
            <v-form v-model="valid" ref="form" @submit.prevent="registerHandler">
              <v-row>
                <v-col>
                  <v-text-field
                  v-model="name"
                  label="Name"
                  :rules="rules.name"
                  required
                  />
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
                color="primary" 
                type="submit"
                depressed
                >
                Register
              </v-btn>
              <router-link :to="{ name: 'login' }">Уже есть аккаунт?</router-link>

            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios';
import rules from './rules_validator';

import { createNamespacedHelpers } from 'vuex';
const { mapActions, mapGetters } = createNamespacedHelpers('auth');

export default {
  data() {
    return {
      loading: false,
      valid: false,
      name: null,
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
    ...mapActions(['register']),

      registerHandler() {
        this.loading = true;
        const formValid = this.$refs.form.validate();
        if (!formValid) {
          this.loading = false;
          return;
        }

        // Вызываем register из Vuex для отправки данных
        this.register({
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password,
        })
        .then(() => {
          // После успешной регистрации перенаправляем на страницу входа
          this.$router.push({ name: 'login' });
        })
        .catch((error) => {
          this.loading = false;
          // Показываем ошибку
          alert('Registration failed: ' + (error.response ? error.response.data.message : error.message));
        })
        .finally(() => {
          this.loading = false;
        });
    }
  },
};
</script>
