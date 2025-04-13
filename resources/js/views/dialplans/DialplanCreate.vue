<template>
    <div>
      <v-row align="center">
        <v-col cols="auto">
          <v-btn
            :to="{ name: 'dialplans' }"
            title="Перейти к списку"
            exact
            icon
          >
            <v-icon>arrow_back</v-icon>
          </v-btn>
        </v-col>
  
        <v-col class="text-truncate">
          <h1 class="headline font-weight-medium text-truncate">
            Новый диалплан
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

      <v-form ref="dialplanForm">
        <v-row>
          <v-col cols="12" sm="6">
            <v-card class="mb-4" :loading="loading">
              <v-card-text>
                <v-text-field
                  v-model="dialplan.destination"
                  label="Destination"
                  autofocus
                  :rules="rules.destination"
                />
                <v-text-field
                  v-model="dialplan.context"
                  label="Context"
                  autofocus
                  :rules="rules.context"
                />
                <v-text-field
                  v-model="dialplan.priority"
                  label="Priority"
                  autofocus
                  :rules="rules.priority"
                />
                <v-select 
                  v-model="dialplan.action"
                  :items="actions"
                  item-text="text" 
                  item-value="value"
                  label="Select Action"
                  required
                  :rules="rules.action"
                />
                <v-text-field
                  v-model="dialplan.params"
                  label="Params"
                  autofocus
                  :rules="rules.params"
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
    name: 'DialplanCreate',

    data() {
      return {
        loading: false,
        dialplan: {},
        actions: [
          { text: 'Dial', value: 'Dial' },
          { text: 'Answer', value: 'Answer' },
          { text: 'Hangup', value: 'Hangup' },
          { text: 'Wait', value: 'Wait' },
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

          if (!this.$refs.dialplanForm.validate()) {
            return;
          }

          this.loading = true;
          window.axios.post(`/dialplans`, this.dialplan)
            .then((response) => {

              this.$notify({
                group: 'foo',
                title: 'Сохранено',
                text: 'Диалплан сохранен.',
                type: 'success',
                position: 'top center'
              });

              this.dialplan = response.data.data;

              if(nextAction === 'commit') {
                this.$router.push({ name: 'dialplans' });
              } else {
                this.$router.push({ name: 'dialplan.edit', params: { id: this.dialplan.id } });
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