import "../../theme/css/auth.css";

import { useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { User, Mail, Phone, Lock, Eye, EyeOff, Loader2, Briefcase, Building2 } from "lucide-react";
import frontendApi from "../../shared/frontendApi";
import useAuthSettings, { authText } from "../../shared/authSettings";
import { showDialog } from "../../shared/components/Dialog";

const ROLE_OPTIONS = [
    { value: "buyer", label: "Buy / Hire" },
    { value: "seller", label: "Sell / Freelance" },
];

function Register() {
    const [searchParams] = useSearchParams();
    const s = useAuthSettings();
    const roleParam = searchParams.get("role") || "buyer";

    const [role, setRole] = useState(roleParam);
    const [tab, setTab] = useState("account");
    const [form, setForm] = useState({
        name: "",
        username: "",
        email: "",
        phone: "",
        password: "",
        password_confirmation: "",
        agree: false,
        // Buyer
        company_name: "",
        // Seller
        bio: "",
        skills: "",
        hourly_rate: "",
        experience_level: "junior",
        available_for_work: true,
        // Shared (profile)
        gender: "",
        dob: "",
        address: "",
        postal_code: "",
        country: "",
        city: "",
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

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (!form.agree) {
            setErrors({ agree: ["You must agree to the terms"] });
            return;
        }
        if (role !== "buyer" && role !== "seller") {
            setErrors({ role: ["Please choose whether you want to buy or sell."] });
            return;
        }
        setLoading(true);
        setErrors({});
        setApiError("");
        try {
            const res = await frontendApi.post("/register", {
                name: form.name,
                username: form.username,
                email: form.email,
                phone: form.phone,
                password: form.password,
                password_confirmation: form.password_confirmation,
                role,
                // Buyer
                company_name: form.company_name,
                // Seller
                bio: form.bio,
                skills: form.skills,
                hourly_rate: form.hourly_rate,
                experience_level: form.experience_level,
                // Shared
                gender: form.gender,
                dob: form.dob,
                address: form.address,
                postal_code: form.postal_code,
                country: form.country,
                city: form.city,
            });
            if (res.data.status) {
                await showDialog({
                    type: "success",
                    title: "Account Created",
                    message: "Your account has been created successfully. Taking you to your dashboard...",
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
        <section className="auth">
            <div className="auth-container">
                <div className="auth-left">
                    <div className="auth-overlay">
                        <span className="auth-badge">{role === "seller" ? "Start Selling" : role === "buyer" ? "Start Buying" : authText(s, "register.badge", "Join KhanVerse")}</span>
                        <h1>{role === "seller" ? "Turn Your Skills Into Income" : role === "buyer" ? "Hire Top Talent Today" : authText(s, "register.hero_heading", "Start Your Journey Today")}</h1>
                        <p>{authText(s, "register.hero_text", "Create your free account and connect with thousands of freelancers and clients worldwide.")}</p>
                    </div>
                </div>

                <div className="auth-right">
                    <div className="auth-card auth-card--wide">
                        <h2>{role === "seller" ? "Become a Seller" : role === "buyer" ? "Become a Buyer" : authText(s, "register.heading", "Create Account")}</h2>
                        <p>{authText(s, "register.subheading", "It's free and takes less than a minute.")}</p>

                        <div className="auth-role-tabs">
                            {ROLE_OPTIONS.map((opt) => (
                                <button
                                    key={opt.value}
                                    type="button"
                                    className={`auth-role-tab ${role === opt.value ? "active" : ""}`}
                                    onClick={() => { setRole(opt.value); setApiError(""); }}
                                >
                                    {opt.value === "seller" ? <Briefcase size={16} /> : <Building2 size={16} />}
                                    {opt.label}
                                </button>
                            ))}
                        </div>
                        {errors.role && <span className="field-error">{errors.role[0]}</span>}

                        <div className="auth-tabs">
                            <button type="button" className={`auth-tab ${tab === "account" ? "active" : ""}`} onClick={() => setTab("account")}>1. Account</button>
                            <button type="button" className={`auth-tab ${tab === "details" ? "active" : ""}`} onClick={() => setTab("details")}>2. {role === "seller" ? "Seller Details" : "Buyer Details"}</button>
                        </div>

                        {apiError && <div className="auth-error">{apiError}</div>}

                        <form onSubmit={handleSubmit}>
                            {tab === "account" && (
                                <>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="name" placeholder="Full Name" value={form.name} onChange={handleChange} required />
                                            </div>
                                            {errors.name && <span className="field-error">{errors.name[0]}</span>}
                                        </div>
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="username" placeholder="Username" value={form.username} onChange={handleChange} required />
                                            </div>
                                            {errors.username && <span className="field-error">{errors.username[0]}</span>}
                                        </div>
                                    </div>

                                    <div className="input-group">
                                        <Mail size={18} />
                                        <input type="email" name="email" placeholder="Email Address" value={form.email} onChange={handleChange} required />
                                    </div>
                                    {errors.email && <span className="field-error">{errors.email[0]}</span>}

                                    <div className="input-group">
                                        <Phone size={18} />
                                        <input type="tel" name="phone" placeholder="Phone Number (optional since you may add it later)" value={form.phone} onChange={handleChange} />
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

                                    <div className="auth-tab-footer">
                                        <button type="button" className="auth-btn auth-btn--ghost" onClick={() => setTab("details")}>
                                            Next: {role === "seller" ? "Seller Details" : "Buyer Details"}
                                        </button>
                                    </div>
                                </>
                            )}

                            {tab === "details" && role === "buyer" && (
                                <>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <Building2 size={18} />
                                                <input type="text" name="company_name" placeholder="Company Name (optional)" value={form.company_name} onChange={handleChange} />
                                            </div>
                                            {errors.company_name && <span className="field-error">{errors.company_name[0]}</span>}
                                        </div>
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="city" placeholder="City" value={form.city} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="country" placeholder="Country" value={form.country} onChange={handleChange} />
                                            </div>
                                        </div>
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="postal_code" placeholder="Postal Code" value={form.postal_code} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="input-group">
                                        <User size={18} />
                                        <input type="text" name="address" placeholder="Address" value={form.address} onChange={handleChange} />
                                    </div>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <select name="gender" value={form.gender} onChange={handleChange}>
                                                    <option value="">Gender (optional)</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="date" name="dob" placeholder="Date of Birth" value={form.dob} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>
                                </>
                            )}

                            {tab === "details" && role === "seller" && (
                                <>
                                    <div className="input-group">
                                        <User size={18} />
                                        <textarea name="bio" placeholder="Tell buyers about yourself (bio)" value={form.bio} onChange={handleChange} rows={3} />
                                    </div>
                                    <div className="input-group">
                                        <User size={18} />
                                        <input type="text" name="skills" placeholder="Skills (comma separated, e.g. Logo Design, SEO)" value={form.skills} onChange={handleChange} />
                                    </div>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="number" step="0.01" min="0" name="hourly_rate" placeholder="Hourly Rate (₹)" value={form.hourly_rate} onChange={handleChange} />
                                            </div>
                                        </div>
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <select name="experience_level" value={form.experience_level} onChange={handleChange}>
                                                    <option value="junior">Junior</option>
                                                    <option value="mid">Mid-level</option>
                                                    <option value="senior">Senior</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="city" placeholder="City" value={form.city} onChange={handleChange} />
                                            </div>
                                        </div>
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="country" placeholder="Country" value={form.country} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="text" name="postal_code" placeholder="Postal Code" value={form.postal_code} onChange={handleChange} />
                                            </div>
                                        </div>
                                        <div>
                                            <label className="checkbox auth-check">
                                                <input type="checkbox" name="available_for_work" checked={form.available_for_work} onChange={handleChange} />
                                                Available for work
                                            </label>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <select name="gender" value={form.gender} onChange={handleChange}>
                                                    <option value="">Gender (optional)</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <div className="input-group">
                                                <User size={18} />
                                                <input type="date" name="dob" placeholder="Date of Birth" value={form.dob} onChange={handleChange} />
                                            </div>
                                        </div>
                                    </div>
                                </>
                            )}

                            {tab === "details" && (
                                <>
                                    <label className="checkbox">
                                        <input type="checkbox" name="agree" checked={form.agree} onChange={handleChange} />
                                        I agree to the <Link to="/terms-conditions" style={{ color: "#4F46E5", fontWeight: 600 }}>Terms</Link> & <Link to="/privacy-policy" style={{ color: "#4F46E5", fontWeight: 600 }}>Privacy Policy</Link>
                                    </label>
                                    {errors.agree && <span className="field-error">{errors.agree[0]}</span>}

                                    <button type="submit" className="auth-btn" disabled={loading}>
                                        {loading ? <Loader2 size={18} className="spin" /> : "Create Account & Finish"}
                                    </button>
                                    <div className="auth-tab-footer">
                                        <button type="button" className="auth-btn auth-btn--ghost" onClick={() => setTab("account")}>
                                            Back to Account
                                        </button>
                                    </div>
                                </>
                            )}
                        </form>

                        <div className="divider"><span>OR</span></div>

                        <button className="google-btn" disabled>
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20" height="20" />
                            Continue with Google
                        </button>

                        <div className="bottom-link">
                            {authText(s, "register.login_link", "Already have an account?")}{" "}
                            <Link to="/login">{authText(s, "register.login", "Login")}</Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

export default Register;
