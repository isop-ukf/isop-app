import { Role } from "./role";
import type { User } from "./user";

export interface InternshipStatusData {
    internship_id: number;
    user_id: string;
    status: InternshipStatus;
    changed: string;
    note: string;
    modified_by: User;
};

export interface NewInternshipStatusData {
    internship_id: number;
    status: InternshipStatus;
    note: string;
};

export enum InternshipStatus {
    SUBMITTED = 'SUBMITTED',
    CONFIRMED = 'CONFIRMED',
    DENIED = 'DENIED',
    DEFENDED = 'DEFENDED',
    NOT_DEFENDED = 'NOT_DEFENDED'
};

export function prettyInternshipStatus(status: InternshipStatus) {
    switch (status) {
        case InternshipStatus.SUBMITTED:
            return "Zadané";
        case InternshipStatus.CONFIRMED:
            return "Potvrdené";
        case InternshipStatus.DENIED:
            return "Zamietnuté";
        case InternshipStatus.DEFENDED:
            return "Obhájené";
        case InternshipStatus.NOT_DEFENDED:
            return "Neobhájené";
        default:
            throw new Error("Unknown status");
    }
}

export function possibleNextStates(status: InternshipStatus, user_role: Role) {
    switch (status) {
        case InternshipStatus.SUBMITTED:
            if (user_role === Role.EMPLOYER) {
                return [];
            }

            return [InternshipStatus.CONFIRMED, InternshipStatus.DENIED];
        case InternshipStatus.CONFIRMED:
            if (user_role === Role.EMPLOYER) {
                return [InternshipStatus.DENIED];
            }

            return [InternshipStatus.SUBMITTED, InternshipStatus.DENIED, InternshipStatus.DEFENDED, InternshipStatus.NOT_DEFENDED];
        case InternshipStatus.DENIED:
            if (user_role === Role.EMPLOYER) {
                return [InternshipStatus.CONFIRMED];
            }

            return [InternshipStatus.SUBMITTED, InternshipStatus.CONFIRMED];
        case InternshipStatus.DEFENDED:
            return [];
        case InternshipStatus.NOT_DEFENDED:
            return [];
        default:
            throw new Error("Unknown status");
    }
}