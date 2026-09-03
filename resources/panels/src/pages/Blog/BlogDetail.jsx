import { Link, useParams } from "react-router-dom";
import { motion } from "framer-motion";
import { ArrowLeft, ArrowRight, Calendar, User } from "lucide-react";

import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";

import blog from "../../data/blog";

import "../../theme/css/blog.css";

const fadeUp = {
    hidden: { opacity: 0, y: 26 },
    show: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
};

const container = {
    hidden: {},
    show: { transition: { staggerChildren: 0.12 } },
};

function BlogDetail() {
    const { slug } = useParams();
    const item = blog.find((b) => b.slug === slug);

    if (!item) {
        return (
            <>
                <PageHero title="Article Not Found" subtitle="The article you are looking for does not exist." />
                <PageContainer>
                    <div style={{ textAlign: "center", padding: "48px 0" }}>
                        <Link to="/blog" className="blog-btn">
                            <ArrowLeft size={16} /> Back to Blog
                        </Link>
                    </div>
                </PageContainer>
            </>
        );
    }

    const related = blog.filter((b) => b.slug !== item.slug).slice(0, 3);
    const paragraphs = Array.isArray(item.content) && item.content.length
        ? item.content
        : [item.description];

    return (
        <>
            <PageHero
                title={item.title}
                subtitle={item.category}
            />

            <PageContainer>
                <motion.div variants={container} initial="hidden" animate="show">
                    <motion.div variants={fadeUp} style={{ marginBottom: 24 }}>
                        <Link to="/blog" className="blog-btn" style={{ marginLeft: -10 }}>
                            <ArrowLeft size={16} /> Back to Blog
                        </Link>
                    </motion.div>

                    <motion.div
                        className="blog-detail-hero"
                        variants={fadeUp}
                        style={{ borderRadius: 24, overflow: "hidden", border: "1px solid var(--border-light, #EEF2F7)" }}
                    >
                        <div style={{ height: 360, overflow: "hidden" }}>
                            <img src={item.image} alt={item.title} style={{ width: "100%", height: "100%", objectFit: "cover" }} />
                        </div>
                        <div style={{ padding: "28px 32px", background: "var(--bg-card, #fff)" }}>
                            <span className="blog-category">{item.category}</span>
                            <h1 style={{ fontSize: 32, lineHeight: 1.25, margin: "8px 0 16px", color: "var(--text-primary, #0F172A)" }}>{item.title}</h1>
                            <div style={{ display: "flex", gap: 22, flexWrap: "wrap", color: "var(--text-muted, #64748B)", fontSize: 13, fontWeight: 500 }}>
                                <span style={{ display: "inline-flex", alignItems: "center", gap: 6 }}><User size={14} /> {item.author}</span>
                                <span style={{ display: "inline-flex", alignItems: "center", gap: 6 }}><Calendar size={14} /> {item.date}</span>
                            </div>
                        </div>
                    </motion.div>

                    <motion.div
                        variants={fadeUp}
                        style={{ maxWidth: 820, margin: "40px auto 0", color: "var(--text-secondary, #334155)", lineHeight: 1.85, fontSize: 16 }}
                    >
                        {paragraphs.map((p, i) => (
                            <p key={i} style={{ marginBottom: 20 }}>{p}</p>
                        ))}
                    </motion.div>

                    {related.length > 0 && (
                        <motion.div variants={fadeUp} style={{ marginTop: 56 }}>
                            <h2 style={{ fontSize: 22, marginBottom: 20, color: "var(--text-primary, #0F172A)" }}>Related Articles</h2>
                            <div className="blog-grid">
                                {related.map((r) => (
                                    <motion.div className="blog-card" key={r.id} whileHover={{ y: -8, transition: { duration: 0.3 } }}>
                                        <div className="blog-image">
                                            <img src={r.image} alt={r.title} />
                                        </div>
                                        <div className="blog-content">
                                            <span className="blog-category">{r.category}</span>
                                            <h3>{r.title}</h3>
                                            <p>{r.description}</p>
                                            <Link to={`/blog/${r.slug}`} className="blog-btn">
                                                Read More <ArrowRight size={16} />
                                            </Link>
                                        </div>
                                    </motion.div>
                                ))}
                            </div>
                        </motion.div>
                    )}
                </motion.div>
            </PageContainer>
        </>
    );
}

export default BlogDetail;
