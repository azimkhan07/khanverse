import "../../theme/css/auth.css";

import { useState } from "react";
import { Link } from "react-router-dom";
import { Mail, Lock, Eye, EyeOff, Loader2 } from "lucide-react";
import frontendApi from "../../shared/frontendApi";
import useAuthSettings, { authText } from "../../shared/authSettings";
import AuthShell from "../../shared/components/AuthShell";

function Login() {
    const s = useAuthSettings();
    const googleEnabled = s.google_enabled !== false;
    const [form, setForm] = useState({ email: "", password: "", remember: false });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [apiError, setApiError] = useState("");

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setForm((prev) => ({ ...prev, [name]: type === "checkbox" ? checked : value }));
        if (errors[name]) setErrors((prev) => ({ ...prev, [name]: "" }));
        if (apiError) setApiError("");
    };

    const captureGeo = async () => {
        if (typeof navigator === "undefined" || !navigator.geolocation) return null;
        try {
            const pos = await Promise.race([
                new Promise((resolve, reject) =>
                    navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 4000, maximumAge: 300000 })
                ),
                new Promise((resolve) => setTimeout(() => resolve(null), 4500)),
            ]);
            return pos ? { latitude: pos.coords.latitude, longitude: pos.coords.longitude } : null;
        } catch {
            return null;
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});
        setApiError("");

        try {
            const geo = await captureGeo();
            const res = await frontendApi.post("/login", {
                email: form.email,
                password: form.password,
                remember: form.remember,
                ...(geo ? { latitude: geo.latitude, longitude: geo.longitude } : {}),
            });

            if (res.data.status) {
                window.location.href = res.data.redirect || "/";
            }
        } catch (err) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors || {});
            } else {
                setApiError(err.response?.data?.message || "Login failed. Please try again.");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <AuthShell>
            <div className="auth-card-body">
                <h2>{authText(s, "login.heading", "Welcome Back")}</h2>
                <p>{authText(s, "login.subheading", "Login to continue your journey.")}</p>

                {apiError && <div className="auth-error">{apiError}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="input-group">
                        <Mail size={18} />
                        <input
                            type="text"
                            name="email"
                            placeholder="Email or Username"
                            value={form.email}
                            onChange={handleChange}
                            required
                            autoComplete="username"
                        />
                    </div>
                    {errors.email && <span className="field-error">{errors.email[0]}</span>}

                    <div className="input-group">
                        <Lock size={18} />
                        <input
                            type={showPassword ? "text" : "password"}
                            name="password"
                            placeholder="Password"
                            value={form.password}
                            onChange={handleChange}
                            required
                        />
                        <span className="password-toggle" onClick={() => setShowPassword(!showPassword)}>
                            {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                        </span>
                    </div>
                    {errors.password && <span className="field-error">{errors.password[0]}</span>}

                    <div className="auth-options">
                        <label>
                            <input
                                type="checkbox"
                                name="remember"
                                checked={form.remember}
                                onChange={handleChange}
                            />
                            {authText(s, "login.remember", "Remember Me")}
                        </label>
                        <Link to="/forgot-password">{authText(s, "login.forgot_link", "Forgot Password?")}</Link>
                    </div>

                    <button type="submit" className="auth-btn" disabled={loading}>
                        {loading ? <Loader2 size={18} className="spin" /> : authText(s, "login.button", "Login")}
                    </button>
                </form>

                <div className="divider"><span>OR</span></div>

                {googleEnabled ? (
                    <button
                        type="button"
                        className="google-btn"
                        onClick={() => (window.location.href = "/auth/google/redirect")}
                    >
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20" height="20" />
                        Continue with Google
                    </button>
                ) : (
                    <button type="button" className="google-btn" disabled title="Google login is not configured yet">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20" height="20" />
                        Google login not configured
                    </button>
                )}

                <div className="bottom-link">
                    {authText(s, "login.new_here", "Don't have an account?")}{" "}
                    <Link to="/register">{authText(s, "login.create_account", "Register")}</Link>
                </div>
            </div>
        </AuthShell>
    );
}

export default Login;
