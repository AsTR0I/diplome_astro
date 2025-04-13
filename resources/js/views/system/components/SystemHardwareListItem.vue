<template>
    <tr>
        <td>{{ data.name ?? 'N/A' }}</td>
        <td>{{ data.size ?? 'N/A' }}</td>
        <td>{{ data.type ?? 'N/A' }}</td>
        <td>{{ data.mountpoint ?? 'N/A' }}</td>
        <td>{{ data.model ?? 'N/A' }}</td>

    </tr>
</template>

<script>
export default {
    name: 'SystemHardwareListItem',

    props: {
        data: {
            type: Object,
            required: true,
        }
    }
}
</script>