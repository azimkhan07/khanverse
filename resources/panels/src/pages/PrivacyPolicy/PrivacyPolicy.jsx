import { motion } from "framer-motion";
import {
    Shield,
    Database,
    Eye,
    Share2,
    Cookie,
    Lock,
    UserCheck,
    Mail,
} from "lucide-react";
import PageHero from "../../components/common/PageHero";
import PageContainer from "../../components/common/PageContainer";
import SectionHeading from "../../components/common/SectionHeading";
import policies from "../../data/policies";
import "../../theme/css/policies.css";

const fadeInUp = {
    initial: { opacity: 0, y: 30 },
    whileInView: { opacity: 1, y: 0 },
    viewport: { once: true, margin: "-40px" },
    transition: { duration: 0.5 },
};

const sections = [
    {
        icon: Database,
        title: "Information We Collect",
        content:
            "We collect information that you provide directly and information collected automatically when you use our platform.",
        items: [
            "Account details such as name, email address and profile information",
            "Payment and billing information processed securely through our payment providers",
            "Project-related data including messages, files and deliverables exchanged on the platform",
            "Device and browser information such as IP address, browser type and operating system",
            "Usage data including pages visited, features used and interaction patterns on the platform",
        ],
    },
    {
        icon: Eye,
        title: "How We Use Your Information",
        content:
            "We use the information we collect to operate, improve and personalize your experience on KhanVerse.",
        items: [
            "To provide, maintain and improve our marketplace services and features",
            "To process transactions and send related information including invoices and confirmations",
            "To communicate with you about updates, security alerts and support messages",
            "To detect, prevent and address fraud, abuse and technical issues on the platform",
            "To personalize your experience and deliver content relevant to your interests and skills",
            "To comply with legal obligations and enforce our terms of service",
        ],
    },
    {
        icon: Share2,
        title: "Information Sharing",
        content:
            "We do not sell your personal information. We may share your data in the following limited circumstances.",
        items: [
            "With other users as needed to facilitate projects (e.g. profile information visible to potential clients)",
            "With trusted service providers who assist in operating our platform under strict data protection agreements",
            "When required by law, legal process or to protect the rights and safety of KhanVerse and its users",
            "In connection with a merger, acquisition or sale of assets, with prior notice to users",
        ],
    },
    {
        icon: Lock,
        title: "Data Security",
        content:
            "We implement industry-standard security measures to protect your personal information.",
        items: [
            "All data is encrypted in transit using TLS/SSL and at rest using AES-256 encryption",
            "Regular security audits and vulnerability assessments are conducted on our infrastructure",
            "Access to personal data is restricted to authorized personnel on a need-to-know basis",
            "We maintain SOC 2 compliance and follow OWASP security best practices",
            "Automated monitoring systems detect and respond to suspicious activity in real time",
        ],
    },
    {
        icon: Cookie,
        title: "Cookies & Tracking",
        content:
            "We use cookies and similar technologies to improve your browsing experience and analyze platform usage.",
        items: [
            "Essential cookies required for authentication, security and core platform functionality",
            "Analytics cookies to understand how visitors interact with our platform and improve performance",
            "Preference cookies to remember your settings, language and display preferences",
            "You can manage cookie preferences through your browser settings at any time",
        ],
    },
    {
        icon: UserCheck,
        title: "Your Rights",
        content:
            "You have control over your personal data. We respect your rights under applicable data protection laws.",
        items: [
            "Access — Request a copy of the personal data we hold about you",
            "Correction — Request correction of inaccurate or incomplete personal data",
            "Deletion — Request deletion of your personal data subject to legal retention requirements",
            "Portability — Request transfer of your data in a structured, machine-readable format",
            "Objection — Object to processing of your personal data for specific purposes",
            "Restriction — Request restriction of processing in certain circumstances",
        ],
    },
    {
        icon: Database,
        title: "Data Retention",
        content:
            "We retain your personal information only for as long as necessary to fulfill the purposes described in this policy.",
        items: [
            "Account data is retained for the duration of your active account and up to 30 days after deletion",
            "Project data is retained for 12 months after project completion for dispute resolution purposes",
            "Financial records are retained for 7 years as required by tax and accounting regulations",
            "Analytics data is aggregated and anonymized after 24 months",
        ],
    },
    {
        icon: Shield,
        title: "Children's Privacy",
        content:
            "KhanVerse is not intended for users under the age of 18. We do not knowingly collect personal information from children. If we become aware that a child has provided us with personal data, we will take steps to delete such information promptly.",
        items: [],
    },
];

function PrivacyPolicy() {
    return (
        <>
            <PageHero
                title="Privacy Policy"
                subtitle="Your privacy and data security are important to us."
            />

            <PageContainer>
                <SectionHeading
                    tag="PRIVACY"
                    title={policies.privacy.title}
                    subtitle="Please read our privacy policy carefully to understand how we collect, use and protect your data."
                />

                <div className="policy-content">
                    <motion.p {...fadeInUp}>
                        {policies.privacy.description}
                    </motion.p>

                    <motion.p {...fadeInUp} style={{ marginTop: "12px" }}>
                        At KhanVerse, we are committed to protecting your privacy and
                        ensuring the security of your personal information. This Privacy
                        Policy explains how we collect, use, disclose and safeguard your
                        data when you use our platform and services. By using KhanVerse,
                        you agree to the collection and use of information in accordance
                        with this policy.
                    </motion.p>

                    <div className="policy-sections">
                        {sections.map((section, index) => {
                            const Icon = section.icon;
                            return (
                                <motion.div
                                    className="policy-section-card"
                                    key={index}
                                    initial={{ opacity: 0, y: 30 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, margin: "-40px" }}
                                    transition={{ duration: 0.4, delay: index * 0.05 }}
                                >
                                    <div className="policy-section-header">
                                        <div className="policy-section-icon">
                                            <Icon size={22} />
                                        </div>
                                        <h3>{section.title}</h3>
                                    </div>

                                    <p>{section.content}</p>

                                    {section.items.length > 0 && (
                                        <ul>
                                            {section.items.map((item, i) => (
                                                <li key={i}>{item}</li>
                                            ))}
                                        </ul>
                                    )}
                                </motion.div>
                            );
                        })}
                    </div>

                    <div className="policy-footer-note">
                        <p>
                            <strong>Last Updated:</strong> September 1, 2026
                        </p>
                        <p>
                            If you have any questions about this Privacy Policy or our data
                            practices, please contact us at{" "}
                            <strong>privacy@khanverse.com</strong>.
                        </p>
                    </div>
                </div>
            </PageContainer>
        </>
    );
}

export default PrivacyPolicy;
