import { useEffect, useState } from "react";
import frontendApi from "../shared/frontendApi";

let cache = null;
let promise = null;

const EMPTY_STATS = {
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

function toSections(raw) {
    const map = {};
    if (Array.isArray(raw)) {
        raw.forEach((s) => {
            if (s.section_key) map[s.section_key] = s;
        });
    }
    return map;
}

function fetchHomeData() {
    if (cache) return Promise.resolve(cache);
    if (!promise) {
        promise = frontendApi
            .get("/frontend/home")
            .then((res) => {
                const sections = toSections(res.data?.sections);
                const stats = res.data?.stats;
                cache = {
                    sections,
                    stats: stats && typeof stats === "object"
                        ? { ...EMPTY_STATS, ...stats }
                        : { ...EMPTY_STATS },
                };
                return cache;
            })
            .catch((err) => {
                console.error("Failed to load home data:", err);
                cache = { sections: {}, stats: { ...EMPTY_STATS } };
                return cache;
            })
            .finally(() => {
                promise = null;
            });
    }
    return promise;
}

export default function useSiteSections() {
    const [data, setData] = useState(cache || { sections: {}, stats: { ...EMPTY_STATS } });

    useEffect(() => {
        let active = true;
        fetchHomeData().then((result) => {
            if (active) setData(result);
        });
        return () => { active = false; };
    }, []);

    return data;
}