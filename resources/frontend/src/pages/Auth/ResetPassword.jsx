import "../../theme/css/auth.css";

import { Link } from "react-router-dom";

import {
    Lock,
    Eye,
    CheckCircle
} from "lucide-react";

function ResetPassword() {

    return (

        <section className="auth">

            <div className="auth-container">

                {/* Left */}

                <div className="auth-left">

                    <div className="auth-overlay">

                        <span className="auth-badge">

                            🔑 Secure Password

                        </span>

                        <h1>

                            Create
                            New Password

                        </h1>

                        <p>

                            Your new password should be strong and different from your previous password.

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

                            Enter your new password below.

                        </p>

                        <form>

                            <div className="input-group">

                                <Lock size={18} />

                                <input
                                    type="password"
                                    placeholder="New Password"
                                />

                                <Eye
                                    size={18}
                                    className="password-toggle"
                                />

                            </div>

                            <div className="input-group">

                                <Lock size={18} />

                                <input
                                    type="password"
                                    placeholder="Confirm Password"
                                />

                                <Eye
                                    size={18}
                                    className="password-toggle"
                                />

                            </div>

                            <button
                                type="submit"
                                className="auth-btn"
                            >

                                <CheckCircle size={18} />

                                Reset Password

                            </button>

                        </form>

                        <div className="bottom-link">

                            <Link to="/login">

                                Back to Login

                            </Link>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    );

}

export default ResetPassword;
