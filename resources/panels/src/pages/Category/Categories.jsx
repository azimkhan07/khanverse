import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import "../../theme/css/categories.css";
import { categories as staticCategories } from "../../components/category/categoriesData";
import frontendApi from "../../shared/frontendApi";
import { Code2, Palette, Megaphone, BrainCircuit, Video, Smartphone, PenTool, Search, FileText, BarChart3, Server, HelpCircle, ArrowRight } from "lucide-react";

const iconMap = {
    "web-development": Code2,
    "graphic-design": Palette,
    "digital-marketing": Megaphone,
    "ai-services": BrainCircuit,
    "video-editing": Video,
    "app-development": Smartphone,
    "mobile-app-development": Smartphone,
    "ui-ux-design": PenTool,
    "content-writing": FileText,
    "seo": Search,
    "data-science": BarChart3,
    "devops": Server,
};

const containerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.08,
        },
    },
};

const cardVariants = {
    hidden: { opacity: 0, y: 40, scale: 0.95 },
    visible: {
        opacity: 1,
        y: 0,
        scale: 1,
        transition: { duration: 0.45, ease: [0.22, 1, 0.36, 1] },
    },
};

function Categories() {
    const [categories, setCategories] = useState([]);

    useEffect(() => {
        frontendApi.get("/frontend/categories")
            .then((res) => {
                const data = Array.isArray(res.data) ? res.data : [];
                if (data.length > 0) {
                    setCategories(data.map((cat) => ({
                        id: cat.id,
                        title: cat.name,
                        services: `${cat.services_count || 0} Services`,
                        icon: iconMap[cat.slug] || HelpCircle,
                        slug: cat.slug,
                    })));
                } else {
                    setCategories(staticCategories);
                }
            })
            .catch(() => setCategories(staticCategories));
    }, []);

    if (categories.length === 0) return null;

    return (
        <section className="categories">
            <motion.div
                className="section-heading"
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: "-60px" }}
                transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
            >
                <span>Browse Services</span>
                <h2>Explore Popular Categories</h2>
                <p>Discover skilled professionals from every category.</p>
            </motion.div>

            <motion.div
                className="categories-grid"
                variants={containerVariants}
                initial="hidden"
                whileInView="visible"
                viewport={{ once: true, margin: "-60px" }}
            >
                {categories.map((item) => {
                    const Icon = item.icon;
                    return (
                        <motion.div
                            className="category-card"
                            key={item.id}
                            variants={cardVariants}
                            whileHover={{ y: -8, scale: 1.03, transition: { duration: 0.3 } }}
                        >
                            <Link to={`/category/${item.slug}`} className="category-card-link">
                                <motion.div
                                    className="category-icon"
                                    whileHover={{ scale: 1.15, rotate: 5 }}
                                    transition={{ type: "spring", stiffness: 300, damping: 15 }}
                                >
                                    <Icon size={38} />
                                </motion.div>
                                <h3>{item.title}</h3>
                                <p>{item.services}</p>
                                <span className="category-explore">
                                    Explore <ArrowRight size={13} />
                                </span>
                            </Link>
                        </motion.div>
                    );
                })}
            </motion.div>
        </section>
    );
}

export default Categories;
