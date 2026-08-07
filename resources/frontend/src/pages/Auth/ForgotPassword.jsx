import "../../theme/css/auth.css";

import { Link } from "react-router-dom";

import {
    Mail,
    ArrowLeft
} from "lucide-react";

function ForgotPassword() {

    return (

        <section className="auth">

            <div className="auth-container">

                {/* Left */}

                <div className="auth-left">

                    <div className="auth-overlay">

                        <span className="auth-badge">

                            🔒 Account Recovery

                        </span>

                        <h1>

                            Forgot
                            Your Password?

                        </h1>

                        <p>

                            Don't worry. Enter your registered email and we'll send you a password reset link.

                        </p>

                    </div>

                </div>

                {/* Right */}

                <div className="auth-right">

                    <div className="auth-card">

                        <h2>

                            Reset Password

                        </h2>

                        <p>

                            Enter your email address below.

                        </p>

                        <form>

                            <div className="input-group">

                                <Mail size={18} />

                                <input
                                    type="email"
                                    placeholder="Email Address"
                                />

                            </div>

                            <button
                                type="submit"
                                className="auth-btn"
                            >

                                Send Reset Link

                            </button>

                        </form>

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

export default ForgotPassword;
