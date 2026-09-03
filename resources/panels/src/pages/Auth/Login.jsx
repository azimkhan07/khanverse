import "../../theme/css/auth.css";

import { useState } from "react";
import { Link } from "react-router-dom";
import { Mail, Lock, Eye, EyeOff, Loader2 } from "lucide-react";
import frontendApi from "../../shared/frontendApi";
import useAuthSettings, { authText } from "../../shared/authSettings";

function Login() {
    const s = useAuthSettings();
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

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});
        setApiError("");

        try {
            const res = await frontendApi.post("/login", {
                email: form.email,
                password: form.password,
                remember: form.remember,
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
        <section className="auth">
            <div className="auth-container">
                <div className="auth-left">
                    <div className="auth-overlay">
                        <span className="auth-badge">{authText(s, "login.badge", "Welcome to KhanVerse")}</span>
                        <h1>{authText(s, "login.hero_heading", "Work. Hire. Grow.")}</h1>
                        <p>{authText(s, "login.hero_text", "Join thousands of freelancers and businesses building the future together.")}</p>
                    </div>
                </div>

                <div className="auth-right">
                    <div className="auth-card">
                        <h2>{authText(s, "login.heading", "Welcome Back")}</h2>
                        <p>{authText(s, "login.subheading", "Login to continue your journey.")}</p>

                        {apiError && <div className="auth-error">{apiError}</div>}

                        <form onSubmit={handleSubmit}>
                            <div className="input-group">
                                <Mail size={18} />
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Email Address"
                                    value={form.email}
                                    onChange={handleChange}
                                    required
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

                        <button className="google-btn" disabled>
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20" height="20" />
                            Continue with Google
                        </button>

                        <div className="bottom-link">
                            {authText(s, "login.new_here", "Don't have an account?")}{" "}
                            <Link to="/register">{authText(s, "login.create_account", "Register")}</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default Login;
