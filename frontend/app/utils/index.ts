export function triggerDownload(file: Blob, file_name: string) {
    const url = window.URL.createObjectURL(file);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${file_name}.pdf`;
    link.target = "_blank";
    link.click();
    window.URL.revokeObjectURL(url);
}