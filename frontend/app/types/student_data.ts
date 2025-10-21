export interface StudentData {
    id: number;
    user_id: number;
    address: string;
    personal_email: string;
    study_field: string;
};

export interface NewStudentData {
    user_id?: number;
    address: string;
    personal_email: string;
    study_field: string;
};