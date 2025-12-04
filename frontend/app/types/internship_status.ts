import type { User } from "./user";

export interface InternshipStatusData {
    status: InternshipStatus;
    changed: string;
    note: string;
    modified_by: User;
};

export interface NewInternshipStatusData {
    status: InternshipStatus;
    note: string;
};

export enum InternshipStatus {
    SUBMITTED = 'SUBMITTED',

    CONFIRMED_BY_COMPANY = 'CONFIRMED_BY_COMPANY',
    CONFIRMED_BY_ADMIN = 'CONFIRMED_BY_ADMIN',

    DENIED_BY_COMPANY = 'DENIED_BY_COMPANY',
    DENIED_BY_ADMIN = 'DENIED_BY_ADMIN',

    DEFENDED = 'DEFENDED',
    NOT_DEFENDED = 'NOT_DEFENDED',
}

export function prettyInternshipStatus(status: InternshipStatus) {
    switch (status) {
        case InternshipStatus.SUBMITTED:
            return "Zadané";
        case InternshipStatus.CONFIRMED_BY_COMPANY:
            return "Potvrdené firmou";
        case InternshipStatus.CONFIRMED_BY_ADMIN:
            return "Potvrdené garantom";
        case InternshipStatus.DENIED_BY_COMPANY:
            return "Zamietnuté firmou";
        case InternshipStatus.DENIED_BY_ADMIN:
            return "Zamietnuté garantom";
        case InternshipStatus.DEFENDED:
            return "Obhájené";
        case InternshipStatus.NOT_DEFENDED:
            return "Neobhájené";
        default:
            throw new Error(`Unknown internship status: '${status}'`);
    }
}