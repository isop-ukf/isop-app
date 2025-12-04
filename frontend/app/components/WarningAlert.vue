<template>
    <div>
        <v-alert density="compact" :text="text" :title="title" type="warning" class="mb-2 mt-2">
            <slot />
        </v-alert>
    </div>
</template>

<script lang="ts">
export default {
    props: {
        title: {
            type: String,
            required: false,
            default: "Upozornenie"
        },
        text: {
            type: String,
            required: true,
        }
    }
}
</script>