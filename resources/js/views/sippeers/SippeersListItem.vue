<template>
    <tr>
        <td>
            {{ sippeer.name }}
        </td>
        <td>
            {{ sippeer.type }}
        </td>
        <td>
            {{ sippeer.secret }}
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
        }
    }
}
</script>