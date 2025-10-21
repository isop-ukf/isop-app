import { Role } from "~/types/role";
import type { User } from "~/types/user";

export default defineNuxtRouteMiddleware(async (to) => {
    const user = useSanctumUser<User>();

    // If user is not authenticated, let sanctum:auth handle it
    if (!user.value) {
        return;
    }

    if (user.value.role !== Role.ADMIN) {
        return abortNavigation(createError({ statusCode: 403, statusMessage: 'Forbidden' }));
    }
});
