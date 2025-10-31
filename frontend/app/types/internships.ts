import type { CompanyData } from "./company_data";
import type { InternshipStatusData } from "./internship_status";

export interface Internship {
    id: number;
    user_id: string;
    company: CompanyData;
    start: string;
    end: string;
    year_of_study: number;
    semester: string;
    position_description: string;
    agreement?: Uint8Array;
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
    agreement?: Uint8Array;
};