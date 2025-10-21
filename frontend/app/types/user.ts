import type { Role } from "./role";
import type { CompanyData } from "./company_data";
import type { StudentData } from "./student_data";

export interface User {
    id: number,
    name: string,
    email: string,
    first_name: string,
    last_name: string,
    phone: string,
    role: Role,
    company_data?: CompanyData,
    student_data?: StudentData,
};