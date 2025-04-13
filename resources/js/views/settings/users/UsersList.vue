<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <v-row align="center">
                    <h1 class="headline font-weight-medium">Пользователи</h1>
                    <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-icon
                                v-on="on"
                                small
                                class="ml-1"
                            >
                                help
                            </v-icon>
                        </template>
                        <span>
                            
                        </span>
                    </v-tooltip>
                </v-row>
            </v-col>

            <v-col
                cols="auto"
                class="grey--text"
            >
                {{ users.meta.total }}
            </v-col>

            <v-spacer />

            <v-col cols="auto">
                <v-btn
                    :disabled="loading"
                    icon
                    title="Обновить"
                    @click="refresh"
                >
                <v-icon>refresh</v-icon>
                </v-btn>
            </v-col>
            <v-col cols="auto">
                <v-btn
                    :to="{ name: 'users.create'}"
                    color="primary"
                    :disabled="true"
                    >
                    <v-icon left>add</v-icon>
                        Добавить
                    </v-btn>
            </v-col>
        </v-row>

        <v-card
            :loading="loading"
            class="mb-3"
        >

            <!-- Необходимо для сохранения фокуса -->
            <div class="v-card__progress"></div>

            <div class="v-data-table theme--light">
                <div class="v-data-table__wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left text-no-wrap">
                                    Name
                                </th>
                                <th class="text-left text-no-wrap">
                                    Email
                                </th>
                                <th class="text-right text-no-wrap"></th>
                            </tr>
                        </thead>
                        <tbody>
                           <users-list-item 
                                v-for="(user, index) in users.data"
                                :key="index"
                                :user="user"
                                :loading="loading"
                                :refresh="refresh"
                           />
                        </tbody>
                    </table>
                </div>
            </div>
        </v-card>

        <v-row
            align="center"
            no-gutters
            >
            <v-col>
                <v-pagination
                    v-if="users.meta.last_page > 1"
                    :total-visible="7"
                    :length="users.meta.last_page"
                    :value="users.meta.current_page"
                    @input="changePage"
                />
            </v-col>
        </v-row>

        <div>
            <notifications group="foo" position="top center" class="mt-12"/>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import UsersListItem from './UsersListItem.vue';
export default {
    name: 'UsersList',

    components: {
        UsersListItem
    },

    data() {
        return {
            loading: false,
            users: {
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

            axios.get('settings/users', {params: params})
                .then((response) => {
                    this.users = response.data;
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
            this.fetchData(Object.assign({}, {page: this.users.meta.current_page}));
        },
    },
}
</script>