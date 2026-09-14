import { useEffect, useState } from "react";
import { Link, Navigate } from "react-router-dom";
import { Loader2, Save, Camera, Shield, Lock, User, Briefcase, ArrowRight } from "lucide-react";
import { useAuth } from "../../contexts/AuthContext";
import frontendApi from "../../shared/frontendApi";
import { showDialog } from "../../shared/components/Dialog";
import LocationFields from "../../shared/components/LocationFields";

function AccountSettings() {
    const { user, loading, fetchUser } = useAuth();
    const [form, setForm] = useState({ name: "", username: "", phone: "", address: "" });
    const [location, setLocation] = useState({ country_id: null, state_id: null, city_id: null });
    const [locationLabels, setLocationLabels] = useState({ state: "", city: "" });
    const [avatar, setAvatar] = useState(null);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");
    const [pwd, setPwd] = useState({ current_password: "", password: "", password_confirmation: "" });
    const [pwdSaving, setPwdSaving] = useState(false);
    const [pwdErrors, setPwdErrors] = useState({});

    useEffect(() => {
        if (!user) return;
        frontendApi.get("/account/settings").then(({ data }) => {
            const p = data.profile || {};
            const prof = p.profile || {};
            setForm({
                name: p.name || user.name || "",
                username: user.username || "",
                phone: prof.phone || p.phone || user.phone || "",
                address: prof.address || "",
            });
            setLocation({
                country_id: prof.country_id || null,
                state_id: prof.state_id || null,
                city_id: prof.city_id || null,
            });
            setLocationLabels({ state: prof.state || "", city: prof.city || "" });
        }).catch(() => {});
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [user?.id]);

    if (loading) {
        return (
            <div style={{ minHeight: "70vh", display: "flex", alignItems: "center", justifyContent: "center" }}>
                <Loader2 size={28} className="spin" style={{ color: "var(--accent,#4F46E5)" }} />
            </div>
        );
    }

    if (!user) return <Navigate to="/login" replace />;

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
        if (error) setError("");
    };

    const submit = async (e) => {
        e.preventDefault();
        setSaving(true);
        setError("");
        try {
            const fd = new FormData();
            Object.entries(form).forEach(([k, v]) => fd.append(k, String(v == null ? "" : v)));
            fd.append("country_id", location.country_id || "");
            fd.append("state_id", location.state_id || "");
            fd.append("city_id", location.city_id || "");
            if (avatar) fd.append("avatar", avatar);
            const res = await frontendApi.post("/account/settings", fd, { headers: { "Content-Type": "multipart/form-data" } });
            await fetchUser();
            await showDialog({ type: "success", title: "Settings Saved", message: res.data?.message || "Your account settings have been saved.", confirmText: "Great" });
        } catch (err) {
            setError(err.response?.data?.message || "Could not save settings. Please try again.");
        } finally {
            setSaving(false);
        }
    };

    const changePassword = async (e) => {
        e.preventDefault();
        setPwdSaving(true);
        setPwdErrors({});
        try {
            const res = await frontendApi.post("/account/settings/password", pwd);
            setPwd({ current_password: "", password: "", password_confirmation: "" });
            await showDialog({ type: "success", title: "Password Updated", message: res.data?.message || "Your password has been changed.", confirmText: "OK" });
        } catch (err) {
            if (err.response?.status === 422) setPwdErrors(err.response.data.errors || {});
            else setPwdErrors({ form: [err.response?.data?.message || "Could not update password."] });
        } finally {
            setPwdSaving(false);
        }
    };

    const isPlainUser = user.role === "user";

    return (
        <div className="account-settings-page">
            <style>{`
                .account-settings-page { background: var(--bg-body,#F8FAFC); min-height: 75vh; padding: 48px 24px 72px; }
                .as-container { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
                .as-card { background: var(--bg-card,#fff); border: 1px solid var(--border-color,#E2E8F0); border-radius: 22px; padding: 26px 28px; box-shadow: var(--shadow-sm,0 1px 3px rgba(0,0,0,.06)); }
                .as-card h2 { font-size: 18px; font-weight: 800; margin: 0 0 4px; display: flex; align-items: center; gap: 8px; color: var(--text-primary,#0F172A); }
                .as-card .sub { color: var(--text-muted,#64748B); font-size: 13px; margin-bottom: 20px; }
                .as-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
                .as-field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary,#475569); }
                .as-input { width: 100%; padding: 11px 14px; border-radius: 12px; border: 1.5px solid var(--border-color,#E2E8F0); background: var(--bg-input,#fff); font-size: 14px; color: var(--text-primary,#0F172A); outline: none; font-family: inherit; }
                .as-input:focus { border-color: var(--accent,#4F46E5); box-shadow: 0 0 0 3px var(--accent-glow,rgba(79,70,229,.1)); }
                .as-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 18px; }
                .as-avatar-row { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
                .as-avatar { width: 72px; height: 72px; border-radius: 18px; object-fit: cover; border: 3px solid var(--border-color,#E2E8F0); background: var(--bg-badge,#F1F5F9); }
                .as-save { display: inline-flex; align-items: center; gap: 8px; padding: 11px 24px; border: none; border-radius: 12px; background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; box-shadow: 0 6px 18px rgba(79,70,229,.28); }
                .as-save:disabled { opacity: .6; cursor: not-allowed; }
                .as-error { color: var(--danger,#EF4444); font-size: 13px; font-weight: 600; }
                .as-role-box { background: var(--accent-light,rgba(79,70,229,.06)); border: 1px solid var(--border-color,#E2E8F0); border-radius: 16px; padding: 18px; }
                .as-role-box h3 { font-size: 15px; font-weight: 800; margin: 0 0 6px; display: flex; align-items: center; gap: 7px; }
                .as-role-box p { color: var(--text-muted,#64748B); font-size: 13px; margin: 0 0 14px; line-height: 1.5; }
                .as-role-btns { display: flex; gap: 12px; flex-wrap: wrap; }
                .as-role-btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; border-radius: 12px; border: 1.5px solid var(--border-color,#E2E8F0); background: var(--bg-card,#fff); color: var(--text-primary,#0F172A); font-size: 14px; font-weight: 700; text-decoration: none; cursor: pointer; font-family: inherit; }
                .as-role-btn.primary { background: linear-gradient(135deg,#4F46E5,#7C3AED); border: none; color: #fff; box-shadow: 0 6px 18px rgba(79,70,229,.28); }
                @media (max-width: 640px) { .as-grid { grid-template-columns: 1fr; } }
            `}</style>
            <div className="as-container">
                {isPlainUser && (
                    <div className="as-card as-role-box">
                        <h3><Briefcase size={17} style={{ color: "var(--accent,#4F46E5)" }} /> Choose Your Path</h3>
                        <p>
                            Your account is active but has no dashboard yet. Choose how you want to participate — you can also switch later. Both roles require verification proofs.
                        </p>
                        <div className="as-role-btns">
                            <Link to="/profile?join=seller" className="as-role-btn primary"><Briefcase size={15} /> Become a Seller</Link>
                            <Link to="/profile?join=buyer" className="as-role-btn"><User size={15} /> Become a Buyer</Link>
                        </div>
                    </div>
                )}

                <div className="as-card">
                    <h2><User size={17} /> Account Details</h2>
                    <p className="sub">Update your basic profile information.</p>
                    <form onSubmit={submit}>
                        <div className="as-avatar-row">
                            <img className="as-avatar" src={avatar ? URL.createObjectURL(avatar) : (user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(form.name || "U")}&background=4F46E5&color=fff`)} alt="" />
                            <label className="as-save" style={{ background: "var(--bg-badge,#F1F5F9)", color: "var(--text-primary,#0F172A)", boxShadow: "none", cursor: "pointer", fontSize: 13 }}>
                                <Camera size={15} /> Change Photo
                                <input type="file" accept="image/*" style={{ display: "none" }} onChange={(e) => setAvatar(e.target.files?.[0] || null)} />
                            </label>
                        </div>
                        <div className="as-grid">
                            <div className="as-field">
                                <label>Name</label>
                                <input className="as-input" name="name" value={form.name} onChange={handleChange} />
                            </div>
                            <div className="as-field">
                                <label>Username</label>
                                <input className="as-input" name="username" value={form.username} onChange={handleChange} disabled />
                            </div>
<div className="as-field">
                            <label>Phone</label>
                            <input className="as-input" name="phone" type="tel" value={form.phone} onChange={handleChange} />
                        </div>
                        <LocationFields value={location} onChange={setLocation} labels={locationLabels} style={{ gridColumn: "1 / -1" }} />
                        <div className="as-field">
                            <label>Address</label>
                            <input className="as-input" name="address" value={form.address} onChange={handleChange} />
                        </div>
                        </div>
                        {error && <div className="as-error">{error}</div>}
                        <button type="submit" className="as-save" disabled={saving}>
                            {saving ? <Loader2 size={15} className="spin" /> : <Save size={15} />} {saving ? "Saving..." : "Save Changes"}
                        </button>
                        <Link to="/profile" style={{ marginLeft: 14, color: "var(--accent,#4F46E5)", fontSize: 13, fontWeight: 700 }}>
                            Profile Details <ArrowRight size={13} style={{ verticalAlign: "middle" }} />
                        </Link>
                    </form>
                </div>

                <div className="as-card">
                    <h2><Lock size={17} /> Change Password</h2>
                    <p className="sub">Choose a strong password that you don't use elsewhere.</p>
                    <form onSubmit={changePassword}>
                        <div className="as-field">
                            <label>Current Password</label>
                            <input className="as-input" type="password" name="current_password" value={pwd.current_password} onChange={(e) => setPwd({ ...pwd, current_password: e.target.value })} required autoComplete="current-password" />
                            {pwdErrors.current_password && <span className="as-error">{pwdErrors.current_password[0]}</span>}
                        </div>
                        <div className="as-grid">
                            <div className="as-field">
                                <label>New Password</label>
                                <input className="as-input" type="password" name="password" value={pwd.password} onChange={(e) => setPwd({ ...pwd, password: e.target.value })} required autoComplete="new-password" />
                                {pwdErrors.password && <span className="as-error">{pwdErrors.password[0]}</span>}
                            </div>
                            <div className="as-field">
                                <label>Confirm New Password</label>
                                <input className="as-input" type="password" name="password_confirmation" value={pwd.password_confirmation} onChange={(e) => setPwd({ ...pwd, password_confirmation: e.target.value })} required autoComplete="new-password" />
                            </div>
                        </div>
                        {pwdErrors.form && <div className="as-error">{pwdErrors.form[0]}</div>}
                        <button type="submit" className="as-save" disabled={pwdSaving}>
                            {pwdSaving ? <Loader2 size={15} className="spin" /> : <Lock size={15} />} {pwdSaving ? "Updating..." : "Update Password"}
                        </button>
                        <span style={{ marginLeft: 12, color: "var(--text-muted,#64748B)", fontSize: 12 }}>
                            <Shield size={12} style={{ verticalAlign: -2 }} /> Your data is always encrypted.
                        </span>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default AccountSettings;