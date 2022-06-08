export function $(selector: string, f: any = null) {
    if (f === null)
        return document.querySelector(selector)
    else {
        document.querySelectorAll(selector).forEach(f)
    }
}

export function getCssVariableValue(variable: string): string {
    return getComputedStyle(document.documentElement).getPropertyValue(variable);
}

export let colors = {
    primary:    getCssVariableValue('--color-primary'),
    secondary : getCssVariableValue('--color-secondary'),
    danger:     getCssVariableValue('--color-danger'),
    success :   getCssVariableValue('--color-success'),
    warning :   getCssVariableValue('--color-warning'),
    body :      getCssVariableValue('--bg-color-body'),
    white:      getCssVariableValue('--color-white'),
    pink:       getCssVariableValue('--color-pink'),
    orange :    getCssVariableValue('--color-orange'),
    grey :      getCssVariableValue('--color-grey'),
    navLinks :  getCssVariableValue('--bg-color-nav-links'),
    black :     getCssVariableValue('--color-black'),
}

export const simpleLoader = () => '<i class="fas fa-circle-notch fa-spin text-primary fz-16"></i>';