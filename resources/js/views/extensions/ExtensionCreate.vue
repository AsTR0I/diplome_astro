<template>
    <div>
      <v-row align="center">
        <v-col cols="auto">
          <v-btn
            :to="{ name: 'extensions' }"
            title="Перейти к списку"
            exact
            icon
          >
            <v-icon>arrow_back</v-icon>
          </v-btn>
        </v-col>
  
        <v-col class="text-truncate">
          <h1 class="headline font-weight-medium text-truncate">
            Новый extension
          </h1>
        </v-col>

  
        <v-col cols="auto">
          <v-btn
            :loading="loading"
            color="primary"
            @click="checkTemplate('commit')"
          >
            <v-icon left>arrow_back</v-icon>
            Сохр. и выйти
          </v-btn>
  
          <v-btn
            :loading="loading"
            color="primary"
            @click="checkTemplate('continue')"
          >
            <v-icon left>save</v-icon>
            Сохранить
          </v-btn>
        </v-col>
      </v-row>

      <v-form ref="extensionForm">
        <v-row>
          <v-col cols="12" sm="6">
            <v-card class="mb-4" :loading="loading">
              <v-card-text>
                <v-text-field
                  v-model="extension.context"
                  label="Context"
                  autofocus
                  :rules="rules.destination"
                />
                <v-text-field
                  v-model="extension.exten"
                  label="Exten"
                  autofocus
                  :rules="rules.exten"
                />
                <v-text-field
                  v-model="extension.priority"
                  label="Priority"
                  autofocus
                  :rules="rules.priority"
                />
                <v-select 
                  v-model="extension.app"
                  :items="actions"
                  item-text="text" 
                  item-value="value"
                  label="Select Action"
                  required
                  :rules="rules.app"
                />
                <v-text-field
                  v-model="extension.appdata"
                  label="Appdata"
                  autofocus
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
    name: 'ExtensionCreate',

    data() {
      return {
        loading: false,
        extension: {},
        actions: [
            { text: 'Dial', value: 'Dial' },
            { text: 'Answer', value: 'Answer' },
            { text: 'Hangup', value: 'Hangup' },
            { text: 'Wait', value: 'Wait' },
            { text: 'MixMonitor', value: 'MixMonitor'},
            { text: 'Playback', value: 'Playback' },
            { text: 'Background', value: 'Background' },
            { text: 'Goto', value: 'Goto' },
            { text: 'NoOp', value: 'NoOp' },
        ],
        rules
      }
    },

    methods: {
        checkTemplate(nextAction) {

          if (!this.$refs.extensionForm.validate()) {
            return;
          }

          this.loading = true;
          window.axios.post(`/extensions`, this.extension)
            .then((response) => {

              this.$notify({
                group: 'foo',
                title: 'Сохранено',
                text: 'Extension сохранен.',
                type: 'success',
                position: 'top center'
              });

              this.extension = response.data.data;

              if(nextAction === 'commit') {
                this.$router.push({ name: 'extensions' });
              } else {
                this.$router.push({ name: 'extension.edit', params: { id: this.extension.id } });
              }
            })
            .catch((error) => {
              this.$notify({
                group: 'foo',
                title: 'Ошибка',
                text: 'Ошибка при сохранении данных.',
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