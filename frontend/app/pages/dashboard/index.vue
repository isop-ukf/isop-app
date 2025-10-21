<script setup lang="ts">
import { Role } from '~/types/role';
import type { User } from '~/types/user';

definePageMeta({
    middleware: ['sanctum:auth'],
});

const user = useSanctumUser<User>();
const router = useRouter();

onMounted(() => {
    if (user.value?.role === Role.ADMIN) {
        router.push('/dashboard/admin');
    } else if (user.value?.role === Role.STUDENT) {
        router.push('/dashboard/student');
    } else if (user.value?.role === Role.EMPLOYER) {
        router.push('/dashboard/company');
    }
});
</script>

<template>
    <div>
    </div>
</template>