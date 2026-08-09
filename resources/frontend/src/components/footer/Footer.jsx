import "../../theme/css/footer.css";

import { ArrowUp, Link } from "lucide-react";

import {
    FaFacebookF,
    FaInstagram,
    FaLinkedinIn,
    FaXTwitter,
} from "react-icons/fa6";
import footer from "../../data/footer";

const apiFooter = {};

const footerData = Object.keys(apiFooter).length ? apiFooter : footer;

function Footer() {
    return (
        <footer className="footer">
            <div className="container">
                <div className="footer-grid">
                    {/* Logo */}

                    <div>
                        <h2 className="footer-logo">
                            {" "}
                            {footerData.company.logo}{" "}
                        </h2>

                        <p> {footerData.company.description} </p>

                        <div className="footer-social">
                            {footerData.company.social.map((social) => (
                                <a href={social.url} key={social.id}>
                                    {social.icon === "facebook" && (
                                        <FaFacebookF />
                                    )}

                                    {social.icon === "instagram" && (
                                        <FaInstagram />
                                    )}

                                    {social.icon === "linkedin" && (
                                        <FaLinkedinIn />
                                    )}

                                    {social.icon === "twitter" && (
                                        <FaXTwitter />
                                    )}
                                </a>
                            ))}
                        </div>
                    </div>

                    {footerData.sections.map((section) => (
                        <div key={section.id}>
                            <h3>{section.title}</h3>

                            {section.links.map((link, index) => (
                                <Link key={index} to={link.url}>
                                    {link.label}
                                </Link>
                            ))}
                        </div>
                    ))}
                </div>

                <div className="footer-bottom">
                    <p> {footerData.copyright} </p>

                    <button className="back-top">
                        <ArrowUp size={18} />
                    </button>
                </div>
            </div>
        </footer>
    );
}

export default Footer;
