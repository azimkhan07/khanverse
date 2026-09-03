import { useState } from "react";
import { motion } from "framer-motion";
import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";
import contact from "../../data/contact";
import frontendApi from "../../shared/frontendApi";
import "../../theme/css/contact.css";

function Contact() {
    const [form, setForm] = useState({ name: "", email: "", subject: "", message: "" });
    const [loading, setLoading] = useState(false);
    const [status, setStatus] = useState(null);
    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
        if (errors[name]) setErrors((prev) => ({ ...prev, [name]: "" }));
        if (status) setStatus(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});
        setStatus(null);

        try {
            const res = await frontendApi.post("/contact", form);
            if (res.data.status) {
                setStatus("success");
                setForm({ name: "", email: "", subject: "", message: "" });
            }
        } catch (err) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors || {});
            } else {
                setStatus("error");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            <PageHero title="Contact Us" subtitle="We'd love to hear from you. Get in touch with our team." />

            <PageContainer>
                <SectionHeading tag="CONTACT" title="Let's Start A Conversation" subtitle="Feel free to reach out anytime. Our team is always ready to help." />

                <div className="contact-wrapper">
                    <motion.div
                        className="contact-info"
                        initial="hidden"
                        whileInView="visible"
                        viewport={{ once: true, margin: "-60px" }}
                        variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.12 } } }}
                    >
                        {[
                            { h: "Email", p: contact.email },
                            { h: "Phone", p: contact.phone },
                            { h: "Address", p: contact.address },
                        ].map((c, i) => (
                            <motion.div
                                className="contact-card"
                                key={c.h}
                                variants={{
                                    hidden: { opacity: 0, y: 30 },
                                    visible: { opacity: 1, y: 0, transition: { duration: 0.5, ease: [0.22, 1, 0.36, 1] } },
                                }}
                                whileHover={{ y: -6, transition: { duration: 0.3 } }}
                            >
                                <h3>{c.h}</h3>
                                <p>{c.p}</p>
                            </motion.div>
                        ))}
                    </motion.div>

                    <motion.div
                        className="contact-form"
                        initial={{ opacity: 0, y: 40, filter: "blur(8px)" }}
                        whileInView={{ opacity: 1, y: 0, filter: "blur(0px)" }}
                        viewport={{ once: true, margin: "-60px" }}
                        transition={{ duration: 0.7, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
                    >
                        {status === "success" && <div className="auth-success">Your message has been sent successfully!</div>}
                        {status === "error" && <div className="auth-error">Failed to send message. Please try again.</div>}

                        <form onSubmit={handleSubmit}>
                            <input
                                type="text"
                                name="name"
                                placeholder="Your Name"
                                value={form.name}
                                onChange={handleChange}
                                required
                            />
                            {errors.name && <span className="field-error">{errors.name[0]}</span>}

                            <input
                                type="email"
                                name="email"
                                placeholder="Email Address"
                                value={form.email}
                                onChange={handleChange}
                                required
                            />
                            {errors.email && <span className="field-error">{errors.email[0]}</span>}

                            <input
                                type="text"
                                name="subject"
                                placeholder="Subject"
                                value={form.subject}
                                onChange={handleChange}
                                required
                            />
                            {errors.subject && <span className="field-error">{errors.subject[0]}</span>}

                            <textarea
                                name="message"
                                rows="6"
                                placeholder="Your Message"
                                value={form.message}
                                onChange={handleChange}
                                required
                            ></textarea>
                            {errors.message && <span className="field-error">{errors.message[0]}</span>}

                            <button type="submit" disabled={loading}>
                                {loading ? "Sending..." : "Send Message"}
                            </button>
                        </form>
                    </motion.div>
                </div>
            </PageContainer>
        </>
    );
}

export default Contact;
