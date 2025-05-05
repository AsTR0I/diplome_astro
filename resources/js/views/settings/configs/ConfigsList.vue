<template>
    <div>
        <v-row
            align="center"
            class="mb-0"
        >
            <v-col cols="auto">
                <v-row align="center">
                    <h1 class="headline font-weight-medium">Конфигурационные файлы</h1>
                    <!-- <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-icon
                                v-on="on"
                                small
                                class="ml-1"
                            >
                                help
                            </v-icon>
                        </template>
                        <span>
                            
                        </span>
                    </v-tooltip> -->
                </v-row>
            </v-col>

            <v-col
                cols="auto"
                class="grey--text d-flex align-center"
                >
                <v-btn
                    icon
                    class="ma-0 pa-0 mr-2"
                    title="Количество папок в текущей директории"
                >
                    <v-icon small>folder</v-icon>
                    {{ configs.meta.directories_count }}
                </v-btn>

                <v-btn
                    icon
                    class="ma-0 pa-0"
                    title="Количество файлов в текущей директории"
                >
                    <v-icon small>description</v-icon>
                    {{ configs.meta.files_count }}
                </v-btn>
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

            <v-col cols="auto">
                <input
                    ref="fileInput"
                    type="file"
                    style="display: none"
                    @change="handleFileChange"
                />
                    <v-btn color="primary" :disabled="loading" @click="triggerFileInput">
                <v-icon left>add</v-icon>
                    Загрузить файл
                </v-btn>
            </v-col>
        </v-row>

        <!-- Ренейм файла -->
        <v-dialog v-model="renameDialog.visible" max-width="500px">
            <v-card>
                <v-card-title>Переименовать</v-card-title>
                <v-card-text>
                <v-text-field v-model="renameDialog.newName" label="Новое имя файла/папки" />
                </v-card-text>
                <v-card-actions>
                <v-spacer />
                <v-btn text @click="renameDialog.visible = false">Отмена</v-btn>
                <v-btn color="primary" @click="renameFile">Сохранить</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Редактирование файла -->
        <v-dialog v-model="editDialog.visible" max-width="80%">
            <v-card :loading="loading">
                <v-card-title class="d-flex justify-space-between align-center">
                <span>{{ editDialog.item?.name || 'Редактирование файла' }}</span>
                <v-btn :disabled="loading" icon @click="closeEditDialog">
                    <v-icon>close</v-icon>
                </v-btn>
                </v-card-title>

                <v-card-text>
                <v-textarea
                    v-model="editDialog.content"
                    label="Содержимое файла"
                    auto-grow
                    rows="10"
                ></v-textarea>
                </v-card-text>

                <v-card-actions class="justify-end">
                <v-btn :disabled="loading" color="grey darken-1" text @click="closeEditDialog">Отменить</v-btn>
                <v-btn :disabled="loading" color="primary" @click="saveFile(false)">Сохранить</v-btn>
                <v-btn :disabled="loading" color="primary" @click="saveFile(true)">Сохранить и выйти</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-card
            :loading="loading"
            class="mb-4"
            style="height: 100vh;"
        >

            <!-- Необходимо для сохранения фокуса -->
            <div class="v-card__progress"></div>
            <v-card-text class="mb-0">
                <v-row
                    align="center"
                    class="mb-0"
                >
                    <v-col cols="auto">
                        <!-- Кнопка перехода в родительскую папку -->
                        <v-btn
                            icon
                            :disabled="!configs.meta.parent_path || loading"
                            :title="`Перейти к ${configs.meta.parent_path || '/'}`"
                            @click="goToParent"
                            >
                            <v-icon>arrow_back</v-icon>
                        </v-btn>
                    </v-col>
                        <v-col cols="auto">
                            <!-- Отображение текущего пути -->
                            <span>{{ configs.meta.relative_path == "" ? "/" : configs.meta.relative_path }}</span>
                        </v-col>
                </v-row>
            </v-card-text>
            <div style="height: calc(100vh - 100px); overflow-y: auto;">
                <v-row wrap dense class="ma-4">
                    <v-col
                        v-for="(item, i) in sortedItems"
                        :key="i"
                        cols="auto"
                        class="text-center"
                    >
                    <template>
                        <div style="position: relative;">
                            <v-card
                                class="mx-2 d-flex flex-column justify-space-between align-center fixed-card"
                                @click="handleClick(item)"
                                @dblclick="editItem(item)"
                                @contextmenu.prevent="openContextMenu($event, item)"
                                :title="item.name"
                                elevation="2"
                                max-width="150"
                                tile
                            >
                            <v-card-title class="d-flex justify-center align-center">
                                <v-icon
                                :color="getIconColor(item)"
                                style="font-size: 50px; margin-bottom: 8px;"
                                >
                                {{ getIcon(item) }}
                                </v-icon>
                            </v-card-title>

                            <v-card-subtitle
                                :title="item.name"
                                class="text-center text-truncate filename-subtitle"
                                style="font-size: 14px; font-weight: 500;"
                                >
                                {{ item.name }}
                                </v-card-subtitle>
                            </v-card>

                            <v-menu
                                v-model="contextMenu.visible"
                                :position-x="contextMenu.x"
                                :position-y="contextMenu.y"
                                absolute
                                offset-y
                                content-class="thin-shadow-menu"
                                >
                                <v-list dense>
                                    <!-- Редактировать, если элемент типа 'file' -->
                                    <v-list-item @click="editItem(contextMenu.item)" v-if="contextMenu.item && contextMenuCheckVisible(contextMenu.item)">
                                        <v-list-item-title>Редактировать</v-list-item-title>
                                    </v-list-item>

                                    <!-- Переименовать (независимо от типа) -->
                                    <v-list-item @click="renameItem(contextMenu.item)">
                                        <v-list-item-title>Переименовать</v-list-item-title>
                                    </v-list-item>

                                    <!-- Скачать, если элемент типа 'file' -->
                                    <v-list-item @click="downloadItem(contextMenu.item)" v-if="contextMenu.item && contextMenuCheckVisible(contextMenu.item)">
                                        <v-list-item-title>Скачать</v-list-item-title>
                                    </v-list-item>

                                    <!-- Удалить (независимо от типа) -->
                                    <v-list-item @click="deleteItem(contextMenu.item)">
                                        <v-list-item-title>Удалить</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </div>
                    </template>

                    </v-col>
                </v-row>
            </div>
        </v-card>
        <v-row
                align="center"
                no-gutters
                >
                <v-col>
                    <v-pagination
                        v-if="configs.meta.pagination.last_page > 1"
                        :total-visible="7"
                        :length="configs.meta.pagination.last_page"
                        :value="configs.meta.pagination.current_page"
                        @input="changePage"
                    />
                </v-col>
            </v-row>
        <div>
            <notifications group="foo" position="top center" class="mt-12"/>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    name: 'ConfigsList',

    components: {
    },

    data() {
        return {
            loading: false,
            configs: {
                data: [],
                meta: {
                    relative_path: null,
                    parent_path: null,
                    files_count: null,
                    directories_count: null,
                    pagination: {
                        total: null,
                        per_page: 25,
                        current_page: null,
                        last_page: null
                    }
                }
            },
            contextMenu: {
                visible: false,
                x: 0,
                y: 0,
                item: null,
            },
            editDialog: {
                visible: false,
                item: null,
                content: '',
            },
            renameDialog: {
                visible: false,
                item: null,
                newName: '',
            },
        }
    },

    computed: {
        sortedItems() {
            // Сначала директории, потом файлы
            return [...this.configs.data].sort((a, b) => {
                if (a.type === b.type) return a.name.localeCompare(b.name);
                return a.type === 'directory' ? -1 : 1;
            });
        }
    },

    created() {
        this.fetchData(Object.assign({}, {path: this.configs.meta.relative_path}));
    },

    methods: {
        fetchData(params) {
            this.loading = true;

            axios.get('settings/configs', {params: params})
                .then((response) => {
                    this.configs = response.data;
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
        fetchFileContent(params) {
            this.loading = true;
            axios.get('settings/configs/files-content', {params: params})
                .then((response) => {
                    this.editDialog.content = response.data.content || '';
                })
                .catch((error) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Ошибка',
                        text: 'Ошибка при загрузке данных.',
                        type: 'error',
                        position: 'top center'
                    });
                    this.editDialog.visible = false;
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        refresh() {
            this.fetchData(Object.assign({}, {path: this.configs.meta.relative_path, page: 1}));
        },
        changePage(page) {
            this.fetchData({ path: this.configs.meta.relative_path, page: page });
        },
        goToParent() {
            this.fetchData(Object.assign({}, {path: this.configs.meta.parent_path, page: 1}))
        },
        prettySize(bytes) {
            if (!bytes) return '';
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
        },

        getIcon(item) {
            if (item.type == 'file') {
                switch (item.extension) {
                    case 'archive':
                    case '7z':
                    case 'tar':
                    case 'tar.gz':
                    case 'gz':
                        return ' folder_zip ';
                    case 'image':
                    case 'png':
                    case 'jpeg':
                        return 'image';
                    case 'audio':
                        return 'audio_file ';
                    case 'video':
                        return 'video_file';
                    default:
                        return 'description';
                }
            } else if (item.type == 'directory') {
                return 'folder';
            } else {
                return 'description'
            }
        },

        getIconColor(item) {
            if (item.type === 'directory') {
                return 'amber darken-2';
            }

            if (item.type === 'file') {
                const ext = item.extension.toLowerCase();

                if (['7z', 'zip', 'rar', 'tar', 'tar.gz', 'gz'].includes(ext)) {
                    return 'deep-orange accent-2'; // архивы
                }

                if (['png', 'jpg', 'jpeg', 'bmp', 'gif', 'webp'].includes(ext)) {
                    return 'green lighten-2'; // изображения
                }

                if (['mp3', 'wav', 'ogg', 'flac'].includes(ext)) {
                    return 'purple lighten-2'; // аудио
                }

                if (['mp4', 'avi', 'mov', 'webm', 'mkv'].includes(ext)) {
                    return 'cyan lighten-2'; // видео
                }

                return 'blue lighten-2'; // дефолтный цвет для других файлов
            }

            return 'grey lighten-2';
        },

        handleClick(item) {
            if (item.type === 'directory') {
            const currentPath = this.configs.meta.relative_path;
            const newPath = [currentPath, item.name].filter(Boolean).join('/');
            this.fetchData({ path: newPath });
            }
        },
        openContextMenu(event, item) {
            this.contextMenu.x = event.clientX;
            this.contextMenu.y = event.clientY;
            this.contextMenu.item = item;
            this.contextMenu.visible = true;
        },
        contextMenuCheckVisible(item) {
            const editableExtensions = ['txt', 'md', 'html', 'js', 'css', 'json', 'php', 'xml','sample', 'conf'];
            if (item && item.type === 'file') {
                const extension = item.extension.toLowerCase();
                return editableExtensions.includes(extension);
            }
            return false;
        },
        downloadItem(item) {
            this.contextMenu.visible = false;
            this.loading = true;

            const params = {
                path: this.configs.meta.relative_path,
                name: item.name
            };

            axios.get('settings/configs/files/download', {
                params,
                responseType: 'blob'
            })
            .then((response) => {
                const fileName = item.name;
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();

                this.$notify({
                    group: 'foo',
                    title: 'Успех',
                    text: 'Файл успешно скачан.',
                    type: 'success',
                    position: 'top center'
                });
            })
            .catch((error) => {
                this.$notify({
                    group: 'foo',
                    title: 'Ошибка',
                    text: error.response?.data?.message || 'Ошибка при скачивании файла.',
                    type: 'error',
                    position: 'top center'
                });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        renameItem(item) {
            this.contextMenu.visible = false;
            this.renameDialog.item = item;
            this.renameDialog.newName = item.name;
            this.renameDialog.visible = true;
        },
        deleteItem(item) {
            if (!confirm("Вы точно хотите удалить?")) {
                return
                this.contextMenu.visible = false;
            }
            this.contextMenu.visible = false;
            this.loading = true;
            const params = {
                path: this.configs.meta.relative_path,
                name: item.name
            };
            axios.post('settings/configs/files/delete', params)
                .then((response) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Успех',
                        text: 'Файл успешно удалён.',
                        type: 'success',
                        position: 'top center'
                    });
                })
                .catch((error) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Ошибка',
                        text: error.response?.data?.message || 'Ошибка при удаление файла.',
                        type: 'error',
                        position: 'top center'
                    });
                    this.editDialog.visible = false;
                })
                .finally(() => {
                    this.loading = false;
                    this.renameDialog.visible = false;
                    this.refresh();
                })
        },

        editItem(item) {
            if (item.type !== 'file') return;
            this.contextMenu.visible = false;
            // Здесь загружаем содержимое файла (заглушка пока)
            this.editDialog.item = item;
            this.fetchFileContent(Object.assign({}, { file_path: this.configs.meta.relative_path + '/' + item.name }))
            this.editDialog.visible = true;

        },

        renameFile() {
            this.loading = true;
            const params = {
                path: this.configs.meta.relative_path, // относительный путь к текущей директории
                old_name: this.renameDialog.item.name, // текущее имя
                new_name: this.renameDialog.newName    // новое имя
            };
            axios.post('settings/configs/files/rename', params)
                .then((response) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Успех',
                        text: 'Файл успешно переименнован.',
                        type: 'success',
                        position: 'top center'
                    });
                })
                .catch((error) => {
                    this.$notify({
                        group: 'foo',
                        title: 'Ошибка',
                        text: error.response?.data?.message || 'Ошибка при изменении имени файла.',
                        type: 'error',
                        position: 'top center'
                    });
                    this.editDialog.visible = false;
                })
                .finally(() => {
                    this.loading = false;
                    this.renameDialog.visible = false;
                    this.refresh();
                })
        },

        closeEditDialog() {
            this.editDialog.visible = false;
            this.editDialog.item = null;
            this.editDialog.content = '';
        },
        saveFile(closeAfterSave = false) {
               
                    var params = {
                        path: this.configs.meta.relative_path,
                        name: this.editDialog.item.name,
                        content: this.editDialog.content
                    }
                    axios.post('settings/configs/files-content', params)
                    .then((response) => {
                        this.$notify({
                            group: 'foo',
                            title: 'Успех',
                            text: 'Файл успешно сохранён.',
                            type: 'success',
                            position: 'top center'
                        });
                    })
                    .catch((error) => {
                        this.$notify({
                            group: 'foo',
                            title: 'Ошибка',
                            text: error.response?.data?.message || 'Ошибка при изменении файла.',
                            type: 'error',
                            position: 'top center'
                        });
                        this.closeEditDialog();
                    })
                    .finally(() => {
                        this.loading = false;
                        if(closeAfterSave) {
                            this.closeEditDialog();
                        }
                        this.refresh();
                    })
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('path', this.configs.meta.relative_path); // если нужно

            this.loading = true;

            axios.post('/settings/configs/files/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            })
            .then(() => {
            this.$notify({
                group: 'foo',
                title: 'Успех',
                text: 'Файл успешно загружен.',
                type: 'success',
                position: 'top center',
            });
            this.refresh();
            })
            .catch((error) => {
            this.$notify({
                group: 'foo',
                title: 'Ошибка',
                text: error.response?.data?.message || 'Ошибка при загрузке файла.',
                type: 'error',
                position: 'top center',
            });
            })
            .finally(() => {
            this.loading = false;
            });
        }
    }
}
</script>

<style scoped>
.fixed-card {
  width: 150px;
  height: 120px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.filename-subtitle {
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  margin: 0 auto;
}
.thin-shadow-menu{
    box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.15); /* мягкая тень */
}

</style>