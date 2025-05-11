<template>
    <tr>
        <td>
            {{ packet.captured_at || 'N/A' }}
        </td>
        <td>
            {{ packet.method || 'N/A' }}
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            {{ packet.call_id || 'N/A' }}
        </td>
        <td class="text-right text-no-wrap">
            <v-btn
                small
                icon
                title="Подробнее"
                target="_blank"
                :to="{ 
                    name: 'sniffer.sip.session', 
                    params: { 
                        callId: packet.call_id 
                    }
                }"
                >
                <v-icon>arrow_forward</v-icon>
            </v-btn>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'PacketsListItem',

    props: {
        packet: {
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

}
</script>