import "../../theme/css/auth.css";

import { useState } from "react";
import { Link } from "react-router-dom";
import { Mail, ArrowLeft, Loader2, CheckCircle } from "lucide-react";
import frontendApi from "../../shared/frontendApi";
import useAuthSettings, { authText } from "../../shared/authSettings";
import AuthShell from "../../shared/components/AuthShell";

function ForgotPassword() {
    const s = useAuthSettings();
    const [email, setEmail] = useState("");
    const [loading, setLoading] = useState(false);
    const [sent, setSent] = useState(false);
    const [error, setError] = useState("");

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError("");

        try {
            const res = await frontendApi.post("/forgot-password", { email });
            if (res.data.status) {
                setSent(true);
            } else {
                setError(res.data.message || "Unable to send reset link");
            }
        } catch (err) {
            if (err.response?.status === 422) {
                setError(err.response.data.errors?.email?.[0] || "Invalid email address");
            } else {
                setError(err.response?.data?.message || "Something went wrong");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <AuthShell>
            <div className="auth-card-body">
                {sent ? (
                    <>
                        <div className="verify-icon"><CheckCircle size={70} /></div>
                        <h2 style={{ textAlign: "center" }}>{authText(s, "forgot.sent_heading", "Check Your Email")}</h2>
                        <p style={{ textAlign: "center" }}>We've sent a password reset link to <strong>{email}</strong>. Please check your inbox.</p>
                    </>
                ) : (
                    <>
                        <h2>{authText(s, "forgot.heading", "Reset Password")}</h2>
                        <p>{authText(s, "forgot.subheading", "Enter your email address below.")}</p>

                        {error && <div className="auth-error">{error}</div>}

                        <form onSubmit={handleSubmit}>
                            <div className="input-group">
                                <Mail size={18} />
                                <input
                                    type="email"
                                    placeholder="Email Address"
                                    value={email}
                                    onChange={(e) => { setEmail(e.target.value); setError(""); }}
                                    required
                                />
                            </div>

                            <button type="submit" className="auth-btn" disabled={loading}>
                                {loading ? <Loader2 size={18} className="spin" /> : authText(s, "forgot.button", "Send Reset Link")}
                            </button>
                        </form>
                    </>
                )}

                <div className="bottom-link">
                    <Link to="/login">
                        <ArrowLeft size={16} />
                        {authText(s, "forgot.back", "Back to Login")}
                    </Link>
                </div>
            </div>
        </AuthShell>
    );
}

export default ForgotPassword;
