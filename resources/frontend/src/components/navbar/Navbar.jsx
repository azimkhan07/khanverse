import { NavLink } from "react-router-dom";
import navigation from "../../data/navigation";

import "../../theme/css/navbar.css";

function Navbar() {

    // Future API Data
    const apiNavigation = [];

    // Fallback
    const menus = apiNavigation?.length
        ? apiNavigation
        : navigation;

    return (

        <header className="navbar">

            <div className="container">

                <div className="logo">

                    KhanVerse

                </div>

                <nav className="nav-menu">

                    {

                        menus
                            .filter(menu => menu.visible)
                            .map((menu) => (

                                <NavLink
                                    key={menu.id}
                                    to={menu.url}
                                >

                                    {menu.title}

                                </NavLink>

                            ))

                    }

                </nav>

                <div className="nav-actions">

                    <NavLink
                        to="/login"
                        className="btn-login"
                    >

                        Login

                    </NavLink>

                    <NavLink
                        to="/register"
                        className="btn-register"
                    >

                        Register

                    </NavLink>

                </div>

            </div>

        </header>

    );

}

export default Navbar;
