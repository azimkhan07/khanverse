import { createContext, useContext, useState, useEffect, useCallback } from "react";
import frontendApi from "../shared/frontendApi";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const fetchUser = useCallback(async () => {
        try {
            const res = await frontendApi.get("/user");
            if (res.data) {
                setUser(res.data);
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
            setUser(null);
            window.location.href = "/login";
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
