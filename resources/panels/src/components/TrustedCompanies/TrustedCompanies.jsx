import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import "../../theme/css/hero.css";
import frontendApi from "../../shared/frontendApi";

const styleText = (name) => (
    <span style={{ fontSize: 20, fontWeight: 800, letterSpacing: 0.5, color: 'var(--text-muted)', whiteSpace: 'nowrap' }}>{name}</span>
);

const logoUrl = (c) => {
    if (!c.logo_url) return null;
    return c.logo_url.startsWith('http') ? c.logo_url : `/storage/${c.logo_url}`;
};

function BrandLogo({ company }) {
    const src = logoUrl(company);
    if (src) {
        return (
            <div className="trusted-logo-img" title={company.name}>
                <img src={src} alt={company.name} loading="lazy" style={{ maxHeight: 32, maxWidth: 140, objectFit: 'contain' }} />
            </div>
        );
    }
    return styleText(company.name);
}

function TrustedCompanies() {
    const [companies, setCompanies] = useState([]);

    useEffect(() => {
        frontendApi
            .get("/frontend/trusted-companies")
            .then((res) => {
                const data = res.data?.data || res.data;
                if (Array.isArray(data) && data.length > 0) {
                    setCompanies(data.map((c) => ({ name: c.name, logo_url: c.logo_url, url: c.url })));
                }
            })
            .catch(() => {});
    }, []);

    if (companies.length === 0) return null;

    const doubledCompanies = [...companies, ...companies];

    return (
        <section className="trusted">
            <motion.p
                initial={{ opacity: 0, y: 10 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5 }}
            >
                Trusted by teams worldwide
            </motion.p>
            <div className="trusted-marquee-wrapper">
                <div className="trusted-marquee-track">
                    {doubledCompanies.map((company, index) => (
                        <motion.div
                            className="trusted-logo-item"
                            key={`${company.name}-${index}`}
                            title={company.name}
                            whileHover={{ scale: 1.1, opacity: 1 }}
                            initial={{ opacity: 0.4 }}
                            whileInView={{ opacity: 0.75 }}
                            viewport={{ once: true }}
                            transition={{ duration: 0.4, delay: (index % companies.length) * 0.05 }}
                        >
                            <BrandLogo company={company} />
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
}

export default TrustedCompanies;
