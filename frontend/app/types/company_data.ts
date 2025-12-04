import type { User } from "./user";

export interface CompanyData {
    id: number;
    name: string;
    address: string;
    ico: number;
    contact: User;
    hiring: boolean;
};

export interface NewCompanyData {
    name: string;
    address: string;
    ico: number;
    hiring: boolean;
};