import "../../theme/css/auth.css";

import { useState } from "react";
import { Link } from "react-router-dom";
import { User, AtSign, Mail, Phone, Lock, Eye, EyeOff, Loader2 } from "lucide-react";
import frontendApi from "../../shared/frontendApi";
import useAuthSettings, { authText } from "../../shared/authSettings";
import { showDialog } from "../../shared/components/Dialog";
import AuthShell from "../../shared/components/AuthShell";

function Register() {
    const s = useAuthSettings();

    const [form, setForm] = useState({
        name: "",
        username: "",
        email: "",
        phone: "",
        password: "",
        password_confirmation: "",
        agree: false,
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);
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
        if (!form.agree) {
            setErrors({ agree: ["Please read and accept the Terms & Privacy Policy first."] });
            return;
        }
        setLoading(true);
        setErrors({});
        setApiError("");
        try {
            const geo = await captureGeo();
            const res = await frontendApi.post("/register", {
                name: form.name,
                username: form.username,
                email: form.email,
                phone: form.phone,
                password: form.password,
                password_confirmation: form.password_confirmation,
                ...(geo ? { latitude: geo.latitude, longitude: geo.longitude } : {}),
            });
            if (res.data.status) {
                await showDialog({
                    type: "success",
                    title: "Account Created",
                    message: "Your account has been created. Complete your profile and choose whether you want to buy or sell services to unlock your dashboard.",
                    confirmText: "Continue",
                });
                window.location.href = res.data.redirect || "/";
            }
        } catch (err) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors || {});
            } else {
                await showDialog({
                    type: "danger",
                    title: "Registration Failed",
                    message: err.response?.data?.message || "Registration failed. Please try again.",
                    confirmText: "Try Again",
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <AuthShell>
            <div className="auth-card-body">
                <h2>{authText(s, "register.heading", "Create Account")}</h2>
                <p>{authText(s, "register.subheading", "It's free. Complete your profile after sign-up to buy or sell services.")}</p>

                {apiError && <div className="auth-error">{apiError}</div>}

                <form onSubmit={handleSubmit}>
                    <div className="input-group">
                        <User size={18} />
                        <input type="text" name="name" placeholder="Full Name" value={form.name} onChange={handleChange} required />
                    </div>
                    {errors.name && <span className="field-error">{errors.name[0]}</span>}

                    <div className="input-group">
                        <AtSign size={18} />
                        <input type="text" name="username" placeholder="Username" value={form.username} onChange={handleChange} required autoComplete="username" />
                    </div>
                    {errors.username && <span className="field-error">{errors.username[0]}</span>}

                    <div className="input-group">
                        <Mail size={18} />
                        <input type="email" name="email" placeholder="Email Address" value={form.email} onChange={handleChange} required />
                    </div>
                    {errors.email && <span className="field-error">{errors.email[0]}</span>}

                    <div className="input-group">
                        <Phone size={18} />
                        <input type="tel" name="phone" placeholder="Phone Number (optional, add it later)" value={form.phone} onChange={handleChange} />
                    </div>
                    {errors.phone && <span className="field-error">{errors.phone[0]}</span>}

                    <div className="row">
                        <div>
                            <div className="input-group">
                                <Lock size={18} />
                                <input type={showPassword ? "text" : "password"} name="password" placeholder="Password" value={form.password} onChange={handleChange} required />
                                <span className="password-toggle" onClick={() => setShowPassword(!showPassword)}>
                                    {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                                </span>
                            </div>
                            {errors.password && <span className="field-error">{errors.password[0]}</span>}
                        </div>
                        <div>
                            <div className="input-group">
                                <Lock size={18} />
                                <input type={showConfirm ? "text" : "password"} name="password_confirmation" placeholder="Confirm Password" value={form.password_confirmation} onChange={handleChange} required />
                                <span className="password-toggle" onClick={() => setShowConfirm(!showConfirm)}>
                                    {showConfirm ? <EyeOff size={18} /> : <Eye size={18} />}
                                </span>
                            </div>
                        </div>
                    </div>

                    <label className="checkbox">
                        <input type="checkbox" name="agree" checked={form.agree} onChange={handleChange} />
                        <span>I read and agree to the <Link to="/terms-conditions" style={{ color: "#4F46E5", fontWeight: 600 }}>Terms</Link> & <Link to="/privacy-policy" style={{ color: "#4F46E5", fontWeight: 600 }}>Privacy Policy</Link></span>
                    </label>
                    {errors.agree && <span className="field-error">{errors.agree[0]}</span>}

                    <button type="submit" className="auth-btn" disabled={loading}>
                        {loading ? <Loader2 size={18} className="spin" /> : "Create Account"}
                    </button>
                </form>

                <div className="divider"><span>OR</span></div>

                <button
                    type="button"
                    className="google-btn"
                    onClick={() => (window.location.href = "/auth/google/redirect")}
                >
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20" height="20" />
                    Continue with Google
                </button>

                <div className="bottom-link">
                    {authText(s, "register.login_link", "Already have an account?")}{" "}
                    <Link to="/login">{authText(s, "register.login", "Login")}</Link>
                </div>
            </div>
        </AuthShell>
    );
}

export default Register;