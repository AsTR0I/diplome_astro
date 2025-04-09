<template>
    <div class="v-data-table theme--light">
        <div class="v-data-table__wrapper">
            <table>
                <tbody>
                    <tr>
                        <td class="text-left">CPU Info:</td>
                        <td class="text-right">{{ data.cpu_model }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">Uptime:</td>
                        <td class="text-right">{{ data.uptime }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">CPU Speed:</td>
                        <td class="text-right">{{ data.cpu_speed }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">Memoty usage:</td>
                        <td class="text-right">RAM: {{ data.memory_usages }}Mb</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SystemResourcesList',

    props: {
        data: {
            type: Object,
            required: true,
        }
    }
}
</script>