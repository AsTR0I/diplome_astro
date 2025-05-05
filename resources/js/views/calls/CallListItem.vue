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
        <td class="text-right">
            <template v-if="call.duration > 0 && call.uniqueid">
                <!-- Кнопка проигрывания -->
                <v-btn icon @click="togglePlayback(call)" :title="isPlaying ? 'Остановить' : 'Воспроизвести запись'" :loading="playLoading">
                    <v-icon>{{ isPlaying ? 'stop' : 'play_arrow' }}</v-icon>
                </v-btn>

                <!-- Кнопка скачивания -->
                <v-btn icon @click="downloadRecording(call)" :title="'Скачать запись'" :loading="downloadLoading">
                    <v-icon>get_app</v-icon>
                </v-btn>

                <!-- Аудио плеер (скрытый или как всплывающее окно, можно переделать) -->
                <audio ref="audioPlayer" controls style="display: none;"></audio>
                </template>
        </td>
    </tr>
</template>

<script>
import axios from 'axios';
import moment from 'moment';

export default {
    name: 'CallListItem',

    data() {
        return {
            playLoading: false,
            downloadLoading: false,
            isPlaying: false
        }
    },

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
                return 'call_received';
            } else if (item.disposition === 'BUSY') {
                return 'phone_alert';
            } else {
                return 'call_missed';
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
        },
        togglePlayback(item) {
            const player = this.$refs.audioPlayer;

            if (this.isPlaying) {
                player.pause();
                player.currentTime = 0;
                this.isPlaying = false;
                return;
            }

            this.playLoading = true;

            axios.get(`calls/recordings/${item.uniqueid}`, { responseType: 'blob' })
                .then((response) => {
                    const blob = new Blob([response.data], { type: 'audio/wav' });
                    const url = URL.createObjectURL(blob);
                    this.isPlaying = true;

                    player.src = url;
                    player.play();
                    player.onended = () => {
                        this.isPlaying = false;
                        URL.revokeObjectURL(url);
                    };
                })
                .catch(() => {
                    this.$notify({
                        group: 'foo',
                        title: 'Ошибка',
                        text: 'Ошибка при воспроизведении записи.',
                        type: 'error',
                        position: 'top center'
                    });
                })
                .finally(() => {
                    this.playLoading = false;
                });
        },
        downloadRecording(item) {
            this.downloadLoading = true;

            axios.get(`calls/recordings/${item.uniqueid}`, { responseType: 'blob' })
                .then((response) => {
                    const blob = new Blob([response.data], { type: 'audio/wav' });
                    const url = URL.createObjectURL(blob);

                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `${item.uniqueid}.wav`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    URL.revokeObjectURL(url);
                })
                .catch(() => {
                    this.$notify({
                        group: 'foo',
                        title: 'Ошибка',
                        text: 'Ошибка при скачивании записи.',
                        type: 'error',
                        position: 'top center'
                    });
                })
                .finally(() => {
                    this.downloadLoading = false;
                });
        }
    }
}
</script>