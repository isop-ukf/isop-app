import type { NewRole, Role } from "./role";
import type { CompanyData, NewCompanyData } from "./company_data";
import type { NewStudentData, StudentData } from "./student_data";

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

export interface NewUser {
    email: string,
    first_name: string,
    last_name: string,
    phone: string,
    role: NewRole,
    company_data?: NewCompanyData,
    student_data?: NewStudentData,
};