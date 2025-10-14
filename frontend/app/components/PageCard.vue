<template>
    <v-card variant="outlined" :width="300" class="d-flex flex-column" style="margin: 20px; cursor: pointer" link
        :to="link">
        <v-card-title class="text-wrap"><strong>{{ title }}</strong></v-card-title>
        <v-card-text>
            {{ description }}
        </v-card-text>
    </v-card>
</template>

<script lang="ts">
function basicPropValidator(value: string, _other_props: any) {
    // zatiaľ stačí vedieť či obsah nie je prázdny reťazec
    return value.trim().length > 0
}

export default {
    props: {
        title: {
            required: true,
            type: String,
            validator: basicPropValidator
        },
        description: {
            required: true,
            type: String,
            validator: basicPropValidator
        },
        link: {
            required: true,
            type: String,
            validator: basicPropValidator
        }
    }
}
</script>