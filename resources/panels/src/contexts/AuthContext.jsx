import { createContext, useContext, useState, useEffect, useCallback } from "react";
import frontendApi from "../shared/frontendApi";

const PROMPT_KEY_PREFIX = "profile_prompt_";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const fetchUser = useCallback(async () => {
        try {
            const res = await frontendApi.get("/user");
            if (res.data?.user) {
                setUser(res.data.user);
            } else {
                setUser(null);
            }
        } catch {
            setUser(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchUser();
    }, [fetchUser]);

    const logout = useCallback(async () => {
        try {
            await frontendApi.post("/logout");
        } catch {
            // silent
        } finally {
            try {
                Object.keys(sessionStorage).forEach((k) => {
                    if (k.startsWith(PROMPT_KEY_PREFIX)) sessionStorage.removeItem(k);
                });
            } catch {
                // silent
            }
            setUser(null);
            window.location.href = "/";
        }
    }, []);

    return (
        <AuthContext.Provider value={{ user, loading, setUser, logout, fetchUser }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        return { user: null, loading: false, setUser: () => {}, logout: () => {}, fetchUser: () => {} };
    }
    return context;
}
