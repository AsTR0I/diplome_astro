<template>
    <tr>
        <td>
            {{ extension.context }}
        </td>
        <td>
            {{ extension.exten }}
        </td>
        <td>
            {{ extension.priority }}
        </td>
        <td>
            {{ extension.app }}
        </td>
        <td>
            {{ extension.appdata }}
        </td>
        <td class="text-right text-no-wrap">
            <v-btn title="Изменить" icon :to="{ name: 'extension.edit', params: { id: extension.id } }">
                <v-icon>edit</v-icon>
            </v-btn>
            <v-btn title="Удалить" icon @click="deleteExtension(extension)">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'ExtensionListItem',

    props: {
        extension: {
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
        deleteExtension(extension) {
            this.loading = true;
            window.axios.post(`extensions/${extension.id}/delete`)
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