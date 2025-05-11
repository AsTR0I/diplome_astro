<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <v-row align="center">
                    <h1 class="headline font-weight-medium">SIP Session Сниффер</h1>
                </v-row>
            </v-col>

            <v-col
                cols="auto"
                class="grey--text"
            >
                {{ packets.meta.total || 0 }}
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
                                    Date
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon v-on="on" small>help</v-icon>
                                        </template>
                                        <span>Timestamp</span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Method
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon v-on="on" small>help</v-icon>
                                        </template>
                                        <span>Method</span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Ruri
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon v-on="on" small>help</v-icon>
                                        </template>
                                        <span>Timestamp</span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    From
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon v-on="on" small>help</v-icon>
                                        </template>
                                        <span>Timestamp</span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    To
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon v-on="on" small>help</v-icon>
                                        </template>
                                        <span>Timestamp</span>
                                    </v-tooltip>
                                </th>
                                <th class="text-left text-no-wrap">
                                    Call-Id
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                        <v-icon v-on="on" small>help</v-icon>
                                        </template>
                                        <span>Call-Id</span>
                                    </v-tooltip>
                                </th>
                                <th class="text-right text-no-wrap"></th>
                            </tr>
                            </thead>

                        <tbody>
                            <packets-list-item 
                                v-for="(packet, index) in packets.data"
                                :key="packet.id + `_${index}`"
                                :packet="packet"
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
               
            </v-col>
        </v-row>

        <div>
            <notifications group="foo" position="top center" class="mt-12"/>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import PacketsListItem from './PacketsListItem.vue'
export default {
    name: 'packetsList',

    components: {
        PacketsListItem,
    },

    data() {
        return {
            loading: false,
            packets: {
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
        this.fetchData(Object.assign({}, {page: this.packets.meta.current_page}));
    },

    methods: {
        fetchData(params) {
            this.loading = true;

            axios.get('sniffer/sip-packets', {params: params})
                .then((response) => {
                    this.packets = response.data;
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
            this.fetchData(Object.assign({}, {page: this.packets.meta.current_page}));
        },
    },
}
</script>