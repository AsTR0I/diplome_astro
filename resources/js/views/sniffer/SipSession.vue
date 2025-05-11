<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <v-row align="center">
                    <h1 class="headline font-weight-medium">SIP Session call-id: {{ callId ?? 'N/A' }}</h1>
                </v-row>
            </v-col>

            <v-spacer />
        </v-row>

        <v-card
        :loading="loading"
        class="pa-4"
        >
        <v-expansion-panels multiple>
            <v-expansion-panel
            v-for="packet in packets.data"
            :key="packet.id"
            >
            <v-expansion-panel-title>
                <div>
                <strong>{{ packet.captured_at }}</strong> — 
                <v-chip
                    :color="methodColor(packet.method)"
                    dark
                    small
                >
                    {{ packet.method || 'UNKNOWN' }}
                </v-chip>
                <br>
                <small>{{ packet.source_ip || '???' }} ➜ {{ packet.dest_ip || '???' }}</small>
                </div>
            </v-expansion-panel-title>

            <v-expansion-panel-text>
                <pre style="white-space: pre-wrap; word-break: break-word;">{{ packet.full_packet }}</pre>
            </v-expansion-panel-text>
            </v-expansion-panel>
        </v-expansion-panels>
        </v-card>


        <div>
            <notifications group="foo" position="top center" class="mt-12"/>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    name: 'packetsList',

    components: {
    },

    data() {
        return {
            loading: false,
            packets: {
                data: [],
            },
        }
    },

    created() {
        this.fetchData({ call_id: this.callId });
    },

    props: {
        callId: {
            type: String,
            required: true
        },
    },

    methods: {
        fetchData(params) {
            this.loading = true;

            axios.get(`sniffer/sip-packets/session`, {params: params})
                .then((response) => {
                    console.log(response)

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
        firstSipLine(packet) {
            const lines = packet.split('\n');
            return lines.find(line =>
                line.match(/^(INVITE|ACK|BYE|CANCEL|OPTIONS|REGISTER|INFO|SUBSCRIBE|NOTIFY|MESSAGE|REFER|SIP\/2.0)/i)
            ) || '(не SIP)';
        },

        methodColor(method) {
            switch ((method || '').toUpperCase()) {
                case 'INVITE': return 'green';
                case 'BYE': return 'red';
                case 'CANCEL': return 'orange';
                case 'ACK': return 'blue';
                case 'REGISTER': return 'cyan';
                case 'SIP/2.0': return 'purple';
                default: return 'grey';
            }
        }
    },
}
</script>