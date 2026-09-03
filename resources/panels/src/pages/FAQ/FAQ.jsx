import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { ChevronDown, HelpCircle, MessageCircle } from "lucide-react";
import PageHero from "../../components/common/PageHero";
import SectionHeading from "../../components/common/SectionHeading";
import PageContainer from "../../components/common/PageContainer";
import faq from "../../data/faq";
import frontendApi from "../../shared/frontendApi";
import "../../theme/css/faq.css";

const categories = [
    { id: "all", label: "All Questions" },
    { id: "general", label: "General" },
    { id: "account", label: "Account & Billing" },
    { id: "freelancers", label: "Freelancers" },
    { id: "projects", label: "Projects & Payments" },
];

const categorizedFaq = [
    {
        id: 1,
        question: "How do I create an account on KhanVerse?",
        answer:
            "Click on the Register button, fill in your details including your name, email and password. You'll receive a verification email — click the link to activate your account. You can then choose to sign up as a freelancer or a buyer.",
        category: "account",
    },
    {
        id: 2,
        question: "How do freelancers get paid?",
        answer:
            "Payments are securely released after successful project completion. Once a buyer approves the delivered work, the funds are transferred to the freelancer's wallet. Freelancers can withdraw funds via bank transfer, PayPal or other supported payment methods.",
        category: "freelancers",
    },
    {
        id: 3,
        question: "Is KhanVerse secure?",
        answer:
            "Yes, we use industry-standard SSL encryption, secure authentication with two-factor verification and protected payment gateways. All transactions are escrow-protected to ensure both buyers and freelancers are safe.",
        category: "general",
    },
    {
        id: 4,
        question: "Can I hire international freelancers?",
        answer:
            "Absolutely. KhanVerse connects businesses with talented freelancers from over 120 countries. Our platform supports multiple currencies and time zones, making global collaboration seamless.",
        category: "projects",
    },
    {
        id: 5,
        question: "How do I contact support?",
        answer:
            "You can reach our support team through the Contact page, by emailing support@khanverse.com, or via the in-app chat. Our team is available Monday to Friday from 9 AM to 6 PM IST, with limited Saturday support.",
        category: "general",
    },
    {
        id: 6,
        question: "What fees does KhanVerse charge?",
        answer:
            "KhanVerse charges a small service fee on each completed transaction. Freelancers pay a 10% platform fee on earnings. There are no upfront costs for buyers. Check our Pricing page for detailed plan information.",
        category: "account",
    },
    {
        id: 7,
        question: "How does the escrow payment system work?",
        answer:
            "When a buyer hires a freelancer, the project funds are placed in escrow. The freelancer then delivers the work. Once the buyer approves the delivery, the funds are released to the freelancer. If there's a dispute, our mediation team steps in to resolve it fairly.",
        category: "projects",
    },
    {
        id: 8,
        question: "Can I upgrade or downgrade my plan?",
        answer:
            "Yes, you can change your subscription plan at any time from your account settings. Upgrades take effect immediately with prorated billing, while downgrades apply at the end of your current billing cycle.",
        category: "account",
    },
    {
        id: 9,
        question: "What skills can I offer as a freelancer?",
        answer:
            "KhanVerse supports a wide range of categories including web development, mobile app development, UI/UX design, writing, digital marketing, video editing, AI/ML, and many more. You can list multiple services under your profile.",
        category: "freelancers",
    },
    {
        id: 10,
        question: "How are disputes resolved?",
        answer:
            "If a disagreement arises, either party can open a dispute. Our resolution team reviews the project details, communication history and deliverables. Most disputes are resolved within 48 hours with a fair outcome for both parties.",
        category: "projects",
    },
];

function FAQ() {
    const [faqData, setFaqData] = useState([]);
    const [openId, setOpenId] = useState(null);
    const [activeCategory, setActiveCategory] = useState("all");

    useEffect(() => {
        frontendApi
            .get("/frontend/faqs")
            .then((res) => {
                const data = Array.isArray(res.data) ? res.data : [];
                if (data.length > 0) {
                    setFaqData(
                        data.map((f) => ({
                            id: f.id,
                            question: f.question,
                            answer: f.answer,
                            category: f.category || "general",
                        }))
                    );
                } else {
                    setFaqData(categorizedFaq);
                }
            })
            .catch(() => setFaqData(categorizedFaq));
    }, []);

    const toggleFAQ = (id) => {
        setOpenId(openId === id ? null : id);
    };

    const filteredFaqs =
        activeCategory === "all"
            ? faqData
            : faqData.filter((item) => item.category === activeCategory);

    return (
        <>
            <PageHero
                title="Frequently Asked Questions"
                subtitle="Find answers to the most common questions about KhanVerse."
            />

            <section className="faq-section">
                <div className="container">
                    <SectionHeading
                        tag="FAQ"
                        title="Need Help?"
                        subtitle="Browse through our frequently asked questions below."
                    />

                    <div className="faq-categories">
                        {categories.map((cat) => (
                            <button
                                key={cat.id}
                                className={`faq-category-btn ${activeCategory === cat.id ? "active" : ""}`}
                                onClick={() => {
                                    setActiveCategory(cat.id);
                                    setOpenId(null);
                                }}
                            >
                                {cat.label}
                            </button>
                        ))}
                    </div>

                    <div className="faq-wrapper">
                        {filteredFaqs.map((item) => (
                            <motion.div
                                className={`faq-card ${openId === item.id ? "faq-open" : ""}`}
                                key={item.id}
                                initial={{ opacity: 0, y: 20 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, margin: "-40px" }}
                                transition={{ duration: 0.4 }}
                            >
                                <button
                                    className="faq-question"
                                    onClick={() => toggleFAQ(item.id)}
                                >
                                    <div className="faq-question-left">
                                        <HelpCircle size={20} />
                                        <h3>{item.question}</h3>
                                    </div>
                                    <motion.span
                                        className="faq-chevron"
                                        animate={{ rotate: openId === item.id ? 180 : 0 }}
                                        transition={{ duration: 0.3 }}
                                    >
                                        <ChevronDown size={20} />
                                    </motion.span>
                                </button>

                                <AnimatePresence>
                                    {openId === item.id && (
                                        <motion.div
                                            className="faq-answer"
                                            initial={{ height: 0, opacity: 0 }}
                                            animate={{ height: "auto", opacity: 1 }}
                                            exit={{ height: 0, opacity: 0 }}
                                            transition={{ duration: 0.3, ease: "easeInOut" }}
                                        >
                                            <p>{item.answer}</p>
                                        </motion.div>
                                    )}
                                </AnimatePresence>
                            </motion.div>
                        ))}
                    </div>

                    {filteredFaqs.length === 0 && (
                        <div className="faq-empty">
                            <MessageCircle size={48} />
                            <h3>No questions found</h3>
                            <p>Try selecting a different category or contact our support team.</p>
                        </div>
                    )}
                </div>
            </section>

            <section className="faq-cta-section">
                <div className="container">
                    <motion.div
                        className="faq-cta-box"
                        initial={{ opacity: 0, y: 30 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5 }}
                    >
                        <span className="section-tag">STILL HAVE QUESTIONS?</span>
                        <h2>Can't Find What You're Looking For?</h2>
                        <p>
                            Our support team is here to help. Reach out to us and we'll get
                            back to you as soon as possible.
                        </p>
                        <a href="/contact" className="faq-cta-btn">
                            Contact Support
                        </a>
                    </motion.div>
                </div>
            </section>
        </>
    );
}

export default FAQ;
