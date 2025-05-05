<template>
    <tr>
        <td>
            {{ user.name }}
        </td>
        <td>
            {{ user.email }}
        </td>
        <td class="text-right text-no-wrap">
            <v-btn title="Изменить" icon :to="{ name: 'settings.users.edit', params: { id: user.id } }" :disabled="true">
                <v-icon>edit</v-icon>
            </v-btn>
            <v-btn title="Удалить" icon @click="deleteUser(user)"   :disabled="user.is_current_user || loading">
                <v-icon>delete</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'UsersListItem',

    props: {
        user: {
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
        deleteUser(user) {
            if (!confirm('Вы точно хотите удалить?')) {
                return;
            }

            this.loading = true;
            window.axios.post(`settings/users/${user.id}/delete`)
                .then((response) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Успех',
                        text: 'User успешно удалён.',
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