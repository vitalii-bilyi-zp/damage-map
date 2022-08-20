import moment from 'moment';

const state = {
    filters: {
        period: [
            moment().startOf('month').format('YYYY-MM-DD'),
            moment().endOf('month').format('YYYY-MM-DD')
        ],
        region: null
    }
};

export default state;
