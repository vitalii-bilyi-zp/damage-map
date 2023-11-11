import moment from 'moment';
import Cookies from 'js-cookie';

const state = {
    filters: {
        period: [
            moment().startOf('month').format('YYYY-MM-DD'),
            moment().format('YYYY-MM-DD')
        ],
        region: null,
        dimensionType: 'objects_number',
    },
    token: Cookies.get('access_token') || null,
    currentUser: null,
};

export default state;
