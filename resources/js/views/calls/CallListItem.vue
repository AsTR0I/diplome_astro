<template>
    <tr>
        <td>
            <v-icon
                :title="getStatusTitle(call)"
                :color="getStatusColor(call)"
            >
                {{ getStatusIcon(call) }}
              </v-icon>
        </td>
        <!-- Статус -->
        <td>
            {{ getStatusText(call) }}
        </td>
        <!-- Дата звонка -->
        <td>
            {{ formatDate(call.calldate) }}
        </td>
        <!-- Кто звонил -->
        <td>
            {{ call.src ?? 'Нет' }}
        </td>
        <!-- Кому звонил -->
        <td>
            {{ call.dst ?? 'Нет' }}
        </td>
        <!-- Длительность звонка -->
        <td>
            {{ formatDuration(call.duration) }}
        </td>
    </tr>
</template>

<script>
import moment from 'moment';

export default {
    name: 'CallListItem',

    props: {
        call: {
            type: Object,
            required: true
        }
    },

    methods: {
        // Статус и иконка
        getStatusTitle(item) {
            if (item.disposition === 'ANSWERED') {
                return 'Отвеченный';
            } else if (item.disposition === 'BUSY') {
                return 'Занято';
            } else {
                return 'Не отвеченный';
            }
        },
        getStatusColor(item) {
            if (item.disposition === 'ANSWERED') {
                return 'blue';
            } else if (item.disposition === 'BUSY') {
                return 'orange';
            } else {
                return 'red';
            }
        },
        getStatusIcon(item) {
            if (item.disposition === 'ANSWERED') {
                return 'call-received';
            } else if (item.disposition === 'BUSY') {
                return 'phone-alert';
            } else {
                return 'call-missed';
            }
        },
        getStatusText(item) {
            if (item.disposition === 'ANSWERED') {
                return 'Отвеченный';
            } else if (item.disposition === 'BUSY') {
                return 'Занято';
            } else {
                return 'Не отвеченный';
            }
        },
        // Дата
        formatDate(dateString) {
            return moment(dateString, 'DD.MM.YYYY HH:mm:ss').format('DD.MM.YYYY HH:mm:ss');
        },
        // Длительность
        formatDuration(duration) {
            const minutes = String(Math.floor(duration / 60)).padStart(2, '0');
            const seconds = String(duration % 60).padStart(2, '0');
            return `${minutes}:${seconds}`;
        }
    }
}
</script>