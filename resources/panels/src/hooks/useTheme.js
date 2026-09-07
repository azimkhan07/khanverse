import { useState, useEffect, useCallback } from 'react';

const STORAGE_PREFIX = 'skillnest-theme';

export function useTheme(role = 'public', userId = null) {
    const key = userId
        ? `${STORAGE_PREFIX}-${role}-${userId}`
        : `${STORAGE_PREFIX}-${role}`;

    const [theme, setTheme] = useState(() => {
        if (typeof window === 'undefined') return 'light';
        return localStorage.getItem(key) || 'light';
    });

    useEffect(() => {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(key, theme);
    }, [theme, key]);

    const toggleTheme = useCallback(() => {
        setTheme(prev => prev === 'light' ? 'dark' : 'light');
    }, []);

    const isDark = theme === 'dark';

    return { theme, isDark, toggleTheme, setTheme };
}
