<template>
    <v-card
       class="pb-2"
       :loading="loading"
   >
       <v-card-title>
           <v-row no-gutters align="center">
               <v-col>
                   Панель исполнения команд
               </v-col>

               <v-col cols="auto" class="ml-4">
                   <v-btn
                       icon
                       title="Обновить"
                       @click="refresh"
                   >
                       <v-icon>refresh</v-icon>
                   </v-btn>
               </v-col>
           </v-row>
       </v-card-title>
       <v-card-content>
        <div class="v-data-table theme--light">
                <div class="v-data-table__wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left text-no-wrap">
                                    Name
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon
                                            v-on="on"
                                            small
                                        >
                                            help
                                        </v-icon>
                                        </template>
                                        <span>
                                            
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Command
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon
                                            v-on="on"
                                            small
                                        >
                                            help
                                        </v-icon>
                                        </template>
                                        <span>
                                            
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-right text-no-wrap"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <asterisk-commands-list-item 
                                v-for="(command, index) in commands.data"
                                :key="command.id + `_${index}`"
                                :command="command"
                                :loading="loading"
                                :refresh="refresh"
                            />
                        </tbody>
                    </table>
                </div>
            </div>
        <v-row
            align="center"
            no-gutters
            >
            <v-col>
                <v-pagination
                    v-if="commands.meta.last_page > 1"
                    :total-visible="7"
                    :length="commands.meta.last_page"
                    :value="commands.meta.current_page"
                    @input="changePage"
                />
            </v-col>
        </v-row>
       </v-card-content>
       <div>
           <notifications group="foo" position="bottom center" class="mt-6"/>
       </div>
   </v-card>
</template>

<script>
import axios from 'axios';
import AsteriskCommandsListItem from './AsteriskCommandsListItem.vue'
export default {
    name: 'AsteriskCommandsList',

    components: {
        AsteriskCommandsListItem
    },

    data() {
        return {
            loading: false,
            commands: {
                data: [],
                links: {
                    first: null,
                    last: null,
                    next: null,
                    prev: null
                },
                meta: {
                    current_page: 1,
                    from: 1,
                    last_page: 1,
                    path: null,
                    per_page: 40,
                    to: 0,
                    total: 0
                }
            },
        }
    },

    created() {
        this.fetchData(Object.assign({},{page: 1}));
    },

    methods: {
        fetchData(params) {
            this.loading = true;

            axios.get('asterisk-commands', {params: params})
                .then((response) => {
                    this.commands = response.data;
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
        changePage(page) {
            this.fetchData(Object.assign({},{page: page}));
            this.updateCurrentPage(page);
            this.$vuetify.goTo('#app');
        },
        refresh() {
            this.fetchData(Object.assign({}, {page: this.commands.meta.current_page}));
        },
    },
}
</script>
