import { useEffect, useState } from "react";
import { Loader2, Save, User, AlertCircle, Briefcase } from "lucide-react";
import { useAuth } from "../../contexts/AuthContext";
import frontendApi from "../frontendApi";
import { showDialog } from "./Dialog";
import SearchableMultiSelect from "./SearchableMultiSelect";
import LocationFields from "./LocationFields";

function ProfileStep() {
    const { user, loading, fetchUser } = useAuth();
    const [form, setForm] = useState({ full_name: "", phone: "", address: "" });
    const [location, setLocation] = useState({ country_id: null, state_id: null, city_id: null });
    const [locationLabels, setLocationLabels] = useState({ state: "", city: "" });
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");
    const [categories, setCategories] = useState([]);
    const [serviceTypes, setServiceTypes] = useState([]);
    const [serviceTypeIds, setServiceTypeIds] = useState([]);
    const [selectedCategory, setSelectedCategory] = useState("");
    const [typesLoading, setTypesLoading] = useState(false);

    const isSeller = user?.role === "seller";
    const needsServices = isSeller && Array.isArray(user?.missing) && user.missing.includes("service_types");

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
        if (!isSeller) return;
        frontendApi.get("/frontend/categories")
            .then(({ data }) => {
                const list = data.data || data || [];
                if (Array.isArray(list) && list.length) setCategories(list);
            })
            .catch(() => {});
    }, [isSeller]);

    useEffect(() => {
        if (!user) return;
        frontendApi
            .get("/profile")
            .then((res) => {
                const p = res.data?.profile || {};
                const rec = p.record || {};
                const prof = p.profile || {};
                setForm({
                    full_name: rec.full_name || p.name || "",
                    phone: prof.phone || p.phone || "",
                    address: prof.address || "",
                });
                setLocation({
                    country_id: prof.country_id || null,
                    state_id: prof.state_id || null,
                    city_id: prof.city_id || null,
                });
                setLocationLabels({ state: prof.state || "", city: prof.city || "" });
                if (isSeller) {
                    const types = Array.isArray(p.service_types) ? p.service_types : [];
                    const ids = types.map((t) => (typeof t === "object" ? t.id : t));
                    setServiceTypeIds(ids);
                    if (types[0]?.category_id) {
                        setSelectedCategory(String(types[0].category_id));
                        loadServiceTypes(types[0].category_id, true);
                    }
                }
            })
            .catch(() => {});
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [user?.id, isSeller]);

    if (loading || !user || user.complete) return null;

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
        if (error) setError("");
    };

    const save = async (e) => {
        e.preventDefault();
        if (!form.full_name.trim() || !form.phone.trim() || !location.country_id || !location.state_id || !location.city_id || !form.address.trim()) {
            setError("Please fill in all the required fields (Name, Phone, Country, State, City, Address).");
            return;
        }
        if (isSeller && needsServices && !serviceTypeIds.length) {
            setError("Select at least one service you offer to complete your seller profile.");
            return;
        }
        setSaving(true);
        setError("");
        try {
            const payload = { ...form, ...location, service_type_ids: serviceTypeIds };
            if (user?.role === "user") payload.intent = "buyer";
            await frontendApi.post("/profile", payload);
            await fetchUser();
            await showDialog({
                type: "success",
                title: "Profile Completed",
                message: "Your profile is complete. You can now place this order.",
                confirmText: "Continue",
            });
        } catch (err) {
            setError(err.response?.data?.message || "Could not save your profile. Please try again.");
        } finally {
            setSaving(false);
        }
    };

    return (
        <div style={{ border: "1.5px solid rgba(245,158,11,.5)", background: "rgba(245,158,11,.06)", borderRadius: 14, padding: "14px 16px", marginBottom: 16 }}>
            <div style={{ display: "flex", alignItems: "center", gap: 8, marginBottom: 4 }}>
                <AlertCircle size={17} style={{ color: "#b45309", flexShrink: 0 }} />
                <strong style={{ fontSize: 14, color: "var(--text-primary,#0F172A)" }}>Complete your profile to place this order</strong>
            </div>
            <p style={{ fontSize: 12.5, color: "var(--text-muted,#64748B)", margin: "2px 0 12px", lineHeight: 1.5 }}>
                A few more details are needed. These unlock your dashboard and let us process your orders.
            </p>

            <form onSubmit={save}>
                <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 10 }}>
                    <div className="form-group" style={{ margin: 0 }}>
                        <label>Full Name <span style={{ color: "#EF4444" }}>*</span></label>
                        <input className="form-input" name="full_name" value={form.full_name} onChange={handleChange} placeholder="Your full name" />
                    </div>
                    <div className="form-group" style={{ margin: 0 }}>
                        <label>Phone <span style={{ color: "#EF4444" }}>*</span></label>
                        <input className="form-input" type="tel" name="phone" value={form.phone} onChange={handleChange} placeholder="+91 98765 43210" />
                    </div>
                    <LocationFields
                        value={location}
                        onChange={setLocation}
                        labels={locationLabels}
                        required
                        style={{ gridColumn: "1 / -1" }}
                    />
                    <div className="form-group" style={{ margin: 0, gridColumn: "1 / -1" }}>
                        <label>Address <span style={{ color: "#EF4444" }}>*</span></label>
                        <input className="form-input" name="address" value={form.address} onChange={handleChange} placeholder="Street, area, building..." />
                    </div>
                    {isSeller && needsServices && (
                        <div className="form-group" style={{ margin: 0, gridColumn: "1 / -1" }}>
                            <label style={{ display: "flex", alignItems: "center", gap: 6, marginBottom: 8 }}>
                                <Briefcase size={14} style={{ color: "var(--accent,#4F46E5)" }} /> Services You Offer <span style={{ color: "#EF4444" }}>*</span>
                            </label>
                            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 10 }}>
                                <select
                                    className="form-select"
                                    value={selectedCategory}
                                    onChange={(e) => { setSelectedCategory(e.target.value); setServiceTypeIds([]); loadServiceTypes(e.target.value); }}
                                >
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
                </div>
                {error && <div style={{ color: "#EF4444", fontSize: 12.5, fontWeight: 600, marginTop: 10 }}>{error}</div>}
                <button
                    type="submit"
                    className="btn btn-primary"
                    style={{ marginTop: 12, display: "inline-flex", alignItems: "center", gap: 8 }}
                    disabled={saving}
                >
                    {saving ? <Loader2 size={15} className="spin" /> : <Save size={15} />} {saving ? "Saving..." : "Save & Continue"}
                </button>
                <span style={{ marginLeft: 10, fontSize: 12, color: "var(--text-muted,#64748B)" }}>
                    <User size={12} style={{ verticalAlign: -2 }} /> You can add more details later from your profile.
                </span>
            </form>
        </div>
    );
}

export default ProfileStep;