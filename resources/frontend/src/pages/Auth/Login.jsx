import "../../theme/css/auth.css";

import { Link } from "react-router-dom";

import { Mail, Lock, Eye } from "lucide-react";

function Login() {
    return (
        <section className="auth">
            <div className="auth-container">
                {/* Left Side */}

                <div className="auth-left">
                    <div className="auth-overlay">
                        <span className="auth-badge">
                            🚀 Welcome to KhanVerse
                        </span>

                        <h1>Work. Hire. Grow.</h1>

                        <p>
                            Join thousands of freelancers and businesses
                            building the future together.
                        </p>
                    </div>
                </div>

                {/* Right Side */}

                <div className="auth-right">
                    <div className="auth-card">
                        <h2>Welcome Back 👋</h2>

                        <p>Login to continue your journey.</p>

                        <form>
                            <div className="input-group">
                                <Mail size={18} />

                                <input
                                    type="email"
                                    placeholder="Email Address"
                                />
                            </div>

                            <div className="input-group">
                                <Lock size={18} />

                                <input type="password" placeholder="Password" />

                                <Eye size={18} className="password-toggle" />
                            </div>

                            <div className="auth-options">
                                <label>
                                    <input type="checkbox" />
                                    Remember Me
                                </label>

                                <Link to="/forgot-password">
                                    Forgot Password?
                                </Link>
                            </div>

                            <button type="submit" className="auth-btn">
                                Login
                            </button>
                        </form>

                        <div className="divider">
                            <span>OR</span>
                        </div>

                        <button className="google-btn">
                            <img
                                src="https://www.svgrepo.com/show/475656/google-color.svg"
                                alt="Google"
                                width="20"
                                height="20"
                            />
                            Continue with Google
                        </button>

                        <div className="bottom-link">
                            Don't have an account?
                            <Link to="/register">Register</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default Login;
