import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { Smartphone, Download, ExternalLink, CheckCircle2, XCircle } from "lucide-react";
import { Link } from "react-router-dom";
import frontendApi from "../../shared/frontendApi";

const DEEP_LINK_SCHEME = "khanverse://";
const DOWNLOAD_URL = "/app/khanverse.apk";

function AppDownload() {
    const [appUrl, setAppUrl] = useState(DOWNLOAD_URL);
    const [status, setStatus] = useState("idle");

    useEffect(() => {
        frontendApi.get("/frontend/app-settings").then((res) => {
            if (res.data?.download_url) setAppUrl(res.data.download_url);
        }).catch(() => {});
    }, []);

    const openApp = () => {
        setStatus("opening");
        const timeout = window.setTimeout(() => {
            setStatus("not-installed");
        }, 2500);
        window.location.href = `${DEEP_LINK_SCHEME}open`;
        window.setTimeout(() => window.clearTimeout(timeout), 3000);
    };

    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

    return (
        <div style={{ background: "var(--bg-body, #F8FAFC)", minHeight: "80vh", padding: "60px 24px" }}>
            <div style={{ maxWidth: 720, margin: "0 auto", textAlign: "center" }}>
                <div style={{ width: 88, height: 88, margin: "0 auto 20px", borderRadius: 24, display: "flex", alignItems: "center", justifyContent: "center",
                    background: "linear-gradient(135deg,#4F46E5,#7C3AED)", color: "#fff", boxShadow: "0 16px 40px rgba(79,70,229,.35)" }}>
                    <Smartphone size={44} />
                </div>
                <h1 style={{ fontSize: "clamp(30px,5vw,44px)", fontWeight: 800, color: "var(--text-primary, #0F172A)", letterSpacing: "-1px" }}>
                    KhanVerse App
                </h1>
                <p style={{ color: "var(--text-muted, #64748B)", marginTop: 12, lineHeight: 1.7, maxWidth: 460, marginLeft: "auto", marginRight: "auto" }}>
                    Take the marketplace with you. Fast, secure and always in sync with your web dashboard.
                </p>

                <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}
                    style={{ marginTop: 34, padding: 28, borderRadius: 20, background: "var(--bg-card, #fff)", border: "1px solid var(--border-color, #E2E8F0)", boxShadow: "0 12px 40px rgba(15,23,42,.08)" }}>
                    <button onClick={openApp}
                        style={{ width: "100%", padding: "16px 24px", borderRadius: 14, border: "none", cursor: "pointer", fontSize: 16, fontWeight: 700,
                            background: "linear-gradient(135deg,#4F46E5,#7C3AED)", color: "#fff", display: "flex", alignItems: "center", justifyContent: "center", gap: 10 }}>
                        <ExternalLink size={18} /> Open in App
                    </button>

                    {status === "opening" && <p style={{ marginTop: 14, color: "var(--text-muted, #64748B)" }}>Opening KhanVerse…</p>}
                    {status === "not-installed" && (
                        <div style={{ marginTop: 14 }}>
                            <p style={{ display: "flex", alignItems: "center", justifyContent: "center", gap: 6, color: "#d97706", marginBottom: 12 }}>
                                <XCircle size={16} /> App not detected on this device.
                            </p>
                            <a href={appUrl} download
                                style={{ display: "block", padding: "14px 24px", borderRadius: 14, textDecoration: "none", color: "#fff", fontWeight: 700,
                                    background: "linear-gradient(135deg,#059669,#10B981)", border: "none", cursor: "pointer" }}>
                                <Download size={18} style={{ verticalAlign: "middle", marginRight: 8 }} /> Download APK
                            </a>
                        </div>
                    )}

                    {isMobile && status !== "not-installed" && (
                        <p style={{ marginTop: 16, fontSize: 13, color: "var(--text-muted, #64748B)" }}>
                            If the app isn't installed, you'll see a download button.
                        </p>
                    )}
                </motion.div>

                <div style={{ marginTop: 26, display: "flex", justifyContent: "center", gap: 10, flexWrap: "wrap" }}>
                    {["Secure login", "Track orders", "Instant chat", "Real-time updates"].map((f) => (
                        <span key={f} style={{ display: "inline-flex", alignItems: "center", gap: 5, padding: "8px 14px", borderRadius: 50, fontSize: 13,
                            background: "var(--bg-card, #fff)", border: "1px solid var(--border-color, #E2E8F0)", color: "var(--text-secondary, #475569)" }}>
                            <CheckCircle2 size={14} style={{ color: "var(--accent, #4F46E5)" }} /> {f}
                        </span>
                    ))}
                </div>

                <p style={{ marginTop: 26, color: "var(--text-light, #94A3B8)", fontSize: 13 }}>
                    Prefer the web? <Link to="/" style={{ color: "var(--accent, #4F46E5)" }}>Continue on website</Link>
                </p>
            </div>
        </div>
    );
}

export default AppDownload;
