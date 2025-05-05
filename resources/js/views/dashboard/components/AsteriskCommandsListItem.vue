<template>
    <tr>
        <td>
            {{ command.name }}
        </td>
        <td>
            {{ command.command }}
        </td>
        <td class="text-right text-no-wrap">
            <v-btn title="Исполнить" icon @click="executeCommand(command)" :loading="commandLoading">
                <v-icon>terminal</v-icon>
            </v-btn>
        </td>
        <v-dialog v-model="showOutputDialog" max-width="800">
            <v-card>
                <v-card-title class="headline">Результат команды</v-card-title>
                <v-card-text>
                <v-simple-table v-if="parsedOutput">
                    <thead>
                    <tr>
                        <th v-for="(head, i) in parsedOutput.headers" :key="'head-' + i">{{ head }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, i) in parsedOutput.rows" :key="'row-' + i">
                        <td v-for="(cell, j) in row" :key="'cell-' + j">{{ cell }}</td>
                    </tr>
                    </tbody>
                </v-simple-table>

                <pre v-else style="white-space: pre-wrap; font-family: monospace;">
                    {{ rawOutput }}
                </pre>
                </v-card-text>
                <v-card-actions>
                <v-spacer />
                <v-btn color="primary" text @click="showOutputDialog = false">Закрыть</v-btn>
                </v-card-actions>
            </v-card>
            </v-dialog>
    </tr>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ExtensionListItem',

    data() {
        return {
            commandLoading: false,
            showOutputDialog: false,
            parsedOutput: null,
            rawOutput: null
        }
    },
    props: {
        command: {
            type: Object,
            required: true
        },
        loading: {
            type: Boolean,
            required: true
        },
        refresh: {
            type: Function,
            required: true
        }
    },

    methods: {
        executeCommand(command) {
            this.commandLoading = true;
            axios.post(`/asterisk-commands/${command.id}/execute`)
            .then((response) => {
                const output = response.data.output || 'Нет вывода';
                this.rawOutput = output;
                this.parsedOutput = this.parseOutput(output);
                this.showOutputDialog = true;
            })
            .catch((error) => {
                this.$notify({
                    group: 'foo',
                    title: 'Ошибка',
                    text: 'Ошибка при выполнении команды',
                    type: 'error',
                    position: 'top center'

                });
            })
            .finally(() => {
                this.commandLoading = false;
            })
        },
        parseOutput(output) {
            const lines = output.trim().split('\n');
            const headers = lines[0].split(/\s{2,}/);
            const rows = lines.slice(1).filter(line => line.trim()).map(line => line.split(/\s{2,}/));
            return { headers, rows };
        }
    }
}
</script>