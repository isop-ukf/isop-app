<template>
    <div>
        <v-alert density="compact" :text="error" :title="title || 'Chyba'" type="error" class="mb-2 mt-2"></v-alert>
    </div>
</template>

<script lang="ts">
export default {
    props: {
        title: {
            type: String,
            required: false,
            default: "Chyba"
        },
        error: {
            type: String,
            required: true,
        }
    }
}
</script>