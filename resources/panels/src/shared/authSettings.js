import { useEffect, useState } from 'react';
import frontendApi from './frontendApi';

let cache = null;
let promise = null;

export function loadAuthSettings() {
    if (!promise) {
        promise = frontendApi
            .get('/auth-settings')
            .then((res) => {
                cache = res.data && typeof res.data === 'object' ? res.data : {};
                return cache;
            })
            .catch(() => {
                cache = {};
                return cache;
            });
    }
    return promise;
}

export default function useAuthSettings() {
    const [settings, setSettings] = useState(cache || {});

    useEffect(() => {
        let mounted = true;
        loadAuthSettings().then((v) => {
            if (mounted) setSettings(v);
        });
        return () => { mounted = false; };
    }, []);

    return settings;
}

export const authText = (settings, key, fallback) => {
    const value = settings[key];
    return value === undefined || value === null || value === '' ? fallback : value;
};