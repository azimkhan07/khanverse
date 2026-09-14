import { useEffect, useRef } from "react";
import { useAuth } from "../../contexts/AuthContext";
import { showDialog } from "./Dialog";

function LoginProfilePrompt() {
    const { user } = useAuth();
    const handled = useRef(null);

    useEffect(() => {
        if (!user || user.role === "admin") return;
        const pct = Number(user.completeness_percent) || 0;
        if (pct >= 75) return;
        if (handled.current === user.id) return;

        const key = `profile_prompt_${user.id}`;
        if (sessionStorage.getItem(key)) return;

        handled.current = user.id;
        sessionStorage.setItem(key, "1");

        const stepsLeft = Array.isArray(user.missing) ? user.missing.length : 0;
        const pctText = Number.isInteger(pct) ? pct : Math.round(pct);

        showDialog({
            type: "info",
            title: "Complete Your Profile",
            message:
                `Your profile is ${pctText}% complete. Please complete your profile to unlock your full experience.` +
                (stepsLeft ? ` ${stepsLeft} step${stepsLeft > 1 ? "s" : ""} remaining.` : ""),
            confirmText: "Complete Profile",
        }).then((open) => {
            if (open) window.location.href = "/profile";
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [user?.id, user?.role, user?.completeness_percent]);

    return null;
}

export default LoginProfilePrompt;