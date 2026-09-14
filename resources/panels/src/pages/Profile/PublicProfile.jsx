import { useEffect, useMemo, useState } from "react";
import { Link, Navigate, useSearchParams } from "react-router-dom";
import { Loader2, Save, User, Camera, ArrowRight, BadgeCheck, Briefcase, FileCheck2, ShieldCheck, Lock } from "lucide-react";
import { useAuth } from "../../contexts/AuthContext";
import frontendApi from "../../shared/frontendApi";
import { showDialog } from "../../shared/components/Dialog";
import SearchableMultiSelect from "../../shared/components/SearchableMultiSelect";
import LocationFields from "../../shared/components/LocationFields";

const capturableGeo = () =>
    Promise.race([
        new Promise((resolve) => {
            if (typeof navigator === "undefined" || !navigator.geolocation) return resolve(null);
            navigator.geolocation.getCurrentPosition(
                (p) => resolve([p.coords.latitude, p.coords.longitude]),
                () => resolve(null),
                { timeout: 1500, maximumAge: 600000 }
            );
        }),
        new Promise((resolve) => setTimeout(() => resolve(null), 2000)),
    ]);

const roleLabel = (role) => {
    if (role === "buyer") return "Buyer";
    if (role === "seller") return "Seller";
    if (role === "admin") return "Administrator";
    return "Member";
};

const strengthColor = (pct) => {
    if (pct >= 90) return "#10B981";
    if (pct >= 75) return "#3B82F6";
    if (pct >= 50) return "#F59E0B";
    if (pct >= 25) return "#F97316";
    return "#EF4444";
};

const strengthLabel = (pct) => {
    if (pct >= 90) return "Excellent";
    if (pct >= 75) return "Good";
    if (pct >= 50) return "Almost there";
    if (pct >= 25) return "Getting started";
    return "Needs work";
};

const kycStatusLabel = {
    pending: "Pending review",
    submitted: "Submitted — under review",
    verified: "Verified",
    rejected: "Rejected — please re-upload",
};

function PublicProfile() {
    const { user, loading, fetchUser } = useAuth();
    const [searchParams, setSearchParams] = useSearchParams();
    const [join, setJoin] = useState(() => searchParams.get("join") === "seller" ? "seller" : searchParams.get("join") === "buyer" ? "buyer" : null);
    const [privacyAgreed, setPrivacyAgreed] = useState(false);
    const [privacyClicked, setPrivacyClicked] = useState(false);
    const [form, setForm] = useState({
        full_name: "",
        phone: "",
        email: "",
        company_name: "",
        gender: "",
        dob: "",
        postal_code: "",
        address: "",
        bio: "",
    });
    const [location, setLocation] = useState({ country_id: null, state_id: null, city_id: null });
    const [locationLabels, setLocationLabels] = useState({ state: "", city: "" });
    const [aadhaarNumber, setAadhaarNumber] = useState("");
    const [panNumber, setPanNumber] = useState("");
    const [aadhaarFile, setAadhaarFile] = useState(null);
    const [panFile, setPanFile] = useState(null);
    const [verificationFile, setVerificationFile] = useState(null);
    const [isConsultancy, setIsConsultancy] = useState(false);
    const [avatar, setAvatar] = useState(null);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");
    const [categories, setCategories] = useState([]);
    const [serviceTypes, setServiceTypes] = useState([]);
    const [serviceTypeIds, setServiceTypeIds] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState("");
    const [typesLoading, setTypesLoading] = useState(false);
    const [categoryDetails, setCategoryDetails] = useState({});

    const fieldSchema = useMemo(() => {
        const cat = categories.find((c) => String(c.id) === String(selectedCategory));
        return cat?.category_type === "field" && Array.isArray(cat.form_fields) ? cat.form_fields : [];
    }, [categories, selectedCategory]);

    const loadServiceTypes = (categoryId, keepIds) => {
        if (!categoryId) { if (!keepIds) setServiceTypes([]); return; }
        setTypesLoading(true);
        frontendApi
            .get("/frontend/service-types", { params: { category_id: categoryId } })
            .then(({ data: res }) => setServiceTypes(Array.isArray(res.data) ? res.data : []))
            .catch(() => {})
            .finally(() => setTypesLoading(false));
    };

    useEffect(() => {
        if (user?.role !== "seller") return;
        frontendApi
            .get("/frontend/categories")
            .then(({ data: res }) => {
                const list = res.data || res || [];
                if (Array.isArray(list) && list.length) setCategories(list);
            })
            .catch(() => {});
    }, [user]);

    useEffect(() => {
        if (!user) return;
        frontendApi
            .get("/profile")
            .then((res) => {
                const p = res.data?.profile || {};
                const rec = p.record || {};
                const prof = p.profile || {};
                setForm({
                    full_name: rec.full_name || p.name || user.name || "",
                    phone: prof.phone || p.phone || "",
                    email: p.email || user.email || "",
                    company_name: rec.company_name || "",
                    gender: prof.gender || "",
                    dob: prof.dob || "",
                    postal_code: prof.postal_code || "",
                    address: prof.address || "",
                    bio: prof.bio || "",
                });
                setLocation({
                    country_id: prof.country_id || null,
                    state_id: prof.state_id || null,
                    city_id: prof.city_id || null,
                });
                setLocationLabels({ state: prof.state || "", city: prof.city || "" });
                if (user.role === "buyer") setIsConsultancy(Boolean(user.kyc?.is_consultancy));
                const types = Array.isArray(p.service_types) ? p.service_types : [];
                const ids = types.map((t) => (typeof t === "object" ? t.id : t));
                setServiceTypeIds(ids);
                if (user.role === "seller" && types[0]?.category_id) {
                    setSelectedCategory(String(types[0].category_id));
                    loadServiceTypes(types[0].category_id, true);
                }
                if (p.category_details?.data && typeof p.category_details.data === "object") {
                    setCategoryDetails(p.category_details.data);
                }
            })
            .catch(() => {});
    }, [user]);

    const chooseJoin = (which) => {
        setJoin(which);
        setSearchParams({ join: which }, { replace: true });
    };

    const cancelJoin = () => {
        setJoin(null);
        setSearchParams({}, { replace: true });
        setPrivacyAgreed(false);
        setPrivacyClicked(false);
    };

    const setCatDetail = (key, value) => setCategoryDetails((prev) => ({ ...prev, [key]: value }));

    const renderCatField = (f) => {
        const value = categoryDetails[f.key];
        const reqMark = f.required ? " *" : "";
        const wide = ["textarea", "select", "multiselect", "checkbox"].includes(f.type) ? { gridColumn: "1 / -1" } : {};
        const label = (
            <label>
                {f.label || f.key}
                {reqMark}
            </label>
        );

        if (f.type === "textarea") {
            return (
                <div className="pp-field full" key={f.key} style={wide}>
                    {label}
                    <textarea className="pp-input" rows={3} placeholder={f.placeholder || ""} value={value || ""} onChange={(e) => setCatDetail(f.key, e.target.value)} />
                </div>
            );
        }

        if (f.type === "select") {
            return (
                <div className="pp-field" key={f.key} style={wide}>
                    {label}
                    <select className="pp-input" value={value || ""} onChange={(e) => setCatDetail(f.key, e.target.value)}>
                        <option value="">{f.placeholder || `Select ${(f.label || f.key).toLowerCase()}`}</option>
                        {(Array.isArray(f.options) ? f.options : []).map((o) => (
                            <option key={o} value={o}>{o}</option>
                        ))}
                    </select>
                </div>
            );
        }

        if (f.type === "multiselect") {
            const selected = Array.isArray(value) ? value : [];
            return (
                <div className="pp-field full" key={f.key} style={wide}>
                    {label}
                    <div style={{ display: "flex", flexWrap: "wrap", gap: 8 }}>
                        {(Array.isArray(f.options) ? f.options : []).map((o) => (
                            <label key={o} style={{ display: "inline-flex", alignItems: "center", gap: 6, padding: "8px 12px", borderRadius: 50, background: selected.includes(o) ? "var(--accent-light,rgba(79,70,229,.12))" : "var(--bg-badge,#F1F5F9)", border: selected.includes(o) ? "1.5px solid var(--accent,#4F46E5)" : "1.5px solid var(--border-color,#E2E8F0)", fontSize: 13, cursor: "pointer", fontWeight: 600 }}>
                                <input
                                    type="checkbox"
                                    checked={selected.includes(o)}
                                    style={{ display: "none" }}
                                    onChange={(e) => {
                                        const next = e.target.checked ? [...selected, o] : selected.filter((x) => x !== o);
                                        setCatDetail(f.key, next);
                                    }}
                                />
                                {o}
                            </label>
                        ))}
                    </div>
                </div>
            );
        }

        if (f.type === "checkbox") {
            return (
                <div className="pp-field full" key={f.key} style={wide}>
                    <label style={{ display: "flex", alignItems: "center", gap: 10, cursor: "pointer", fontSize: 13.5 }}>
                        <input type="checkbox" checked={Boolean(value)} onChange={(e) => setCatDetail(f.key, e.target.checked)} style={{ width: 16, height: 16, accentColor: "#4F46E5" }} />
                        <span>{f.label || f.key}</span>
                    </label>
                </div>
            );
        }

        const inputType = f.type === "number" ? "number" : f.type === "tel" ? "tel" : "text";
        return (
            <div className="pp-field" key={f.key} style={wide}>
                {label}
                <input className="pp-input" type={inputType} placeholder={f.placeholder || ""} value={value || ""} onChange={(e) => setCatDetail(f.key, e.target.value)} />
            </div>
        );
    };

    const appendCategoryDetails = (fd) => {
        const cat = categories.find((c) => String(c.id) === String(selectedCategory));
        if (!cat || cat.category_type !== "field") return;
        fd.append("category_id", String(selectedCategory));
        (Array.isArray(cat.form_fields) ? cat.form_fields : []).forEach((f) => {
            const v = categoryDetails[f.key];
            if (v === undefined || v === null || v === "") return;
            if (f.type === "multiselect") {
                (Array.isArray(v) ? v : []).forEach((item) => fd.append(`category_details[${f.key}][]`, String(item)));
            } else if (f.type === "checkbox") {
                fd.append(`category_details[${f.key}]`, v ? "1" : "0");
            } else {
                fd.append(`category_details[${f.key}]`, String(v));
            }
        });
    };

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
            if (join) {
                if (!privacyAgreed) {
                    setSaving(false);
                    setError("Please read and agree to the Privacy Policy before continuing.");
                    return;
                }
                const fd = new FormData();
                Object.entries(form).forEach(([k, v]) => fd.append(k, String(v == null ? "" : v)));
                fd.append("country_id", location.country_id || "");
                fd.append("state_id", location.state_id || "");
                fd.append("city_id", location.city_id || "");
                fd.append("privacy_policy", "1");
                if (join === "seller") {
                    fd.append("aadhaar_number", aadhaarNumber || "");
                    fd.append("pan_number", panNumber || "");
                    if (aadhaarFile) fd.append("aadhaar_document", aadhaarFile);
                    if (panFile) fd.append("pan_document", panFile);
                    appendCategoryDetails(fd);
                    const coords = await capturableGeo();
                    if (coords) {
                        fd.append("latitude", coords[0]);
                        fd.append("longitude", coords[1]);
                    }
                } else {
                    fd.append("is_consultancy", isConsultancy ? "1" : "0");
                    if (verificationFile) fd.append("verification_document", verificationFile);
                }
                const res = await frontendApi.post(`/become-${join}`, fd, { headers: { "Content-Type": "multipart/form-data" } });
                await fetchUser();
                await showDialog({
                    type: "success",
                    title: "You're in!",
                    message: res.data?.message || "Your account has been upgraded. A welcome email with your privacy policy copy is on its way.",
                    confirmText: "Continue",
                });
                window.location.href = join === "seller" ? "/seller" : "/buyer";
            } else {
                const fd = new FormData();
                Object.entries(form).forEach(([k, v]) => fd.append(k, String(v == null ? "" : v)));
                fd.append("country_id", location.country_id || "");
                fd.append("state_id", location.state_id || "");
                fd.append("city_id", location.city_id || "");
                serviceTypeIds.forEach((id) => fd.append("service_type_ids[]", String(id)));
                if (avatar) fd.append("avatar", avatar);
                fd.append("aadhaar_number", aadhaarNumber || "");
                fd.append("pan_number", panNumber || "");
                if (aadhaarFile) fd.append("aadhaar_document", aadhaarFile);
                if (panFile) fd.append("pan_document", panFile);
                fd.append("is_consultancy", isConsultancy ? "1" : "0");
                if (verificationFile) fd.append("verification_document", verificationFile);
                if (user.role === "seller") appendCategoryDetails(fd);
                const res = await frontendApi.post("/profile", fd, { headers: { "Content-Type": "multipart/form-data" } });
                await fetchUser();
                await showDialog({
                    type: "success",
                    title: "Profile Updated",
                    message: res.data?.message || "Your profile has been saved successfully.",
                    confirmText: "Great",
                });
            }
        } catch (err) {
            const msg = err.response?.data?.message || "Could not update your profile. Please try again.";
            setError(msg);
            await showDialog({
                type: "danger",
                title: "Update Failed",
                message: msg,
                confirmText: "OK",
            });
        } finally {
            setSaving(false);
        }
    };

    const avatarSrc = useMemo(() => {
        if (avatar) return URL.createObjectURL(avatar);
        if (user?.avatar) return user.avatar;
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(form.full_name || "U")}&background=4F46E5&color=fff`;
    }, [avatar, user, form.full_name]);

    if (loading) {
        return (
            <div style={{ minHeight: "70vh", display: "flex", alignItems: "center", justifyContent: "center" }}>
                <Loader2 size={28} className="spin" style={{ color: "var(--accent,#4F46E5)" }} />
            </div>
        );
    }

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    const complete = Boolean(user.complete);
    const isPlainUser = user.role === "user";
    const kyc = user.kyc || {};
    const pct = Math.max(0, Math.min(100, Number(user.completeness_percent) || 0));
    const ringColor = strengthColor(pct);
    const showStrength = user.role !== "admin";
    const dashboardLink = user.role === "seller" ? "/seller" : user.role === "buyer" ? "/buyer" : user.role === "admin" ? "/admin" : null;

    return (
        <div className="public-profile">
            <style>{`
                .public-profile { background: var(--bg-body,#F8FAFC); min-height: 75vh; padding: 48px 24px 72px; }
                .pp-container { max-width: 820px; margin: 0 auto; }
                .pp-card { background: var(--bg-card,#fff); border: 1px solid var(--border-color,#E2E8F0); border-radius: 22px; padding: 28px; box-shadow: var(--shadow-sm,0 1px 3px rgba(0,0,0,.06)); }
                .pp-head { display: flex; gap: 22px; align-items: center; flex-wrap: wrap; margin-bottom: 24px; }
                .pp-avatar-side { display: flex; flex-direction: column; align-items: center; gap: 10px; }
                .pp-avatar-wrap { position: relative; }
                .pp-avatar-ring { width: 96px; height: 96px; border-radius: 50%; padding: 4px; display: block; background: conic-gradient(var(--ring-color,#4F46E5) var(--ring-pct,0%), #E2E8F0 0); }
                .pp-avatar { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid var(--bg-card,#fff); background: var(--bg-badge,#F1F5F9); display: block; }
                .pp-camera { position: absolute; bottom: -2px; right: -2px; width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid var(--bg-card,#fff); }
                .pp-strength { width: 112px; }
                .pp-strength-top { display: flex; justify-content: space-between; align-items: baseline; gap: 6px; font-size: 11px; font-weight: 800; }
                .pp-strength-track { height: 6px; border-radius: 50px; background: var(--bg-badge,#E2E8F0); margin: 4px 0 3px; overflow: hidden; }
                .pp-strength-bar { height: 100%; border-radius: 50px; transition: width .4s ease; }
                .pp-strength-hint { font-size: 10.5px; text-align: center; opacity: .85; }
                .pp-title h1 { font-size: clamp(20px,3vw,26px); font-weight: 800; color: var(--text-primary,#0F172A); margin: 0 0 4px; }
                .pp-title p { color: var(--text-muted,#64748B); font-size: 14px; margin: 0; }
                .pp-role { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: var(--accent,#4F46E5); background: var(--accent-light,rgba(79,70,229,.08)); padding: 4px 12px; border-radius: 50px; }
                .pp-banner { display: flex; gap: 12px; align-items: flex-start; padding: 14px 16px; border-radius: 14px; margin-bottom: 20px; font-size: 13.5px; line-height: 1.5; }
                .pp-banner.warn { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3); color: #b45309; }
                .pp-banner.ok { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: #047857; }
                .pp-banner.info { background: rgba(79,70,229,.07); border: 1px solid rgba(79,70,229,.25); color: #4338CA; }
                .pp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 18px; }
                .pp-field { display: flex; flex-direction: column; gap: 6px; }
                .pp-field.full { grid-column: 1 / -1; }
                .pp-field label { font-size: 12.5px; font-weight: 600; color: var(--text-secondary,#475569); }
                .pp-input { width: 100%; padding: 11px 14px; border-radius: 12px; border: 1.5px solid var(--border-color,#E2E8F0); background: var(--bg-input,#fff); font-size: 14px; color: var(--text-primary,#0F172A); outline: none; font-family: inherit; }
                .pp-input:focus { border-color: var(--accent,#4F46E5); box-shadow: 0 0 0 3px var(--accent-glow,rgba(79,70,229,.1)); }
                .pp-kyc-box { background: rgba(245,158,11,.06); border: 1px solid rgba(245,158,11,.3); border-radius: 16px; padding: 16px; margin-top: 18px; }
                .pp-kyc-note { font-size: 12.5px; color: var(--text-muted,#64748B); margin: 2px 0 12px; }
                .pp-file-drop { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; text-align: center; padding: 18px; border: 2px dashed var(--border-color,#CBD5E1); border-radius: 12px; background: var(--bg-badge,#F8FAFC); cursor: pointer; font-size: 12.5px; color: var(--text-muted,#64748B); }
                .pp-actions { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 26px; flex-wrap: wrap; }
                .pp-save { display: inline-flex; align-items: center; gap: 8px; padding: 12px 26px; border: none; border-radius: 12px; background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; box-shadow: 0 6px 18px rgba(79,70,229,.28); }
                .pp-save:disabled { opacity: .6; cursor: not-allowed; }
                .pp-dash { display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; border-radius: 12px; border: 1.5px solid var(--border-color,#E2E8F0); background: var(--bg-card,#fff); color: var(--text-primary,#0F172A); font-size: 14px; font-weight: 600; text-decoration: none; font-family: inherit; }
                .pp-ghost { background: var(--bg-badge,#F1F5F9); color: var(--text-secondary); border: none; }
                .pp-error { color: var(--danger,#EF4444); font-size: 13px; font-weight: 600; margin-top: 12px; }
                .pp-join-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 6px; }
                .pp-join-card { border: 1.5px solid var(--border-color,#E2E8F0); border-radius: 16px; padding: 20px; cursor: pointer; background: var(--bg-card,#fff); transition: all .2s; }
                .pp-join-card:hover { border-color: var(--accent,#4F46E5); box-shadow: 0 10px 26px rgba(79,70,229,.12); }
                .pp-join-card h4 { margin: 0 0 4px; font-size: 15px; font-weight: 800; display: flex; align-items: center; gap: 8px; }
                .pp-join-card p { margin: 0; font-size: 12.5px; color: var(--text-muted,#64748B); line-height: 1.55; }
                .pp-privacy-row { display: flex; align-items: flex-start; gap: 10px; margin-top: 18px; font-size: 13px; line-height: 1.5; cursor: pointer; }
                .pp-privacy-row input { margin-top: 2px; width: 16px; height: 16px; accent-color: #4F46E5; }
                table.pp-upload-list { width: 100%; border-collapse: collapse; margin-top: 6px; }
                table.pp-upload-list td { padding: 10px 4px; border-bottom: 1px solid var(--border-color,#E2E8F0); font-size: 13px; }
                @media (max-width: 640px) { .pp-grid, .pp-join-actions { grid-template-columns: 1fr; } }
            `}</style>

            <div className="pp-container">
                <div className="pp-card">
                    <div className="pp-head">
                        <div className="pp-avatar-side">
                            <div className="pp-avatar-wrap">
                                <span className="pp-avatar-ring" style={{ "--ring-color": ringColor, "--ring-pct": `${pct}%` }}>
                                    <img className="pp-avatar" src={avatarSrc} alt="" />
                                </span>
                                <label className="pp-camera" title="Change photo">
                                    <Camera size={13} />
                                    <input type="file" accept="image/*" style={{ display: "none" }} onChange={(e) => setAvatar(e.target.files?.[0] || null)} />
                                </label>
                            </div>
                            {showStrength && (
                                <div className="pp-strength">
                                    <div className="pp-strength-top">
                                        <span>Profile Strength</span>
                                        <span style={{ color: ringColor }}>{pct}%</span>
                                    </div>
                                    <div className="pp-strength-track">
                                        <div className="pp-strength-bar" style={{ width: `${pct}%`, background: ringColor }} />
                                    </div>
                                    <div className="pp-strength-hint" style={{ color: ringColor }}>{strengthLabel(pct)}</div>
                                </div>
                            )}
                        </div>
                        <div className="pp-title">
                            <h1>{form.full_name || user.name}</h1>
                            <p>{form.email || user.email}</p>
                            <span className="pp-role"><User size={13} /> {roleLabel(user.role)}</span>
                        </div>
                    </div>

                    {isPlainUser && !join && (
                        <div className="pp-banner info" style={{ marginBottom: 22 }}>
                            <Briefcase size={19} style={{ flexShrink: 0, marginTop: 2 }} />
                            <span>
                                Your member account is active. To get a dashboard, choose <strong>Buyer</strong> (order services) or <strong>Seller</strong> (offer services). You can switch later.
                            </span>
                        </div>
                    )}

                    {complete && !isPlainUser ? (
                        <div className="pp-banner ok">
                            <BadgeCheck size={19} style={{ flexShrink: 0, marginTop: 2 }} />
                            <span>Your profile is complete. You have full access to your dashboard and can place orders.</span>
                        </div>
                    ) : (
                        !isPlainUser && (
                            <div className="pp-banner warn">
                                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" style={{ flexShrink: 0, marginTop: 2 }}><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>
                                    Finish filling the details below to complete your profile. Your dashboard unlocks once your profile is complete.
                                    {user.missing?.length ? <> Missing: <strong>{user.missing.join(", ")}</strong>.</> : ""}
                                </span>
                            </div>
                        )
                    )}

                    <form onSubmit={submit}>
                        {isPlainUser && !join && (
                            <div className="pp-join-actions">
                                <button type="button" className="pp-join-card" onClick={() => chooseJoin("buyer")}>
                                    <h4><User size={16} style={{ color: "var(--accent,#4F46E5)" }} /> Become a Buyer</h4>
                                    <p>Browse services, request quotes, place orders and track projects.</p>
                                </button>
                                <button type="button" className="pp-join-card" onClick={() => chooseJoin("seller")}>
                                    <h4><Briefcase size={16} style={{ color: "var(--accent,#4F46E5)" }} /> Become a Seller</h4>
                                    <p>Create services, set your weekly availability and start earning.</p>
                                </button>
                            </div>
                        )}

                        <div className="pp-grid">
                            <div className="pp-field">
                                <label>Full Name {join ? "*" : ""}</label>
                                <input className="pp-input" name="full_name" value={form.full_name} onChange={handleChange} required={join === "seller"} />
                            </div>
                            <div className="pp-field">
                                <label>Phone {join ? "*" : ""}</label>
                                <input className="pp-input" name="phone" type="tel" placeholder="+91 98765 43210" value={form.phone} onChange={handleChange} required />
                            </div>
                            <div className="pp-field">
                                <label>Email</label>
                                <input className="pp-input" name="email" type="email" value={form.email} disabled />
                            </div>
                            {(user.role === "buyer" || (isPlainUser && join === "buyer")) && (
                                <div className="pp-field">
                                    <label>Company Name</label>
                                    <input className="pp-input" name="company_name" value={form.company_name} onChange={handleChange} />
                                </div>
                            )}
                            <LocationFields value={location} onChange={setLocation} labels={locationLabels} required style={{ gridColumn: "1 / -1" }} />
                            <div className="pp-field">
                                <label>Postal Code</label>
                                <input className="pp-input" name="postal_code" value={form.postal_code} onChange={handleChange} />
                            </div>
                            <div className="pp-field">
                                <label>Date of Birth</label>
                                <input className="pp-input" name="dob" type="date" value={form.dob} onChange={handleChange} />
                            </div>
                            <div className="pp-field">
                                <label>Gender</label>
                                <select className="pp-input" name="gender" value={form.gender} onChange={handleChange}>
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div className="pp-field full">
                                <label>Address *</label>
                                <input className="pp-input" name="address" placeholder="Street, area, building..." value={form.address} onChange={handleChange} required />
                            </div>
                            <div className="pp-field full">
                                <label>Bio</label>
                                <textarea className="pp-input" name="bio" rows={3} placeholder="Tell buyers/sellers a little about yourself (optional)" value={form.bio} onChange={handleChange} />
                            </div>

                            {user.role === "seller" && (
                                <div className="pp-field full" style={{ background: "var(--accent-light,rgba(79,70,229,.06))", border: "1px solid var(--border-color,#E2E8F0)", borderRadius: 16, padding: 16 }}>
                                    <label style={{ display: "flex", alignItems: "center", gap: 7, fontWeight: 700 }}>
                                        <Briefcase size={15} style={{ color: "var(--accent,#4F46E5)" }} />
                                        Services You Offer *
                                    </label>
                                    <p className="hint" style={{ margin: "2px 0 10px", fontSize: 12.5, color: "var(--text-muted,#64748B)" }}>
                                        Required to complete your seller profile — buyers find you through these (e.g. carpenter, painter, plumber, logo design…).
                                    </p>
                                    <div style={{ display: "flex", flexDirection: "column", gap: 10 }}>
                                        <select className="pp-input" value={selectedCategory} onChange={(e) => { setSelectedCategory(e.target.value); setServiceTypeIds([]); loadServiceTypes(e.target.value); }}>
                                            <option value="">Choose a category</option>
                                            {categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                                        </select>
                                        <SearchableMultiSelect
                                            options={serviceTypes}
                                            selected={serviceTypeIds}
                                            onChange={setServiceTypeIds}
                                            loading={typesLoading}
                                            disabled={!selectedCategory}
                                            placeholder={selectedCategory ? "Search services…" : "Select a category first"}
                                            emptyText="No services in this category yet."
                                        />
                                    </div>
                                </div>
                            )}

                            {user.role === "seller" && fieldSchema.length > 0 && (
                                <>
                                    <div className="pp-field full" style={{ background: "var(--accent-light,rgba(79,70,229,.06))", border: "1px solid var(--border-color,#E2E8F0)", borderRadius: 16, padding: 16 }}>
                                        <label style={{ display: "flex", alignItems: "center", gap: 7, fontWeight: 700 }}>
                                            <Briefcase size={15} style={{ color: "var(--accent,#4F46E5)" }} />
                                            {fieldSchema[0]?.section || "Category Details"}
                                        </label>
                                        <p className="hint" style={{ margin: "2px 0 0", fontSize: 12.5, color: "var(--text-muted,#64748B)" }}>
                                            Required for {selectedCategory ? categories.find((c) => String(c.id) === String(selectedCategory))?.name : "your category"} — this helps buyers find and trust you.
                                        </p>
                                    </div>
                                    {fieldSchema.map(renderCatField)}
                                </>
                            )}
                        </div>

                        {(join === "seller" || user.role === "seller") && (
                            <div className="pp-kyc-box">
                                <label style={{ display: "flex", alignItems: "center", gap: 7, fontWeight: 700 }}>
                                    <ShieldCheck size={15} style={{ color: "#B45309" }} />
                                    Identity Verification (KYC) — Aadhaar + PAN
                                </label>
                                <p className="pp-kyc-note">
                                    Upload Aadhaar and PAN to verify your identity. All documents are stored securely, only visible to admins, and never shown on your public profile.
                                </p>
                                <table className="pp-upload-list">
                                    <tbody>
                                        <tr>
                                            <td style={{ width: 190 }}><strong>Aadhaar Number</strong></td>
                                            <td><input className="pp-input" value={aadhaarNumber} onChange={(e) => setAadhaarNumber(e.target.value)} placeholder="XXXX XXXX XXXX" /></td>
                                        </tr>
                                        <tr>
                                            <td>{"Aadhaar Document" + (kyc.aadhaar_uploaded ? " ✓" : " *")}</td>
                                            <td>
                                                <label className="pp-file-drop">
                                                    <FileCheck2 size={17} />
                                                    {aadhaarFile ? aadhaarFile.name : (kyc.aadhaar_uploaded ? "Uploaded — choose to replace" : "Upload a clear photo or PDF")}
                                                    <input type="file" accept=".jpg,.jpeg,.png,.pdf" style={{ display: "none" }} onChange={(e) => setAadhaarFile(e.target.files?.[0] || null)} />
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style={{ width: 190 }}><strong>PAN Number</strong></td>
                                            <td><input className="pp-input" value={panNumber} onChange={(e) => setPanNumber(e.target.value)} placeholder="ABCDE1234F" /></td>
                                        </tr>
                                        <tr>
                                            <td>{"PAN Document" + (kyc.pan_uploaded ? " ✓" : " *")}</td>
                                            <td>
                                                <label className="pp-file-drop">
                                                    <FileCheck2 size={17} />
                                                    {panFile ? panFile.name : (kyc.pan_uploaded ? "Uploaded — choose to replace" : "Upload a clear photo or PDF")}
                                                    <input type="file" accept=".jpg,.jpeg,.png,.pdf" style={{ display: "none" }} onChange={(e) => setPanFile(e.target.files?.[0] || null)} />
                                                </label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                {kyc.status && (
                                    <p style={{ fontSize: 12.5, margin: "10px 0 0", fontWeight: 600, color: kyc.status === "rejected" ? "var(--danger,#EF4444)" : "var(--text-muted,#64748B)" }}>
                                        Status: {kycStatusLabel[kyc.status] || kyc.status}
                                        {kyc.status === "rejected" && kyc.rejection_reason ? ` — ${kyc.rejection_reason}` : ""}
                                    </p>
                                )}
                            </div>
                        )}

                        {(join === "buyer" || (user.role === "buyer" && user.kyc?.is_consultancy)) && (
                            <div className="pp-kyc-box">
                                <label style={{ display: "flex", alignItems: "center", gap: 7, fontWeight: 700 }}>
                                    <ShieldCheck size={15} style={{ color: "#B45309" }} />
                                    Buyer Verification Proof
                                </label>
                                <p className="pp-kyc-note">
                                    Consultancy buyers must verify their identity before placing consultancy orders. Upload a business/identity document.
                                </p>
                                <div style={{ display: "flex", alignItems: "center", gap: 10, marginBottom: 12 }}>
                                    <input type="checkbox" checked={isConsultancy} onChange={(e) => setIsConsultancy(e.target.checked)} style={{ width: 16, height: 16, accentColor: "#4F46E5" }} />
                                    <label htmlFor="consultancy" style={{ fontSize: 13, cursor: "pointer" }}>I am a consultancy buyer (business / bulk purchases)</label>
                                </div>
                                {(join === "buyer" || isConsultancy) && (
                                    <>
                                        <table className="pp-upload-list">
                                            <tbody>
                                                <tr>
                                                    <td style={{ width: 190 }}>{"Verification Document" + (kyc.verification_uploaded ? " ✓" : " *")}</td>
                                                    <td>
                                                        <label className="pp-file-drop">
                                                            <FileCheck2 size={17} />
                                                            {verificationFile ? verificationFile.name : (kyc.verification_uploaded ? "Uploaded — choose to replace" : "Upload proof (photo/PDF)")}
                                                            <input type="file" accept=".jpg,.jpeg,.png,.pdf" style={{ display: "none" }} onChange={(e) => setVerificationFile(e.target.files?.[0] || null)} />
                                                        </label>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        {kyc.status && (
                                            <p style={{ fontSize: 12.5, margin: "10px 0 0", fontWeight: 600, color: kyc.status === "rejected" ? "var(--danger,#EF4444)" : "var(--text-muted,#64748B)" }}>
                                                Status: {kycStatusLabel[kyc.status] || kyc.status}
                                                {kyc.status === "rejected" && kyc.rejection_reason ? ` — ${kyc.rejection_reason}` : ""}
                                            </p>
                                        )}
                                    </>
                                )}
                            </div>
                        )}

                        {join && (
                            <label className="pp-privacy-row">
                                <input type="checkbox" checked={privacyAgreed} onChange={(e) => { setPrivacyAgreed(e.target.checked); if (e.target.checked) setPrivacyClicked(true); }} />
                                <span>
                                    {privacyClicked ? (
                                        "I have read and agree to the "
                                    ) : (
                                        <>
                                            Before you check this box, <Link to="/privacy-policy" target="_blank" onClick={() => setPrivacyClicked(true)} style={{ color: "#4F46E5", fontWeight: 700 }}>click here to view the Privacy Policy</Link>. Then tick the box to agree.{" "}
                                        </>
                                    )}
                                    <Link to="/privacy-policy" target="_blank" onClick={() => setPrivacyClicked(true)} style={{ color: "#4F46E5", fontWeight: 700 }}>Privacy Policy</Link>
                                    {" "}<Lock size={11} style={{ verticalAlign: -1, color: "var(--text-muted,#64748B)" }} />
                                </span>
                            </label>
                        )}

                        {error && <div className="pp-error">{error}</div>}

                        <div className="pp-actions">
                            <div style={{ display: "flex", gap: 12, flexWrap: "wrap" }}>
                                <button type="submit" className="pp-save" disabled={saving || (join && !privacyAgreed)}>
                                    {saving ? <Loader2 size={16} className="spin" /> : join ? <BadgeCheck size={16} /> : <Save size={16} />}
                                    {saving ? "Saving..." : join === "seller" ? "Become a Seller" : join === "buyer" ? "Become a Buyer" : "Save Profile"}
                                </button>
                                {join && (
                                    <button type="button" className="pp-save pp-ghost" onClick={cancelJoin}>
                                        <ArrowRight size={15} /> Back
                                    </button>
                                )}
                            </div>
                            {dashboardLink && complete && !join && (
                                <Link to={dashboardLink} className="pp-dash">
                                    Open Dashboard <ArrowRight size={15} />
                                </Link>
                            )}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}

export default PublicProfile;