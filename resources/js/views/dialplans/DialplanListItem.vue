<template>
    <tr>
        <td>
            {{ dialplan.destination	}}
        </td>
        <td>
            {{ dialplan.context	}}
        </td>
        <td>
            {{ dialplan.priority }}
        </td>
        <td>
            {{ dialplan.action	}}
        </td>
        <td>
            {{ dialplan.params	}}
        </td>
        <td class="text-right text-no-wrap">
            <v-btn title="Изменить" icon :to="{ name: 'dialplan.edit', params: { id: dialplan.id } }" :disabled="loading">
                <v-icon>edit</v-icon>
            </v-btn>
            <v-btn title="Удалить" icon @click="deleteDialplan(dialplan)" :disabled="loading">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'DialplanListItem',

    props: {
        dialplan: {
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
        deleteDialplan(dialplan) {
            this.loading = true;
            window.axios.post(`dialplans/${dialplan.id}/delete`)
                .then((response) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Успех',
                        text: 'Диалплан успешно удалён.',
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