export const formatUAH = (amount) => {
    return new Intl.NumberFormat('uk-UA', {
        style: 'currency',
        currency: 'UAH',
    }).format(amount);
};
