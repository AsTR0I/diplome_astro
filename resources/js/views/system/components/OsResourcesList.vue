<template>
    <div class="v-data-table theme--light">
        <div class="v-data-table__wrapper">
            <table>
                <tbody>
                    <tr>
                        <td class="text-left">OS Name:</td>
                        <td class="text-right">{{ data.os_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">OS Release:</td>
                        <td class="text-right">{{ data.os_release	 }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">OS Version:</td>
                        <td class="text-right">{{ data.os_version }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">Architecture:</td>
                        <td class="text-right">{{ data.architecture }}</td>
                    </tr>
                    <tr>
                        <td class="text-left">Hostname:</td>
                        <td class="text-right">{{ data.hostname }}</td>
                    </tr>
                    	
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'OsResourcesList',

    props: {
        data: {
            type: Object,
            required: true,
        }
    }
}
</script>