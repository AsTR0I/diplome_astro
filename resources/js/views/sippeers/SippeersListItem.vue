<template>
    <tr>
        <td>
            {{ sippeer.name }}
        </td>
        <td>
            {{ sippeer.type }}
        </td>
        <td>
            <v-text-field
                solo
                flat
                dense
                readonly
                hide-details
                style="max-width: 180px"
                :value="sippeer.secret"
                :type="passwordVisible ? 'text' : 'password'"
                v-if="sippeer.secret"
            >
                <template v-slot:append>
                <v-icon
                    class="mr-2"
                    @click="passwordVisible = !passwordVisible"
                >
                    {{ passwordVisible ? 'visibility_off' : 'visibility' }}
                </v-icon>
                <v-icon @click="copyPassword(sippeer)">content_copy</v-icon>
                </template>
            </v-text-field>
        </td>
        <td>
            {{ sippeer.host }}
        </td>
        <td>
            {{ sippeer.context }}
        </td>
        <td>
            {{ sippeer.nat }}
        </td>
        <td>
            {{ sippeer.directmedia }}
        </td>
        <td>
            {{ sippeer.ipaddr }}
        </td>
        <td>
            {{ sippeer.port }}
        </td>
        <td>
            {{ sippeer.allow }}
        </td>
        <td class="text-right text-no-wrap">
            <v-btn title="Изменить" icon :to="{ name: 'sippeers.edit', params: { id: sippeer.id } }">
                <v-icon>edit</v-icon>
            </v-btn>
            <v-btn title="Удалить" icon @click="deleteExtension(sippeer)">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'SippeersListItem',

    data() {
        return {
            passwordVisible: false
        }
    },

    props: {
        sippeer: {
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
        deleteExtension(sippeer) {
            if (!confirm('Вы точно хотите удалить?')) {
                return;
            }

            this.loading = true;
            window.axios.post(`sippeers/${sippeer.id}/delete`)
                .then((response) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Успех',
                        text: 'Extension успешно удалён.',
                        type: 'success',
                        position: 'top center'
                    });
                })
                .catch((error) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Ошибка',
                        text: 'Ошибка при удалении.',
                        type: 'error',
                        position: 'top center'
                    });
                })
                .finally(() => {
                    this.loading = false;
                    this.refresh();
                })
        },
        copyPassword(sippeer) {
            this.$copyText(sippeer.secret)
                .then(() => {
                this.$notify({
                    text: 'Пароль скопирован в буфер',
                    type: 'success',
                });
                });
        },
    }
}
</script>