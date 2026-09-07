import { useEffect, useRef, useState } from "react";
import { useNavigate } from "react-router-dom";
import { X, Smartphone, Download, MonitorSmartphone, ArrowRight } from "lucide-react";
import frontendApi from "../../shared/frontendApi";

const DISMISS_SESSION_KEY = "khanverse-install-dismissed-session";

function isMobileOrTablet() {
    try {
        const coarse = window.matchMedia("(pointer: coarse)").matches;
        const small = window.matchMedia("(max-width: 1023px)").matches;
        const touch = navigator.maxTouchPoints > 0 || "ontouchstart" in window;
        return coarse && small && touch;
    } catch {
        return false;
    }
}

function InstallAppPrompt() {
    const navigate = useNavigate();
    const [info, setInfo] = useState(null);
    const [ready, setReady] = useState(false);
    const [canPrompt, setCanPrompt] = useState(false);
    const [dismissed, setDismissed] = useState(() => {
        try { return sessionStorage.getItem(DISMISS_SESSION_KEY) === "1"; } catch { return false; }
    });
    const deferredPrompt = useRef(null);

    useEffect(() => {
        const onPrompt = (e) => {
            e.preventDefault();
            deferredPrompt.current = e;
            setCanPrompt(true);
        };
        window.addEventListener("beforeinstallprompt", onPrompt);
        return () => window.removeEventListener("beforeinstallprompt", onPrompt);
    }, []);

    useEffect(() => {
        if (!isMobileOrTablet()) return;
        let active = true;
        frontendApi
            .get("/frontend/install")
            .then((res) => {
                if (!active) return;
                setInfo(res.data || {});
            })
            .catch(() => {});
        const t = setTimeout(() => active && setReady(true), 1200);
        return () => { active = false; clearTimeout(t); };
    }, []);

    if (!isMobileOrTablet() || dismissed || !info?.enabled || !ready) return null;

    const brand = "KhanVerse";
    const apk = info.apk;

    const continueBrowsing = () => {
        try { sessionStorage.setItem(DISMISS_SESSION_KEY, "1"); } catch { /* ignore */ }
        setDismissed(true);
    };

    const useApp = async () => {
        const prompt = deferredPrompt.current;
        if (prompt) {
            await prompt.prompt();
            deferredPrompt.current = null;
            continueBrowsing();
            return;
        }
        if (apk && apk.url) {
            window.location.href = apk.url;
            continueBrowsing();
            return;
        }
        navigate("/app");
    };

    return (
        <>
            <div style={{
                position: "fixed", inset: 0, zIndex: 9998,
                background: "rgba(2,6,23,.4)", backdropFilter: "blur(3px)", WebkitBackdropFilter: "blur(3px)",
            }} onClick={continueBrowsing} />
            <div style={{
                position: "fixed", left: 10, right: 10, bottom: 12, zIndex: 9999,
                maxWidth: 480, margin: "0 auto",
                background: "#ffffff", borderRadius: 20,
                boxShadow: "0 24px 60px rgba(2,6,23,.4)",
                border: "1px solid rgba(79,70,229,.18)",
                padding: "18px 18px 16px",
            }}>
                <button
                    onClick={continueBrowsing}
                    aria-label="Dismiss"
                    style={{
                        position: "absolute", top: 8, right: 10, border: "none", background: "transparent",
                        color: "#94a3b8", cursor: "pointer", padding: 4, lineHeight: 0, zIndex: 1,
                    }}
                >
                    <X size={18} />
                </button>

                <div style={{ display: "flex", gap: 12, alignItems: "center" }}>
                    <div style={{
                        width: 48, height: 48, borderRadius: 14, flexShrink: 0,
                        display: "flex", alignItems: "center", justifyContent: "center",
                        background: "linear-gradient(135deg,#4F46E5,#7C3AED)", color: "#fff",
                    }}>
                        <Smartphone size={24} />
                    </div>
                    <div style={{ flex: 1, minWidth: 0 }}>
                        <div style={{ fontSize: 15.5, fontWeight: 800, color: "#0f172a", display: "flex", alignItems: "center", gap: 6, flexWrap: "wrap" }}>
                            Install {brand} App
                            {apk?.version && <span style={{ fontSize: 10.5, fontWeight: 700, color: "#4F46E5", background: "#EEF2FF", borderRadius: 99, padding: "2px 8px" }}>v{apk.version}</span>}
                        </div>
                        <div style={{ fontSize: 12.5, color: "#64748b", marginTop: 3, lineHeight: 1.45 }}>
                            {canPrompt
                                ? "Get faster access with the " + brand + " mobile app."
                                : apk
                                    ? "Download " + brand + " app (" + (apk.size || "APK") + ") for the best experience."
                                    : "Open " + brand + " faster — install the mobile app."}
                        </div>
                    </div>
                </div>

                <div style={{ display: "flex", gap: 10, marginTop: 16 }}>
                    <button
                        onClick={continueBrowsing}
                        style={{
                            flex: 1, border: "1px solid #E2E8F0", cursor: "pointer", borderRadius: 12,
                            background: "#fff", color: "#475569", fontWeight: 600, fontSize: 13.5,
                            padding: "12px 10px", display: "flex", alignItems: "center", justifyContent: "center", gap: 6,
                        }}
                    >
                        Continue Browsing
                    </button>
                    <button
                        onClick={useApp}
                        style={{
                            flex: 1, border: "none", cursor: "pointer", borderRadius: 12,
                            background: "linear-gradient(135deg,#4F46E5,#7C3AED)", color: "#fff",
                            fontWeight: 700, fontSize: 13.5, padding: "12px 10px",
                            display: "flex", alignItems: "center", justifyContent: "center", gap: 6,
                        }}
                    >
                        {canPrompt ? <MonitorSmartphone size={16} /> : <Download size={16} />}
                        Use App {!canPrompt && !apk?.url && <ArrowRight size={16} />}
                    </button>
                </div>
            </div>
        </>
    );
}

export default InstallAppPrompt;