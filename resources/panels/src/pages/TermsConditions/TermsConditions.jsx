import { motion } from "framer-motion";
import {
    FileText,
    UserCheck,
    CreditCard,
    Ban,
    Scale,
    AlertTriangle,
    RefreshCw,
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
        icon: FileText,
        title: "Acceptance of Terms",
        content:
            "By accessing or using KhanVerse, you agree to be bound by these Terms & Conditions. If you do not agree to these terms, please do not use our platform.",
        items: [
            "These terms constitute a legally binding agreement between you and KhanVerse",
            "We reserve the right to modify these terms at any time with reasonable notice",
            "Continued use of the platform after changes constitutes acceptance of updated terms",
            "You must be at least 18 years of age to create an account and use our services",
        ],
    },
    {
        icon: UserCheck,
        title: "User Responsibilities",
        content:
            "Users are responsible for their activity on the platform and must maintain the security and accuracy of their account information.",
        items: [
            "Maintain the confidentiality of your account credentials and enable two-factor authentication",
            "Provide accurate, current and complete information during registration and profile setup",
            "Immediately notify KhanVerse of any unauthorized use of your account",
            "You are solely responsible for all activity that occurs under your account",
            "Keep your profile, portfolio and credentials up to date for accurate matching",
            "Comply with all applicable local, national and international laws and regulations",
        ],
    },
    {
        icon: CreditCard,
        title: "Payments & Fees",
        content:
            "All financial transactions on KhanVerse are processed through our secure payment system. Understanding our fee structure is important for all users.",
        items: [
            "Freelancers are charged a 10% platform service fee on all earnings from completed projects",
            "Buyers may be charged a small processing fee on payments made through the platform",
            "All prices are displayed in USD unless otherwise specified; currency conversion may apply",
            "Escrow payments are held securely until project deliverables are approved by the buyer",
            "Withdrawals are processed within 3-5 business days depending on the chosen payment method",
            "KhanVerse reserves the right to adjust fees with 30 days advance notice to users",
        ],
    },
    {
        icon: Ban,
        title: "Prohibited Activities",
        content:
            "To maintain a safe and trusted marketplace, the following activities are strictly prohibited on KhanVerse.",
        items: [
            "Fraud, identity misrepresentation or creating fake accounts to deceive other users",
            "Spam, unsolicited messages or mass automated communication to other users",
            "Copyright infringement, plagiarism or unauthorized use of intellectual property",
            "Sharing or distributing malware, viruses or any harmful code through the platform",
            "Circumventing platform fees by conducting transactions outside of KhanVerse",
            "Harassment, hate speech, discrimination or threatening behavior toward other users",
            "Attempting to gain unauthorized access to other accounts, systems or data",
            "Manipulating reviews, ratings or project outcomes through artificial means",
        ],
    },
    {
        icon: Scale,
        title: "Dispute Resolution",
        content:
            "We aim to resolve disputes fairly and efficiently. Our resolution process is designed to protect both buyers and freelancers.",
        items: [
            "Disputes should first be attempted to be resolved directly between the parties involved",
            "If direct resolution fails, either party may escalate the dispute through our Resolution Center",
            "KhanVerse will review all evidence including project details, communications and deliverables",
            "Most disputes are resolved within 48 hours with a binding decision from our resolution team",
            "Refunds may be issued in full or partial amounts depending on the circumstances",
            "Repeated disputes or abuse of the resolution system may result in account restrictions",
        ],
    },
    {
        icon: AlertTriangle,
        title: "Intellectual Property",
        content:
            "Ownership and rights related to work created through the platform are governed by the following terms.",
        items: [
            "Freelancers retain ownership of their pre-existing intellectual property and portfolio",
            "Upon full payment, buyers receive full ownership rights to project deliverables",
            "Freelancers may showcase completed work in their portfolio unless an NDA is in place",
            "KhanVerse retains the right to use platform content for marketing and promotional purposes",
            "All platform code, design and content is the intellectual property of KhanVerse",
        ],
    },
    {
        icon: RefreshCw,
        title: "Refund Policy",
        content:
            "We want you to be satisfied with every transaction on KhanVerse. Our refund policy is as follows.",
        items: [
            "Full refunds are issued if no work has been started or delivered on a project",
            "Partial refunds may be granted based on the amount of work completed and quality",
            "Refund requests must be submitted within 14 days of the project delivery date",
            "Processing fees are non-refundable as they are charged by third-party payment processors",
            "Refunds are processed back to the original payment method within 7-10 business days",
            "Abuse of the refund policy may result in account suspension or permanent ban",
        ],
    },
    {
        icon: FileText,
        title: "Limitation of Liability",
        content:
            "KhanVerse provides the platform on an 'as is' basis. We strive for reliability but cannot guarantee uninterrupted service.",
        items: [
            "KhanVerse is not liable for indirect, incidental or consequential damages arising from platform use",
            "Our total liability to any user shall not exceed the fees paid by that user in the past 12 months",
            "We do not guarantee specific outcomes, earnings or results from using our platform",
            "Users are responsible for ensuring their use of the platform complies with local laws",
        ],
    },
];

function TermsConditions() {
    return (
        <>
            <PageHero
                title="Terms & Conditions"
                subtitle="Please read these terms before using KhanVerse."
            />

            <PageContainer>
                <SectionHeading
                    tag="TERMS"
                    title={policies.terms.title}
                    subtitle="These terms govern your use of our platform, services and all interactions between users."
                />

                <div className="policy-content">
                    <motion.p {...fadeInUp}>
                        {policies.terms.description}
                    </motion.p>

                    <motion.p {...fadeInUp} style={{ marginTop: "12px" }}>
                        Welcome to KhanVerse. These Terms & Conditions outline the rules
                        and regulations for the use of our platform. By using KhanVerse,
                        you accept these terms in full. Please read them carefully before
                        creating an account or using any of our services. These terms apply
                        to all users including freelancers, buyers and visitors.
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
                            If you have any questions about these Terms & Conditions, please
                            contact us at <strong>legal@khanverse.com</strong>.
                        </p>
                    </div>
                </div>
            </PageContainer>
        </>
    );
}

export default TermsConditions;
