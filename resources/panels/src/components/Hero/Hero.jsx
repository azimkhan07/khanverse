import { Search, ArrowRight, Sparkles, Shield, Zap, Star } from "lucide-react";
import { Link, useNavigate } from "react-router-dom";
import { motion, useScroll, useTransform } from "framer-motion";
import { Fragment, useEffect, useRef, useState } from "react";
import { useAuth } from "../../contexts/AuthContext";
import frontendApi from "../../shared/frontendApi";
import "../../theme/css/hero.css";
import heroImg from "../../theme/images/4.jpg";
import heroBg from "../../theme/images/hero.png";

const container = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.12 } },
};

const fadeUp = {
    hidden: { opacity: 0, y: 30 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } },
};

const slideLeft = {
    hidden: { opacity: 0, x: -60 },
    visible: { opacity: 1, x: 0, transition: { duration: 0.7, ease: "easeOut" } },
};

const fadeIn = {
    hidden: { opacity: 0, scale: 0.95 },
    visible: { opacity: 1, scale: 1, transition: { duration: 0.8, ease: "easeOut", delay: 0.3 } },
};

function Hero() {
    const { user } = useAuth();
    const navigate = useNavigate();
    const ref = useRef(null);
    const [query, setQuery] = useState("");
    const [stats, setStats] = useState(null);

    const { scrollYProgress } = useScroll({
        target: ref,
        offset: ["start start", "end start"],
    });

    const auroraY = useTransform(scrollYProgress, [0, 1], [0, 160]);
    const contentY = useTransform(scrollYProgress, [0, 1], [0, -60]);
    const contentOpacity = useTransform(scrollYProgress, [0, 0.85], [1, 0]);
    const cardY = useTransform(scrollYProgress, [0, 1], [0, -120]);
    const cardScale = useTransform(scrollYProgress, [0, 1], [1, 1.08]);
    const cardRotate = useTransform(scrollYProgress, [0, 1], [0, -4]);
    const floatingY = useTransform(scrollYProgress, [0, 1], [0, -40]);
    const bgScale = useTransform(scrollYProgress, [0, 1], [1.2, 1]);
    const bgY = useTransform(scrollYProgress, [0, 1], [0, 80]);

    useEffect(() => {
        frontendApi
            .get("/frontend/home")
            .then((res) => {
                const s = res.data?.stats;
                if (s) setStats(s);
            })
            .catch(() => {});
    }, []);

    const submitSearch = (e) => {
        e.preventDefault();
        const term = query.trim();
        navigate(term ? `/search?q=${encodeURIComponent(term)}` : "/search");
    };

    const heroStatBlocks = [
        stats && { value: stats.sellers, label: "Freelancers", icon: Star, fill: "#FBBF24" },
        stats && { value: stats.services, label: "Active Services", icon: Zap },
        stats && { value: stats.delivered, label: "Orders Delivered", icon: Shield },
    ].filter(Boolean).filter((s) => s.value > 0);

    const showFlower = stats && (stats.delivered > 0 || stats.sellers > 0 || stats.services > 0);

    return (
        <section className="hero" ref={ref}>
            <motion.div
                className="hero-bg"
                style={{ backgroundImage: `url(${heroBg})`, scale: bgScale, y: bgY }}
            />
            <div className="hero-overlay" />
            <motion.div className="hero-aurora aurora-1" style={{ y: auroraY }} />
            <motion.div className="hero-aurora aurora-2" style={{ y: auroraY }} />
            <motion.div className="hero-aurora aurora-3" style={{ y: auroraY }} />

            <motion.div className="hero-container" style={{ y: contentY, opacity: contentOpacity }}>
                <motion.div
                    className="hero-left"
                    variants={container}
                    initial="hidden"
                    animate="visible"
                >
                    <motion.div className="hero-badge" variants={fadeUp}>
                        <motion.div
                            animate={{ scale: [1, 1.2, 1] }}
                            transition={{ duration: 2, repeat: Infinity, ease: "easeInOut" }}
                        >
                            <Sparkles size={14} />
                        </motion.div>
                        <span>World's #1 AI Freelance Marketplace</span>
                    </motion.div>

                    <motion.h1 variants={slideLeft}>
                        Find the Perfect
                        <span className="hero-gradient-text"> Digital Service </span>
                        For Your Business
                    </motion.h1>

                    <motion.p className="hero-desc" variants={fadeUp}>
                        Hire verified freelancers for web development,
                        graphic design, AI solutions, marketing and much more.
                    </motion.p>

                    <motion.form className="hero-search" variants={fadeUp} onSubmit={submitSearch}>
                        <Search size={18} className="hero-search-icon" />
                        <input
                            type="text"
                            placeholder="What service are you looking for?"
                            value={query}
                            onChange={(e) => setQuery(e.target.value)}
                        />
                        <button className="hero-search-btn" type="submit">
                            Search
                        </button>
                    </motion.form>

                    <motion.div className="popular-tags" variants={fadeUp}>
                        <span className="tags-label">Popular :</span>
                        {["Logo Design", "Web Dev", "AI", "SEO", "UI/UX"].map((tag) => (
                            <motion.div
                                key={tag}
                                whileHover={{ scale: 1.08, y: -2 }}
                                whileTap={{ scale: 0.95 }}
                                style={{ display: "inline-block" }}
                            >
                                <Link to={`/search?q=${encodeURIComponent(tag)}`} className="tag-item">
                                    {tag}
                                </Link>
                            </motion.div>
                        ))}
                    </motion.div>

                    {heroStatBlocks.length > 0 && (
                        <motion.div className="hero-stats" variants={fadeUp}>
                            {heroStatBlocks.map((item, i) => {
                                const Icon = item.icon;
                                return (
                                    <Fragment key={item.label}>
                                        {i > 0 && <div className="hero-stat-divider" />}
                                        <div className="hero-stat">
                                            <div className="hero-stat-icon">
                                                <Icon size={16} fill={item.fill || "none"} color={item.fill || "currentColor"} />
                                            </div>
                                            <div>
                                                <h3>{Number(item.value).toLocaleString("en-IN")}+</h3>
                                                <span>{item.label}</span>
                                            </div>
                                        </div>
                                    </Fragment>
                                );
                            })}
                        </motion.div>
                    )}
                </motion.div>

                <motion.div
                    className="hero-right"
                    variants={fadeIn}
                    initial="hidden"
                    animate="visible"
                    style={{ y: cardY, scale: cardScale, rotate: cardRotate }}
                >
                    <div className="hero-card">
                        <motion.img
                            src={heroImg}
                            alt="SkillNest Platform"
                            initial={{ opacity: 0, scale: 1.05 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 1, delay: 0.4 }}
                        />
                        {showFlower && (
                            <motion.div
                                className="floating-card fc-1"
                                animate={{ y: [0, -10, 0] }}
                                transition={{ duration: 3, repeat: Infinity, ease: "easeInOut" }}
                                style={{ y: floatingY }}>
                                <div className="fc-avatar fc-avatar-yellow">⭐</div>
                                <div>
                                    <strong>{stats.rating > 0 ? stats.rating : "New"}</strong>
                                    <small>{stats.rating > 0 ? "Avg Rating" : "No reviews yet"}</small>
                                </div>
                            </motion.div>
                        )}
                        <motion.div
                            className="floating-card fc-2"
                            animate={{ y: [0, -10, 0] }}
                            transition={{ duration: 3.5, repeat: Infinity, ease: "easeInOut", delay: 0.5 }}
                            style={{ y: floatingY }}>
                            <div className="fc-avatar fc-avatar-blue">🚀</div>
                            <div>
                                <strong>{Number(stats?.delivered || 0).toLocaleString("en-IN")}</strong>
                                <small>Orders Delivered</small>
                            </div>
                        </motion.div>
                        <motion.div
                            className="floating-card fc-3"
                            animate={{ y: [0, -10, 0] }}
                            transition={{ duration: 4, repeat: Infinity, ease: "easeInOut", delay: 1 }}
                            style={{ y: floatingY }}>
                            <div className="fc-avatar fc-avatar-green">💼</div>
                            <div>
                                <strong>{Number(stats?.sellers || 0).toLocaleString("en-IN")}</strong>
                                <small>Active Freelancers</small>
                            </div>
                        </motion.div>
                    </div>
                </motion.div>
            </motion.div>
        </section>
    );
}

export default Hero;