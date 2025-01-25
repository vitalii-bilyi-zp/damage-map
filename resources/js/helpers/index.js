export const formatUAH = (amount) => {
    return new Intl.NumberFormat('uk-UA', {
        style: 'currency',
        currency: 'UAH',
    }).format(amount);
};

export const debounce = (func, delay = 300) => {
    let timeout;

    return function (...args) {
        clearTimeout(timeout); // Clear the previous timeout
        timeout = setTimeout(() => func.apply(this, args), delay); // Set a new timeout
    };
}
