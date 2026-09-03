import "../../theme/css/auth.css";

import { useState } from "react";
import { Link, useParams } from "react-router-dom";
import { Mail, Lock, Eye, EyeOff, CheckCircle, Loader2 } from "lucide-react";
import frontendApi from "../../shared/frontendApi";
import useAuthSettings, { authText } from "../../shared/authSettings";

function ResetPassword() {
    const { token } = useParams();
    const s = useAuthSettings();
    const [form, setForm] = useState({ email: "", password: "", password_confirmation: "" });
    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState(false);
    const [errors, setErrors] = useState({});
    const [apiError, setApiError] = useState("");

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
        if (errors[name]) setErrors((prev) => ({ ...prev, [name]: "" }));
        if (apiError) setApiError("");
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});
        setApiError("");

        try {
            const res = await frontendApi.post("/reset-password", {
                token,
                email: form.email,
                password: form.password,
                password_confirmation: form.password_confirmation,
            });

            if (res.data.status) {
                setSuccess(true);
            } else {
                setApiError(res.data.message || "Unable to reset password");
            }
        } catch (err) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors || {});
            } else {
                setApiError(err.response?.data?.message || "Something went wrong");
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
                        <span className="auth-badge">{authText(s, "reset.badge", "Secure Password")}</span>
                        <h1>{authText(s, "reset.hero_heading", "Create New Password")}</h1>
                        <p>{authText(s, "reset.hero_text", "Your new password should be strong and different from your previous password.")}</p>
                    </div>
                </div>

                <div className="auth-right">
                    <div className="auth-card">
                        {success ? (
                            <>
                                <div className="verify-icon"><CheckCircle size={70} /></div>
                                <h2>{authText(s, "reset.sent_heading", "Password Reset!")}</h2>
                                <p>Your password has been reset successfully. You can now login with your new password.</p>
                            </>
                        ) : (
                            <>
                                <h2>{authText(s, "reset.heading", "Reset Password")}</h2>
                                <p>{authText(s, "reset.subheading", "Enter your new password below.")}</p>

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
                                            placeholder="New Password"
                                            value={form.password}
                                            onChange={handleChange}
                                            required
                                        />
                                        <span className="password-toggle" onClick={() => setShowPassword(!showPassword)}>
                                            {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                                        </span>
                                    </div>
                                    {errors.password && <span className="field-error">{errors.password[0]}</span>}

                                    <div className="input-group">
                                        <Lock size={18} />
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            name="password_confirmation"
                                            placeholder="Confirm Password"
                                            value={form.password_confirmation}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>

                                    <button type="submit" className="auth-btn" disabled={loading}>
                                        {loading ? <Loader2 size={18} className="spin" /> : <><CheckCircle size={18} /> {authText(s, "reset.button", "Reset Password")}</>}
                                    </button>
                                </form>
                            </>
                        )}

                        <div className="bottom-link">
                            <Link to="/login">Back to Login</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default ResetPassword;
