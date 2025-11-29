export type ApiKey = {
    id: number,
    name: string,
    created_at: string,
    last_used_at: string,
    owner: string,
};

export type NewApiKey = {
    key: string,
};