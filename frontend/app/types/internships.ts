import type { CompanyData } from "./company_data";
import type { InternshipStatusData } from "./internship_status";
import type { User } from "./user";

export interface Internship {
    id: number;
    user_id?: string;
    user?: User;
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