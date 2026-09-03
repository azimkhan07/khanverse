import { ArrowUp } from "lucide-react";
import {
    FaFacebookF,
    FaInstagram,
    FaLinkedinIn,
    FaXTwitter,
} from "react-icons/fa6";
import { Link } from "react-router-dom";
import { useState, useEffect, useMemo } from "react";
import { motion } from "framer-motion";
import { useAuth } from "../../contexts/AuthContext";
import frontendApi from "../../shared/frontendApi";
import footerStatic from "../../data/footer";
import "../../theme/css/footer.css";

const socialIcons = {
    facebook: FaFacebookF,
    instagram: FaInstagram,
    linkedin: FaLinkedinIn,
    twitter: FaXTwitter,
};

const sectionVariant = {
    hidden: { opacity: 0, y: 40 },
    visible: (i) => ({
        opacity: 1,
        y: 0,
        transition: { delay: i * 0.1, duration: 0.5, ease: "easeOut" },
    }),
};

function Footer() {
    const { user } = useAuth();
    const [footer, setFooter] = useState(footerStatic);

    useEffect(() => {
        frontendApi
            .get("/frontend/footer")
            .then((res) => {
                const data = res.data?.data || res.data;
                if (data && data.sections) setFooter(data);
            })
            .catch(() => {});
    }, []);

    const displaySections = useMemo(() => {
        return footer.sections.map((section) => {
            if (section.title === "Marketplace" && user) {
                const extra = [];
                if (user.role !== "seller" && user.role !== 2) {
                    extra.push({ label: "Become a Seller", url: "/register?role=seller" });
                }
                if (user.role !== "buyer" && user.role !== 3) {
                    extra.push({ label: "Become a Buyer", url: "/register?role=buyer" });
                }
                return { ...section, links: [...section.links, ...extra] };
            }
            return section;
        });
    }, [footer.sections, user]);

    return (
        <footer className="footer">
            <div className="container">
                <div className="footer-grid">
                    <motion.div
                        className="footer-brand"
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-50px" }}
                        variants={sectionVariant}
                        custom={0}
                    >
                        <Link to="/" className="footer-logo-link">
                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                                <rect width="36" height="36" rx="10" fill="url(#fLogoGrad)" />
                                <path d="M10 26V10h4.5l3.5 10 3.5-10H26v16h-3V14l-3.5 10h-3L16 14v12h-3z" fill="#fff" />
                                <defs>
                                    <linearGradient id="fLogoGrad" x1="0" y1="0" x2="36" y2="36">
                                        <stop stopColor="#4F46E5" />
                                        <stop offset="1" stopColor="#7C3AED" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <span className="footer-logo-text">KhanVerse</span>
                        </Link>
                        <p className="footer-desc">{footer.company.description}</p>
                        <div className="footer-social">
                            {footer.company.social.map((social) => {
                                if (!social.visible) return null;
                                const Icon = socialIcons[social.icon];
                                return (
                                    <motion.a
                                        href={social.url}
                                        key={social.id}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        whileHover={{ scale: 1.15, y: -3 }}
                                        whileTap={{ scale: 0.9 }}
                                    >
                                        {Icon && <Icon size={15} />}
                                    </motion.a>
                                );
                            })}
                        </div>
                    </motion.div>

                    {displaySections.map((section, i) => (
                        <motion.div
                            className="footer-col"
                            key={section.id}
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true, margin: "-50px" }}
                            variants={sectionVariant}
                            custom={i + 1}
                        >
                            <h3>{section.title}</h3>
                            <ul>
                                {section.links.map((link, index) => (
                                    <li key={index}>
                                        <motion.div
                                            whileHover={{ x: 6 }}
                                            transition={{ type: "spring", stiffness: 300, damping: 20 }}
                                        >
                                            <Link to={link.url}>{link.label}</Link>
                                        </motion.div>
                                    </li>
                                ))}
                            </ul>
                        </motion.div>
                    ))}
                </div>

                <motion.div
                    className="footer-newsletter"
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, margin: "-50px" }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                >
                    <div className="newsletter-text">
                        <h4>Stay updated</h4>
                        <p>Get the latest news and updates from KhanVerse.</p>
                    </div>
                    <div className="newsletter-form">
                        <input type="email" placeholder="Enter your email" />
                        <motion.button
                            whileHover={{ scale: 1.03 }}
                            whileTap={{ scale: 0.97 }}
                        >
                            Subscribe
                        </motion.button>
                    </div>
                </motion.div>

                <div className="footer-bottom">
                    <p>{footer.copyright}</p>
                    <div className="footer-bottom-links">
                        <Link to="/privacy-policy">Privacy</Link>
                        <Link to="/terms-conditions">Terms</Link>
                        <Link to="/cookie-policy">Cookies</Link>
                    </div>
                    <motion.button
                        className="back-top"
                        onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}
                        aria-label="Back to top"
                        whileHover={{ scale: 1.15, y: -4 }}
                        whileTap={{ scale: 0.9 }}
                    >
                        <ArrowUp size={18} />
                    </motion.button>
                </div>
            </div>
        </footer>
    );
}

export default Footer;
