export function useStatusMessage(status) {
    switch (status) {
        case 503:
            return 'In Maintenance';
        case 500:
            return 'Server Error';
        case 429:
            return 'Too Many Requests';
        case 419:
            return 'Session Expired';
        case 404:
            return 'Page Not Found';
        case 403:
            return 'Forbidden';
        case 402:
            return 'Payment Required';
        case 401:
            return 'Unauthorized';
        default:
            return 'Sorry, something went wrong unexpectedly.';
    }
}
