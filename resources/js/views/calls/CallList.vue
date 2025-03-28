<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <h1 class="headline font-weight-medium">История звонков</h1>
            </v-col>

            <v-col
                cols="auto"
                class="grey--text"
            >
                {{ calls.meta.total }}
            </v-col>

            <v-spacer/>

            <v-col cols="auto">
                <v-btn
                icon
                title="Обновить"
                @click="refresh"
                >
                <v-icon>mdi-refresh</v-icon>
                </v-btn>

            </v-col>
        </v-row>

        <v-card
            :loading="loading"
            class="mb-3"
        >
            <v-card-text>
                <v-row>
                    <v-col cols="12" sm="6">
                        Тут будут фильтры
                    </v-col>
                </v-row>
            </v-card-text>

            <!-- Необходимо для сохранения фокуса -->
            <div class="v-card__progress"></div>

            <div class="v-data-table theme--light">
                <div class="v-data-table__wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-left text-no-wrap">Статус звонка</th>
                                <th class="text-left text-no-wrap">Дата звонка</th>
                                <th class="text-left text-no-wrap">Кто звонил</th>
                                <th class="text-left text-no-wrap">Кому звонил</th>
                                <th class="text-left text-no-wrap">Длительность</th>
                            </tr>
                        </thead>
                        <tbody>
                            <call-list-item 
                                v-for="(call, index) in calls.data"
                                :key="call.uniqueid + `_${index}`"
                                :call="call"
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
                    v-if="calls.meta.last_page > 1"
                    :total-visible="7"
                    :length="calls.meta.last_page"
                    :value="calls.meta.current_page"
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

import CallListItem from './CallListItem.vue';

import { createNamespacedHelpers } from 'vuex';
const { mapState, mapMutations } = createNamespacedHelpers('calls');

export default {
    name: 'CallList',

    components: {
        CallListItem
    },

    data() {
        return {
            loading: false,
            calls: {
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

    computed: {
        ...mapState(['filters', 'currentPage']),
    },

    created() {
        this.fetchData(Object.assign({}, this.filter, {page: this.changePage}));
    },

    methods: {
        ...mapMutations({
            updateSrcFilter: 'updateSrcFilter',
            updateDstFilter: 'updateDstFilter',
            updateCurrentPage: 'updateCurrentPage'
        }),

        fetchData(params) {
            this.loading = true;

            axios.get('calls', {params: params})
                .then((response) => {
                    this.calls = response.data;
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
            this.fetchData(Object.assign({}, this.filter, {page: page}));
            this.updateCurrentPage(page);
            this.$vuetify.goTo('#app');
        },

        refresh() {
            this.fetchData(Object.assign({}, this.filter, {page: this.changePage}));
        }
    },
}
</script>