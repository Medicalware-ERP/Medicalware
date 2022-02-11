export function $(selector, f) {
    if (f === undefined)
        return document.querySelector(selector)
    else
        document.querySelectorAll(selector).forEach(f)
}