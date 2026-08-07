import "../../theme/css/auth.css";

import { Link } from "react-router-dom";

import {
    MailCheck,
    RefreshCw,
    ArrowLeft
} from "lucide-react";

function VerifyEmail() {

    return (

        <section className="auth">

            <div className="auth-container">

                {/* Left */}

                <div className="auth-left">

                    <div className="auth-overlay">

                        <span className="auth-badge">

                            📧 Verify Your Email

                        </span>

                        <h1>

                            One More
                            Step Left

                        </h1>

                        <p>

                            We've sent a verification email to your registered email address.
                            Please verify your account before continuing.

                        </p>

                    </div>

                </div>

                {/* Right */}

                <div className="auth-right">

                    <div className="auth-card verify-card">

                        <div className="verify-icon">

                            <MailCheck size={70} />

                        </div>

                        <h2>

                            Check Your Inbox

                        </h2>

                        <p>

                            Click the verification link we've sent to your email.

                        </p>

                        <button
                            type="button"
                            className="auth-btn"
                        >

                            <RefreshCw size={18} />

                            Resend Verification Email

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
