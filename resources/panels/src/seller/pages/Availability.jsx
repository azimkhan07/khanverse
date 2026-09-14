import { useEffect, useState } from "react";
import { Loader2, CalendarRange, Save, Clock, Coffee } from "lucide-react";
import api from "../../shared/api";
import { showDialog } from "../../shared/components/Dialog";

const DAY_NAMES = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

const defaultDays = () =>
    DAY_NAMES.map((name, day_of_week) => ({
        day_of_week,
        name,
        enabled: false,
        is_off: false,
        start_time: "09:00",
        end_time: "18:00",
    }));

function Availability() {
    const [days, setDays] = useState(defaultDays());
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        api.get("/availability")
            .then(({ data }) => {
                const existing = data.availability || [];
                setDays((prev) =>
                    prev.map((d) => {
                        const row = existing.find((e) => e.day_of_week === d.day_of_week);
                        if (!row) return d;
                        return {
                            ...d,
                            enabled: true,
                            is_off: row.is_off,
                            start_time: row.start_time || "09:00",
                            end_time: row.end_time || "18:00",
                        };
                    })
                );
            })
            .finally(() => setLoading(false));
    }, []);

    const patch = (day_of_week, key, value) => {
        setDays((prev) => prev.map((d) => (d.day_of_week === day_of_week ? { ...d, [key]: value } : d)));
    };

    const save = async () => {
        setSaving(true);
        try {
            const payload = days.map(({ day_of_week, enabled, is_off, start_time, end_time }) => ({
                day_of_week,
                enabled,
                is_off,
                start_time,
                end_time,
            }));
            const res = await api.post(
                "/availability",
                { days: payload },
                { headers: { "Content-Type": "application/json" } }
            );
            await showDialog({ type: "success", title: "Availability Saved", message: res.data?.message || "Your weekly availability has been saved.", confirmText: "Great" });
        } catch (err) {
            window.dispatchEvent(new CustomEvent("app:toast", { detail: { type: "error", message: err.response?.data?.message || "Could not save availability." } }));
        } finally {
            setSaving(false);
        }
    };

    return (
        <div style={{ padding: 24 }}>
            <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: 20 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, margin: 0, display: "flex", alignItems: "center", gap: 10 }}>
                        <CalendarRange size={22} style={{ color: "var(--accent, #4F46E5)" }} /> Weekly Availability
                    </h1>
                    <p style={{ color: "var(--text-muted, #64748B)", fontSize: 13, margin: "6px 0 0" }}>
                        Tell buyers when you are available. Buyers can still request a service even if you are offline.
                    </p>
                </div>
                <button
                    onClick={save}
                    disabled={saving}
                    style={{
                        display: "inline-flex", alignItems: "center", gap: 8,
                        padding: "11px 22px", border: "none", borderRadius: 12, cursor: "pointer",
                        background: "linear-gradient(135deg,#4F46E5,#7C3AED)", color: "#fff",
                        fontSize: 14, fontWeight: 700, fontFamily: "inherit",
                        boxShadow: "0 6px 18px rgba(79,70,229,.28)",
                    }}
                >
                    {saving ? <Loader2 size={15} className="spin" /> : <Save size={15} />} {saving ? "Saving..." : "Save Schedule"}
                </button>
            </div>

            {loading ? (
                <div style={{ display: "flex", justifyContent: "center", padding: 60 }}>
                    <Loader2 size={26} className="spin" style={{ color: "var(--accent, #4F46E5)" }} />
                </div>
            ) : (
                <div style={{ display: "flex", flexDirection: "column", gap: 10, maxWidth: 760 }}>
                    {days.map((d) => (
                        <div
                            key={d.day_of_week}
                            style={{
                                display: "flex", alignItems: "center", gap: 14, flexWrap: "wrap",
                                padding: "14px 18px", borderRadius: 16,
                                background: "var(--bg-card, #fff)", border: "1px solid var(--border-color, #E2E8F0)",
                            }}
                        >
                            <label style={{ display: "flex", alignItems: "center", gap: 10, flex: "0 0 190px", cursor: "pointer" }}>
                                <input
                                    type="checkbox"
                                    checked={d.enabled}
                                    onChange={(e) => patch(d.day_of_week, "enabled", e.target.checked)}
                                    style={{ width: 16, height: 16, accentColor: "#4F46E5" }}
                                />
                                <span style={{ fontWeight: d.enabled ? 800 : 600, fontSize: 14 }}>{d.name}</span>
                            </label>

                            {d.enabled && !d.is_off && (
                                <>
                                    <span style={{ display: "inline-flex", alignItems: "center", gap: 6 }}>
                                        <Clock size={14} style={{ color: "var(--text-muted, #64748B)" }} />
                                        <input
                                            type="time"
                                            value={d.start_time}
                                            onChange={(e) => patch(d.day_of_week, "start_time", e.target.value)}
                                            style={{
                                                padding: "8px 10px", borderRadius: 10,
                                                border: "1px solid var(--border-color, #E2E8F0)",
                                                background: "var(--bg-input, #fff)", fontFamily: "inherit", fontSize: 13,
                                            }}
                                        />
                                    </span>
                                    <span style={{ color: "var(--text-muted, #64748B)" }}>to</span>
                                    <input
                                        type="time"
                                        value={d.end_time}
                                        onChange={(e) => patch(d.day_of_week, "end_time", e.target.value)}
                                        style={{
                                            padding: "8px 10px", borderRadius: 10,
                                            border: "1px solid var(--border-color, #E2E8F0)",
                                            background: "var(--bg-input, #fff)", fontFamily: "inherit", fontSize: 13,
                                        }}
                                    />
                                </>
                            )}

                            {d.enabled && (
                                <label style={{ display: "inline-flex", alignItems: "center", gap: 6, marginLeft: "auto", cursor: "pointer", fontSize: 13 }}>
                                    <input
                                        type="checkbox"
                                        checked={d.is_off}
                                        onChange={(e) => patch(d.day_of_week, "is_off", e.target.checked)}
                                    />
                                    <Coffee size={13} /> Day off
                                </label>
                            )}
                        </div>
                    ))}

                    <p style={{ color: "var(--text-muted, #64748B)", fontSize: 12, margin: "8px 2px 0" }}>
                        Tip: leave a day un-ticked to hide it entirely. "Day off" shows your day off to buyers.
                    </p>
                </div>
            )}
        </div>
    );
}

export default Availability;