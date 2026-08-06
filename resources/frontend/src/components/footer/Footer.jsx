import "../../theme/css/footer.css";

import { ArrowUp } from "lucide-react";

import {
    FaFacebookF,
    FaInstagram,
    FaLinkedinIn,
    FaXTwitter,
} from "react-icons/fa6";

function Footer() {
    return (
        <footer className="footer">
            <div className="container">
                <div className="footer-grid">
                    {/* Logo */}

                    <div>
                        <h2 className="footer-logo">KhanVerse</h2>

                        <p>
                            The next generation freelance marketplace powered by
                            AI.
                        </p>

                        <div className="footer-social">
                            <FaFacebookF />
                            <FaInstagram />
                            <FaLinkedinIn />
                            <FaXTwitter />
                        </div>
                    </div>

                    {/* Company */}

                    <div>
                        <h3>Company</h3>

                        <a href="#">About</a>

                        <a href="#">Careers</a>

                        <a href="#">Blog</a>

                        <a href="#">Contact</a>
                    </div>

                    {/* Marketplace */}

                    <div>
                        <h3>Marketplace</h3>

                        <a href="#">Find Services</a>

                        <a href="#">Become Seller</a>

                        <a href="#">Categories</a>

                        <a href="#">Projects</a>
                    </div>

                    {/* Support */}

                    <div>
                        <h3>Support</h3>

                        <a href="#">Help Center</a>

                        <a href="#">Privacy Policy</a>

                        <a href="#">Terms</a>

                        <a href="#">FAQs</a>
                    </div>
                </div>

                <div className="footer-bottom">
                    <p>© 2026 KhanVerse. All Rights Reserved.</p>

                    <button className="back-top">
                        <ArrowUp size={18} />
                    </button>
                </div>
            </div>
        </footer>
    );
}

export default Footer;
