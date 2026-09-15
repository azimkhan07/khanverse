import { useEffect, useState } from "react";
import frontendApi from "../shared/frontendApi";
import fallbackBlog from "../data/blog";

let cache = null;
let inflight = null;

function entryToText(entry) {
    if (!entry) return "";
    if (typeof entry === "string") return entry;
    if (Array.isArray(entry)) return entry.join(" ");
    return "";
}

function stripHtml(html) {
    const div = document.createElement("div");
    div.innerHTML = html || "";
    return div.textContent || div.innerText || "";
}

function excerptFrom(entry) {
    const text = stripHtml(entryToText(entry || "")).trim();
    if (!text) return "";
    return text.length > 160 ? `${text.slice(0, 160)}…` : text;
}

function formatDate(value) {
    if (!value) return "";
    const d = new Date(value);
    if (isNaN(d.getTime())) return "";
    return d.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
}

function normalize(posts) {
    return posts.map((item, i) => {
        const fallback = fallbackBlog[i % fallbackBlog.length];
        const content = typeof item.content === "string" && item.content.trim() ? item.content : (fallback?.content || []);
        return {
            id: item.id,
            slug: item.slug,
            title: item.title,
            category: item.category || fallback?.category || "",
            author: item.author || fallback?.author || "",
            date: formatDate(item.published_at) || fallback?.date || "",
            image: item.cover_image_url || fallback?.image || "",
            description: item.excerpt || excerptFrom(content) || fallback?.description || "",
            content,
        };
    });
}

function load() {
    if (cache) return Promise.resolve(cache);
    if (inflight) return inflight;
    inflight = frontendApi
        .get("/frontend/blog")
        .then((res) => {
            const list = Array.isArray(res.data) ? res.data : [];
            const merged = list.length ? list : fallbackBlog;
            cache = normalize(merged);
            return cache;
        })
        .catch(() => {
            cache = normalize(fallbackBlog);
            return cache;
        });
    return inflight;
}

export default function useBlogPosts() {
    const [state, setState] = useState({ posts: cache ? cache.slice() : [], loading: !cache });

    useEffect(() => {
        let alive = true;
        load().then((posts) => {
            if (alive) setState({ posts: posts.slice(), loading: false });
        });
        return () => { alive = false; };
    }, []);

    return state;
}