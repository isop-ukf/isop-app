<template>
    <v-card variant="outlined" :width="300" class="d-flex flex-column" style="margin: 20px; cursor: pointer" link
        :to="link">
        <v-card-title class="text-wrap">
            <div class="title-row">
                <v-icon v-if="icon" :icon="icon" size="24" class="title-icon" />
                <strong class="title-text">{{ title }}</strong>
            </div>
        </v-card-title>
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
        },
        icon: {
            required: false,
            type: String,
            default: null,
        },
    }
}
</script>

<style scoped>
.title-row {
  display: flex;
  align-items: center;
  gap: 8px;              
}
.title-icon {
  transform: translateY(1px);
}
.title-text {
  line-height: 1.2;    
}
</style>
