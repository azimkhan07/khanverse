import { NavLink } from "react-router-dom";
import "../../theme/css/navbar.css";

function Navbar() {
    return (
        <header className="navbar">

            <div className="container">

                <div className="logo">
                    KhanVerse
                </div>

                <nav className="nav-menu">
                    <NavLink to="/">Home</NavLink>
                    <NavLink to="/">Explore</NavLink>
                    <NavLink to="/">Categories</NavLink>
                    <NavLink to="/">Pricing</NavLink>
                    <NavLink to="/">Contact</NavLink>
                </nav>

                <div className="nav-actions">

                    <button className="btn-login">
                        Login
                    </button>

                    <button className="btn-register">
                        Register
                    </button>
                </div>

            </div>

        </header>
    );
}

export default Navbar;
