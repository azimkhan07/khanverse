import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { Play, Clock } from "lucide-react";
import frontendApi from "../../shared/frontendApi";

const embedUrl = (u) => {
    if (!u) return "";
    const yt = u.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{6,})/);
    if (yt) return `https://www.youtube.com/embed/${yt[1]}`;
    return u;
};

function Tutorials() {
    const [videos, setVideos] = useState([]);
    const [loading, setLoading] = useState(true);
    const [active, setActive] = useState(null);

    useEffect(() => {
        frontendApi.get("/frontend/tutorials")
            .then((res) => {
                const data = Array.isArray(res.data) ? res.data : [];
                setVideos(data);
                if (data.length) setActive(data[0]);
            })
            .catch(() => {})
            .finally(() => setLoading(false));
    }, []);

    return (
        <div style={{ background: "var(--bg-body, #F8FAFC)", minHeight: "80vh", padding: "60px 24px" }}>
            <div style={{ maxWidth: 1080, margin: "0 auto" }}>
                <div style={{ textAlign: "center", marginBottom: 44 }}>
                    <h1 style={{ fontSize: "clamp(28px,4vw,40px)", fontWeight: 800, color: "var(--text-primary, #0F172A)", letterSpacing: "-1px" }}>
                        How to use KhanVerse
                    </h1>
                    <p style={{ color: "var(--text-muted, #64748B)", marginTop: 8 }}>
                        Watch these short tutorials to get the most out of the platform.
                    </p>
                </div>

                {loading ? (
                    <div style={{ textAlign: "center", padding: 60, color: "var(--text-muted, #64748B)" }}>Loading…</div>
                ) : videos.length === 0 ? (
                    <div style={{ textAlign: "center", padding: 60, color: "var(--text-muted, #64748B)" }}>No tutorials available yet.</div>
                ) : (
                    <div style={{ display: "grid", gridTemplateColumns: "1.6fr 1fr", gap: 24, alignItems: "start" }}>
                        <div>
                            {active && (
                                <motion.div key={active.id} initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.4 }}
                                    style={{ background: "var(--bg-card, #fff)", border: "1px solid var(--border-color, #E2E8F0)", borderRadius: 20, overflow: "hidden", boxShadow: "0 12px 40px rgba(15,23,42,.08)" }}>
                                    <iframe
                                        src={embedUrl(active.video_url)}
                                        title={active.title}
                                        style={{ width: "100%", aspectRatio: "16/9", border: 0, background: "#000" }}
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowFullScreen
                                    />
                                    <div style={{ padding: 22 }}>
                                        <h2 style={{ fontSize: 20, fontWeight: 700, color: "var(--text-primary, #0F172A)" }}>{active.title}</h2>
                                        {active.duration && (
                                            <span style={{ display: "inline-flex", alignItems: "center", gap: 5, marginTop: 6, fontSize: 13, color: "var(--text-muted, #64748B)" }}>
                                                <Clock size={14} /> {active.duration}
                                            </span>
                                        )}
                                        {active.description && (
                                            <p style={{ color: "var(--text-muted, #64748B)", lineHeight: 1.7, marginTop: 12 }}>{active.description}</p>
                                        )}
                                    </div>
                                </motion.div>
                            )}
                        </div>
                        <div style={{ display: "flex", flexDirection: "column", gap: 10 }}>
                            {videos.map((v) => (
                                <button key={v.id} onClick={() => setActive(v)}
                                    style={{ display: "flex", alignItems: "center", gap: 12, padding: 14, borderRadius: 14, cursor: "pointer", textAlign: "left",
                                        border: active?.id === v.id ? "1.5px solid var(--accent, #4F46E5)" : "1px solid var(--border-color, #E2E8F0)",
                                        background: active?.id === v.id ? "var(--accent-light, rgba(79,70,229,.08))" : "var(--bg-card, #fff)",
                                        color: "var(--text-primary, #0F172A)" }}>
                                    <span style={{ width: 36, height: 36, borderRadius: 10, flexShrink: 0, display: "flex", alignItems: "center", justifyContent: "center",
                                        background: active?.id === v.id ? "linear-gradient(135deg,#4F46E5,#7C3AED)" : "var(--bg-badge, #F1F5F9)", color: active?.id === v.id ? "#fff" : "var(--accent, #4F46E5)" }}>
                                        <Play size={16} />
                                    </span>
                                    <div style={{ minWidth: 0 }}>
                                        <div style={{ fontWeight: 600, fontSize: 14, whiteSpace: "nowrap", overflow: "hidden", textOverflow: "ellipsis" }}>{v.title}</div>
                                        {v.duration && <div style={{ fontSize: 12, color: "var(--text-muted, #64748B)", marginTop: 2 }}>{v.duration}</div>}
                                    </div>
                                </button>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

export default Tutorials;
