import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE_URL = __ENV.BASE_URL ?? 'http://php';

export const options = {
    vus: 30,
    duration: '30s',

    thresholds: {
        http_req_failed: ['rate<0.01'],
        http_req_duration: ['p(95)<600'],
        checks: ['rate>0.99'],
    },
};

export default function () {
    const response = http.get(`${BASE_URL}/`, {
        tags: {
            name: 'homepage',
        },
    });

    check(response, {
        'HTTP 200': (r) => r.status === 200,
        'Contenu HTML': (r) =>
            r.headers['Content-Type']?.includes('text/html'),
        'Temps < 500 ms': (r) => r.timings.duration < 500,
    });

    sleep(1);
}
