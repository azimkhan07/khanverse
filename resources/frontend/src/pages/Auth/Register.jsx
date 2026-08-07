import "../../theme/css/auth.css";

import { Link } from "react-router-dom";

import { User, Mail, Phone, Lock, Eye } from "lucide-react";

function Register() {
    return (
        <section className="auth">
            <div className="auth-container">
                {/* Left */}

                <div className="auth-left">
                    <div className="auth-overlay">
                        <span className="auth-badge">🚀 Join KhanVerse</span>

                        <h1>Start Your Journey Today</h1>

                        <p>
                            Create your free account and connect with thousands
                            of freelancers and clients worldwide.
                        </p>
                    </div>
                </div>

                {/* Right */}

                <div className="auth-right">
                    <div className="auth-card">
                        <h2>Create Account</h2>

                        <p>It's free and takes less than a minute.</p>

                        <form>
                            <div className="row">
                                <div className="input-group">
                                    <User size={18} />

                                    <input
                                        type="text"
                                        placeholder="First Name"
                                    />
                                </div>

                                <div className="input-group">
                                    <User size={18} />

                                    <input
                                        type="text"
                                        placeholder="Last Name"
                                    />
                                </div>
                            </div>

                            <div className="input-group">
                                <User size={18} />

                                <input type="text" placeholder="Username" />
                            </div>

                            <div className="input-group">
                                <Mail size={18} />

                                <input
                                    type="email"
                                    placeholder="Email Address"
                                />
                            </div>

                            <div className="input-group">
                                <Phone size={18} />

                                <input
                                    type="text"
                                    placeholder="Mobile Number"
                                />
                            </div>

                            <div className="input-group">
                                <Lock size={18} />

                                <input type="password" placeholder="Password" />

                                <Eye size={18} className="password-toggle" />
                            </div>

                            <div className="input-group">
                                <Lock size={18} />

                                <input
                                    type="password"
                                    placeholder="Confirm Password"
                                />

                                <Eye size={18} className="password-toggle" />
                            </div>

                            <label className="checkbox">
                                <input type="checkbox" />I agree to the Terms &
                                Privacy Policy
                            </label>

                            <button type="submit" className="auth-btn">
                                Create Account
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
                            Already have an account?
                            <Link to="/login">Login</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default Register;
