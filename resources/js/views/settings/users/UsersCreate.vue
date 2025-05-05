<template>
    <div>
      <v-row align="center">
        <v-col cols="auto">
          <v-btn
            :to="{ name: 'settings.users' }"
            title="Перейти к списку"
            exact
            icon
          >
            <v-icon>arrow_back</v-icon>
          </v-btn>
        </v-col>
  
        <v-col class="text-truncate">
          <h1 class="headline font-weight-medium text-truncate">
            Зарегестрировать нового пользователя
          </h1>
        </v-col>

  
        <v-col cols="auto">
          <v-btn
            :loading="loading"
            color="primary"
            @click="checkTemplate('commit')"
          >
            <v-icon left>arrow_back</v-icon>
            Создать
          </v-btn>
  
          <!-- <v-btn
            :loading="loading"
            color="primary"
            @click="checkTemplate('continue')"
          >
            <v-icon left>save</v-icon>
            Сохранить
          </v-btn> -->
        </v-col>
      </v-row>

      <v-form ref="usersForm">
        <v-row>
          <v-col cols="12" sm="6">
            <v-card class="mb-4" :loading="loading">
              <v-card-text>
                <v-text-field
                  v-model="user.name"
                  label="Name"
                  autofocus
                  :rules="rules.name"
                />
                <v-text-field
                  v-model="user.email"
                  label="Email"
                  :rules="rules.email"
                />
                <v-text-field
                  v-model="user.password"
                  label="Password"
                  :rules="rules.password"
                />
                <v-text-field
                  v-model="user.password_confirmation"
                  label="Password confirmation"
                  :rules="rules.password_confirmation"
                />
              </v-card-text>
            </v-card>
        </v-col>
        </v-row>
      </v-form>
      <div>
        <notifications group="foo" position="top center" class="mt-12"/>
      </div>
    </div>
</template>

<script>
import rules from './rules_validator';
export default {
    name: 'UsersCreate',

    data() {
      return {
        loading: false,
        user: {
          name: null,
          email: null,
          password: null,
          password_confirmation: null
        },
        rules
      }
    },

    methods: {
        checkTemplate(nextAction) {

          if (!this.$refs.usersForm.validate()) {
            return;
          }

          this.loading = true;
          window.axios.post(`register`, this.user)
            .then((response) => {

              this.$notify({
                group: 'foo',
                title: 'Сохранено',
                text: 'User сохранен.',
                type: 'success',
                position: 'top center'
              });

              this.user = response.data.data;

              if(nextAction === 'commit') {
                this.$router.push({ name: 'settings.users' });
              } else {
                this.$router.push({ name: 'settings.users.edit', params: { id: this.user.id } });
              }
            })
            .catch((error) => {
              this.$notify({
                group: 'foo',
                title: 'Ошибка',
                text: error.response.data.message || 'Ошибка при сохранении данных.',
                type: 'error',
                position: 'top center'
              });
            })
            .finally(() => {
              this.loading = false;
            });
        }
    }
}
</script>