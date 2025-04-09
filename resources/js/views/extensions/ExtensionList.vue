<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <v-row align="center">
                    <h1 class="headline font-weight-medium">Расширения</h1>
                    <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-icon
                                v-on="on"
                                small
                                class="ml-1"
                            >
                                help-circle
                            </v-icon>
                        </template>
                        <span>
                            Тут будет описание-помощник
                        </span>
                    </v-tooltip>
                </v-row>
            </v-col>

            <v-col
                cols="auto"
                class="grey--text"
            >
                {{ extensions.meta.total }}
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
                    :to="{ name: 'extension.create'}"
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
                                    Context
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon
                                            v-on="on"
                                            small
                                        >
                                            help-circle
                                        </v-icon>
                                        </template>
                                        <span>
                                            Определяет контекст выполнения в Asteris
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Exten
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon
                                            v-on="on"
                                            small
                                        >
                                            help-circle
                                        </v-icon>
                                        </template>
                                        <span>
                                            Расширение, к которому применяется правило
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
                                            help-circle
                                        </v-icon>
                                        </template>
                                        <span>
                                            Определяет порядок выполнения правил
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    App
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon
                                            v-on="on"
                                            small
                                        >
                                            help-circle
                                        </v-icon>
                                        </template>
                                        <span>
                                            Приложение, вызываемое в этом шаге
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Appdata
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon
                                            v-on="on"
                                            small
                                        >
                                            help-circle
                                        </v-icon>
                                        </template>
                                        <span>
                                            Параметры, передаваемые в приложение
                                        </span>
                                    </v-tooltip>
                                </th>
                                <th class="text-right text-no-wrap"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <extension-list-item 
                                v-for="(extension, index) in extensions.data"
                                :key="extension.id + `_${index}`"
                                :extension="extension"
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
                    v-if="extensions.meta.last_page > 1"
                    :total-visible="7"
                    :length="extensions.meta.last_page"
                    :value="extensions.meta.current_page"
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
import ExtensionListItem from './ExtensionListItem.vue';

export default {
    name: 'ExtensionList',

    components: {
        ExtensionListItem
    },

    data() {
        return {
            loading: false,
            extensions: {
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
        this.fetchData(Object.assign({}, {page: this.extensions.meta.current_page}));
    },

    methods: {
        fetchData(params) {
            this.loading = true;

            axios.get('extensions', {params: params})
                .then((response) => {
                    this.extensions = response.data;
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
            this.fetchData(Object.assign({}, {page: page}));
            this.$vuetify.goTo('#app');
        },
        refresh() {
            this.fetchData(Object.assign({}, {page: this.extensions.meta.current_page}));
        },
    },
}
</script>