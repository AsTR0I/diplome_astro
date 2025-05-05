<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <v-row align="center">
                    <h1 class="headline font-weight-medium">SIP телефоны</h1>
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
                            SIP пир (SIP peer) — это точка подключения (абонент или шлюз), которая
                            может регистрироваться на Asterisk-сервере и совершать/принимать звонки.
                            Здесь вы можете добавлять и редактировать такие подключения.
                        </span>
                    </v-tooltip>
                </v-row>
            </v-col>

            <v-col
                cols="auto"
                class="grey--text"
            >
                {{ sippeers.meta.total }}
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
                <v-menu>
                    <template v-slot:activator="{ on }">
                        <v-btn
                        icon
                        title="Экспорт"
                        v-on="on"
                        >
                        <v-icon>get_app</v-icon>
                        </v-btn>
                    </template>

                    <v-list>
                        <v-list-item 
                            @click="downloadXLSX()"
                            >
                            <v-list-item-title>Выгрузить в XLSX</v-list-item-title>
                        </v-list-item>
                        <v-list-item
                            :disabled="sippeers.meta.last_page <= 1"
                            @click="downloadXLSX('full')"
                            >
                            <v-list-item-title>Выгрузить в XLSX (Полная)</v-list-item-title>
                        </v-list-item>
                    </v-list>

                    <v-divider />
                </v-menu>
            </v-col>
            <v-col cols="auto">
                <v-btn
                    :to="{ name: 'sippeers.create'}"
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
                                Name
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Имя SIP-пира (уникальный идентификатор)</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Type
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Тип пира: peer, user или friend</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Secret
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Пароль для аутентификации</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Host
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>IP адрес или имя хоста (или dynamic)</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Context
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Контекст в dial-плане (extensions.conf)</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Nat
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Наличие NAT у клиента (yes, no и т.д.)</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Directmedia
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Передавать RTP напрямую (yes, no, nonat)</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Ip
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Регистрированный IP адрес пира</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Port
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Порт, на котором работает клиент</span>
                                </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                Allow
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                    <v-icon v-on="on" small>help</v-icon>
                                    </template>
                                    <span>Разрешённые аудиокодеки (например, ulaw, alaw, g729)</span>
                                </v-tooltip>
                                </th>
                                <th class="text-right text-no-wrap"></th>
                            </tr>
                            </thead>

                        <tbody>
                            <sippeers-list-item 
                                v-for="(sippeer, index) in sippeers.data"
                                :key="sippeer.id + `_${index}`"
                                :sippeer="sippeer"
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
                    v-if="sippeers.meta.last_page > 1"
                    :total-visible="7"
                    :length="sippeers.meta.last_page"
                    :value="sippeers.meta.current_page"
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
import SippeersListItem from './SippeersListItem.vue';

export default {
    name: 'SippeersList',

    components: {
        SippeersListItem
    },

    data() {
        return {
            loading: false,
            sippeers: {
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
        this.fetchData(Object.assign({}, {page: this.sippeers.meta.current_page}));
    },

    methods: {
        fetchData(params) {
            this.loading = true;

            axios.get('sippeers', {params: params})
                .then((response) => {
                    this.sippeers = response.data;
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
            this.fetchData(Object.assign({}, {page: this.sippeers.meta.current_page}));
        },
        downloadXLSX(mode) {
            this.loading = true;
            const params = {
                paginate: mode == 'full' ? 'false' : 'true',
                page: this.sippeers.meta.current_page
            };
            axios.get("sippeers/export-to-xlsx", {
                params,
                responseType: 'blob'
            })
            .then((response) => {
                const disposition = response.headers['content-disposition'];
                let fileName = 'sippeers_export.xlsx';

                if (disposition && disposition.includes('filename=')) {
                    const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (match && match[1]) {
                        fileName = match[1].replace(/['"]/g, '');
                    }
                }

                const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = fileName;
                link.click();
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
            })
        }
    },
}
</script>