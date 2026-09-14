import { useEffect, useState } from "react";
import { Home, Loader2, User } from "lucide-react";
import api from "../api";

function ProfileGate({ base, roleLabel, children }) {
    const [checking, setChecking] = useState(true);
    const [complete, setComplete] = useState(true);

    useEffect(() => {
        let active = true;
        api.get("/role-record")
            .then(({ data }) => {
                if (!active) return;
                setComplete(Boolean(data.complete));
            })
            .catch(() => {})
            .finally(() => {
                if (active) setChecking(false);
            });
        return () => { active = false; };
    }, []);

    if (checking) {
        return (
            <div style={{ minHeight: "60vh", display: "flex", alignItems: "center", justifyContent: "center" }}>
                <Loader2 size={26} className="spin" style={{ color: "var(--accent,#4F46E5)" }} />
            </div>
        );
    }

    if (!complete) {
        return (
            <div className="profile-gate">
                <style>{`
                    .profile-gate { min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
                    .pg-card { max-width: 480px; width: 100%; text-align: center; background: var(--bg-card,#fff); border: 1px solid var(--border-color,#E2E8F0); border-radius: 20px; padding: 40px 32px; box-shadow: var(--shadow-sm,0 1px 3px rgba(0,0,0,.06)); }
                    .pg-icon { width: 72px; height: 72px; margin: 0 auto 18px; border-radius: 22px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; }
                    .pg-card h2 { font-size: 22px; font-weight: 800; color: var(--text-primary,#0F172A); margin: 0 0 8px; }
                    .pg-card p { color: var(--text-muted,#64748B); font-size: 14px; line-height: 1.6; margin: 0 0 24px; }
                    .pg-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
                    .pg-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; text-decoration: none; font-family: inherit; border: none; }
                    .pg-primary { background: linear-gradient(135deg,#4F46E5,#7C3AED); color: #fff; box-shadow: 0 6px 18px rgba(79,70,229,.28); }
                    .pg-ghost { background: transparent; color: var(--text-secondary,#475569); border: 1.5px solid var(--border-color,#E2E8F0); }
                `}</style>
                <div className="pg-card">
                    <div className="pg-icon"><User size={30} /></div>
                    <h2>Complete your {roleLabel || base} profile</h2>
                    <p>
                        To unlock your {roleLabel || base} dashboard and start using all its features,
                        please finish filling your profile details on the website.
                    </p>
                    <div className="pg-actions">
                        <button className="pg-btn pg-primary" onClick={() => { window.location.href = "/profile"; }}>
                            <User size={15} /> Complete Profile
                        </button>
                        <a className="pg-btn pg-ghost" href="/">
                            <Home size={15} /> Back to Website
                        </a>
                    </div>
                </div>
            </div>
        );
    }

    return children;
}

export default ProfileGate;