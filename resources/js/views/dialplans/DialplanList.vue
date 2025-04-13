<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <v-row align="center">
                    <h1 class="headline font-weight-medium">Диалплан</h1>
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
                            Диалплан (dialplan) — это логика обработки вызовов в Asterisk. 
                            Он определяет, что происходит, когда кто-то звонит: маршруты, условия и действия.
                        </span>
                    </v-tooltip>
                </v-row>
            </v-col>

            <v-col
                cols="auto"
                class="grey--text"
            >
                {{ dialplans.meta.total }}
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
                    :to="{ name: 'dialplan.create'}"
                    color="primary"
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
                                    Destination
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
                                            Цель маршрута, которая указывает, куда будет направлен звонок
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Context
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
                                            Контекст диалплана определяет область или "сценарий", в котором применяются правила.
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Priority
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
                                            Приоритет указывает порядок выполнения действий в диалплане.
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Action
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
                                            Действие, которое будет выполнено
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Params
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
                                            Параметры указывают дополнительные настройки для "action".
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-right text-no-wrap"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <dialplan-list-item 
                                v-for="(dialplan, index) in dialplans.data"
                                :key="dialplan.id + `_${index}`"
                                :dialplan="dialplan"
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
                    v-if="dialplans.meta.last_page > 1"
                    :total-visible="7"
                    :length="dialplans.meta.last_page"
                    :value="dialplans.meta.current_page"
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
import DialplanListItem from './DialplanListItem.vue';

export default {
    name: 'DialplanList',

    components: {
        DialplanListItem
    },

    data() {
        return {
            loading: false,
            dialplans: {
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

            axios.get('dialplans', {params: params})
                .then((response) => {
                    this.dialplans = response.data;
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
            this.fetchData(Object.assign({}, {page: this.dialplans.meta.current_page}));
        },
    },
}
</script>