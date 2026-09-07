import { useEffect, useState } from "react";
import frontendApi from "../../shared/frontendApi";

const EMPTY = {
    services: 0,
    sellers: 0,
    buyers: 0,
    categories: 0,
    projects: 0,
    delivered: 0,
    countries: 0,
    rating: 0,
    reviews: 0,
};

export default function useHomeStats() {
    const [stats, setStats] = useState(EMPTY);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        let active = true;
        frontendApi
            .get("/frontend/home")
            .then((res) => {
                if (!active) return;
                const s = res.data?.stats;
                if (s && typeof s === "object") {
                    setStats({ ...EMPTY, ...s });
                }
            })
            .catch((err) => {
                console.error("Failed to load home stats:", err);
            })
            .finally(() => active && setLoading(false));
        return () => { active = false; };
    }, []);

    return { stats, loading };
}