import { useEffect } from "react";
import { useLocation } from "react-router-dom";

function ScrollToTop() {
    const { pathname, search, hash } = useLocation();

    useEffect(() => {
        if (hash) {
            const el = document.querySelector(hash);
            if (el) {
                el.scrollIntoView({ behavior: "smooth", block: "start" });
            } else {
                window.scrollTo({ top: 0, left: 0, behavior: "instant" });
            }
            return;
        }
        window.scrollTo({ top: 0, left: 0, behavior: "instant" });
    }, [pathname, search, hash]);

    return null;
}

export default ScrollToTop;
