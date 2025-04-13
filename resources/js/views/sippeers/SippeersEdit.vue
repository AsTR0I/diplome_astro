<template>
    <div>
      <v-row align="center">
        <v-col cols="auto">
          <v-btn
            :to="{ name: 'sippeers' }"
            title="Перейти к списку"
            exact
            icon
          >
            <v-icon>arrow_back</v-icon>
          </v-btn>
        </v-col>
  
        <v-col class="text-truncate">
          <h1 class="headline font-weight-medium text-truncate">
            Редактирование Sippeer
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

      <v-form ref="sippeerForm">
        <v-row>
          <v-col cols="12" sm="6">
            <v-card class="mb-4" :loading="loading">
              <v-card-text>
                <v-text-field
                  v-model="sippeer.name"
                  label="Name"
                  autofocus
                  :rules="rules.name"
                />
                <v-select 
                  v-model="sippeer.type"
                  :items="sippeersTypesItems"
                  item-text="text" 
                  item-value="value"
                  label="Select type"
                  required
                  :rules="rules.type"
                />
                <v-text-field
                  v-model="sippeer.secret"
                  label="Secret"
                  autofocus
                  :rules="rules.secret"
                />
                <v-select 
                  v-model="sippeer.host"
                  :items="hostModeItems"
                  item-text="text" 
                  item-value="value"
                  label="Select host mode"
                  required
                  :rules="rules.host"
                />
                <v-text-field v-if="sippeer.host != 'dynamic'"
                  v-model="sippeer.ipaddr"
                  label="Ip address"
                  autofocus
                  :rules="rules.ip_addr"
                />
                <v-select 
                  v-model="sippeer.context"
                  :items="contextsItems"
                  item-text="text" 
                  item-value="value"
                  label="Select context mode"
                  required
                  :rules="rules.context"
                />
                <v-select 
                  v-model="sippeer.nat"
                  :items="natModeItems"
                  item-text="text" 
                  item-value="value"
                  label="Select nat mode"
                  required
                  :rules="rules.nat"
                />
                <v-select 
                  v-model="sippeer.directmedia"
                  :items="directmediaModeItems"
                  item-text="text" 
                  item-value="value"
                  label="Select directmedia mode"
                  required
                  :rules="rules.directmedia"
                />
                <v-select 
                  v-model="sippeer.allow"
                  :items="codecOptionsItems"
                  item-text="text" 
                  item-value="value"
                  label="Select allow mode"
                  required
                  :rules="rules.allow"
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
import axios from 'axios';
import rules from './rules_validator';

export default {
    name: 'SippeersEdit',

    data() {
      return {
        loading: false,
        sippeer: {
          name: null,
          type: null,
          secret: null,
          host: null,
          context: null,
          nat: null,
          directmedia: null,
          allow: null,
          ipaddr: null
        },
        rules,
        sippeersTypesItems: [
          { text: 'Friend', value: 'friend' },
          { text: 'User', value: 'user' },
          { text: 'Peer', value: 'peer' },
        ],
        natModeItems: [
          { text: 'Yes', value: 'yes' },
          { text: 'No', value: 'no' },
        ],
        contextsItems: [],
        hostModeItems: [
          { text: 'Dynamic', value: 'dynamic' },
          { text: 'Ip', value: 'ip' },
          // { text: 'Domain', value: 'domain'}
        ],
        directmediaModeItems: [
          { text: 'Yes', value: 'yes' },
          { text: 'No', value: 'no' },
          // { text: 'Domain', value: 'domain'}
        ],
        codecOptionsItems: [
          { text: 'ulaw (G.711 µ-law)', value: 'ulaw' },
          { text: 'alaw (G.711 A-law)', value: 'alaw' },
          { text: 'GSM', value: 'gsm' },
          { text: 'G.729', value: 'g729' },
          { text: 'Opus', value: 'opus' }
        ]
      }
    },

    created() {
      this.fetchContexts();
      this.fetchData();
    },

    methods: {
        checkTemplate(nextAction) {

          if (!this.$refs.sippeerForm.validate()) {
            return;
          }

          this.loading = true;
          window.axios.post(`/sippeers/${this.$route.params.id}`, this.sippeer)
            .then((response) => {

              this.$notify({
                group: 'foo',
                title: 'Сохранено',
                text: 'Sippeer сохранен.',
                type: 'success',
                position: 'top center'
              });

              this.sippeer = response.data.data;

              if(nextAction === 'commit') {
                this.$router.push({ name: 'sippeers' });
              } else {
                this.$router.push({ name: 'sippeers.edit', params: { id: this.sippeer.id } });
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
        },

        fetchData() {
          this.loading = true;
          window.axios.get(`/sippeers/${this.$route.params.id}`)
            .then((response) => {
              this.sippeer = response.data.data;
            })
            .catch((error) => {
              this.$notify({
                group: 'foo',
                title: 'Ошибка',
                text: 'Ошибка при загрузке данных.',
                type: 'error',
                position: 'top center'
              });
            })
            .finally(() => {
              this.loading = false;
            });
        },

        fetchContexts() {
          this.loading = true;

          axios.get('extensions/contexts')
            .then((response) => {
              this.contextsItems = response.data.data;
            })
            .catch((error) => {
              this.$notify({
                group: 'foo',
                title: 'Ошибка',
                text: 'Ошибка при получении contexts.',
                type: 'error',
                position: 'top center'
              });
            })
            .finally(() => {
              this.loading = false;
            })
        }
    }
}
</script>