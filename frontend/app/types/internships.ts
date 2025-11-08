import type { CompanyData } from "./company_data";
import type { InternshipStatusData } from "./internship_status";
import type { User } from "./user";

export interface Internship {
    id: number;
    student: User;
    company: CompanyData;
    start: string;
    end: string;
    year_of_study: number;
    semester: string;
    position_description: string;
    agreement: boolean;
    report: boolean;
    report_confirmed: boolean;
    status: InternshipStatusData;
};

export interface NewInternship {
    user_id: number;
    company_id: number;
    start: string;
    end: string;
    year_of_study: number;
    semester: string;
    position_description: string;
};

export function convertDate(date: string): Date {
    const matcher = /^\d\d.\d\d.\d\d\d\d$/;

    if (!matcher.test(date)) {
        throw new Error(`Invalid date or format: '${date}'`);
    }

    const [day, month, year] = date.split('.').map(Number);

    if (day === undefined || month === undefined || year === undefined) {
        throw new Error(`Unable to parse date parts: '${date}'`);
    }

    if (month < 1 || month > 12) {
        throw new Error(`Invalid month: ${month}`);
    }

    if (day < 1 || day > 31) {
        throw new Error(`Invalid day: ${day}`);
    }

    return new Date(year, month - 1, day);
}