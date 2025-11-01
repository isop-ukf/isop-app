import type { User } from "./user";

export interface InternshipStatusData {
    internship_id: number;
    user_id: string;
    status: InternshipStatus;
    changed: string;
    note: string;
    modified_by: User;
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