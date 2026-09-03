import "../../theme/css/auth.css";

import { useState } from "react";
import { Link } from "react-router-dom";
import { MailCheck, RefreshCw, ArrowLeft, Loader2 } from "lucide-react";
import frontendApi from "../../shared/frontendApi";
import useAuthSettings, { authText } from "../../shared/authSettings";

function VerifyEmail() {
    const s = useAuthSettings();
    const [loading, setLoading] = useState(false);
    const [sent, setSent] = useState(false);
    const [error, setError] = useState("");

    const handleResend = async () => {
        setLoading(true);
        setError("");

        try {
            await frontendApi.post("/verify-email");
            setSent(true);
        } catch (err) {
            if (err.response?.status === 401) {
                setError("Please login again to verify your email");
            } else {
                setError(err.response?.data?.message || "Failed to resend verification email");
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
                        <span className="auth-badge">{authText(s, "verify.badge", "Verify Your Email")}</span>
                        <h1>{authText(s, "verify.hero_heading", "One More Step Left")}</h1>
                        <p>{authText(s, "verify.hero_text", "We've sent a verification email to your registered email address. Please verify your account before continuing.")}</p>
                    </div>
                </div>

                <div className="auth-right">
                    <div className="auth-card verify-card">
                        <div className="verify-icon">
                            <MailCheck size={70} />
                        </div>

                        <h2>{authText(s, "verify.heading", "Check Your Inbox")}</h2>
                        <p>{authText(s, "verify.subheading", "Click the verification link we've sent to your email.")}</p>

                        {error && <div className="auth-error">{error}</div>}
                        {sent && <div className="auth-success">Verification email sent! Check your inbox.</div>}

                        <button
                            type="button"
                            className="auth-btn"
                            onClick={handleResend}
                            disabled={loading}
                        >
                            {loading ? <Loader2 size={18} className="spin" /> : <><RefreshCw size={18} /> {authText(s, "verify.button", "Resend Verification Email")}</>}
                        </button>

                        <div className="bottom-link">
                            <Link to="/login">
                                <ArrowLeft size={16} />
                                Back to Login
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default VerifyEmail;
