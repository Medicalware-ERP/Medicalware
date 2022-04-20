export function isText(data: any): data is string {
    return typeof data === 'string';
}