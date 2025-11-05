<template>
    <div>
        <v-alert density="compact" :text="text" :title="title" type="success" class="mb-2 mt-2"></v-alert>
    </div>
</template>

<script lang="ts">
export default {
    props: {
        title: {
            type: String,
            required: true,
        },
        text: {
            type: String,
            required: true,
        }
    }
}
</script>