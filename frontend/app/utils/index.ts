import { FetchError } from 'ofetch';

export function triggerDownload(file: Blob, file_name: string, ext: string) {
    const url = window.URL.createObjectURL(file);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${file_name}.${ext}`;
    link.target = "_blank";
    link.click();
    window.URL.revokeObjectURL(url);
};

export function simplifyApiError(e: any): string {
    if (e instanceof FetchError) {
        const error = e as FetchError;
        return e.response?._data.message ?? error.statusMessage ?? error.message;
    }

    return `Unprocessable error: ${e}`;
};